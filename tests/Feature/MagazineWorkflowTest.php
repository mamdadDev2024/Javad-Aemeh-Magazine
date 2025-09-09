<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Magazine;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Category;
use App\Models\View;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class MagazineWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        Role::create(['name' => 'user']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'writer']);
        Role::create(['name' => 'super admin']);
        
        Storage::fake('public');
    }

    /** @test */
    public function complete_magazine_creation_and_interaction_workflow()
    {
        // Step 1: Create users with different roles
        $writer = User::factory()->create(['username' => 'writer_user']);
        $reader1 = User::factory()->create(['username' => 'reader1']);
        $reader2 = User::factory()->create(['username' => 'reader2']);
        $admin = User::factory()->create(['username' => 'admin_user']);
        
        $writer->assignRole('writer');
        $reader1->assignRole('user');
        $reader2->assignRole('user');
        $admin->assignRole('admin');

        // Step 2: Create categories
        $techCategory = Category::create(['name' => 'Technology']);
        $scienceCategory = Category::create(['name' => 'Science']);

        // Step 3: Writer creates a magazine with articles
        $imageFile = UploadedFile::fake()->image('magazine.jpg');
        $pdfFile = UploadedFile::fake()->create('magazine.pdf', 1000, 'application/pdf');
        $articlePdf = UploadedFile::fake()->create('article.pdf', 500, 'application/pdf');

        $response = $this->actingAs($writer)->post(route('writer.magazine.store'), [
            'title' => 'Advanced Programming Magazine',
            'desc' => 'A comprehensive magazine about advanced programming techniques',
            'image' => $imageFile,
            'addOn' => $pdfFile,
            'category' => [$techCategory->id, $scienceCategory->id],
            'articles' => [
                [
                    'title' => 'Laravel Best Practices',
                    'author' => 'John Doe',
                    'abstract' => 'Learn the best practices for Laravel development',
                    'body' => 'This article covers comprehensive Laravel best practices including...',
                    'addOn' => $articlePdf
                ],
                [
                    'title' => 'Modern JavaScript Techniques',
                    'author' => 'Jane Smith',
                    'abstract' => 'Explore modern JavaScript development techniques',
                    'body' => 'Modern JavaScript has evolved significantly with ES6+ features...'
                ]
            ]
        ]);

        $response->assertRedirect(route('home'));

        // Verify magazine was created
        $magazine = Magazine::where('title', 'Advanced Programming Magazine')->first();
        $this->assertNotNull($magazine);
        $this->assertEquals($writer->id, $magazine->user_id);
        $this->assertEquals(2, $magazine->categories()->count());
        $this->assertEquals(2, $magazine->articles()->count());

        // Step 4: Readers view the magazine (tracking views)
        $magazineShowResponse1 = $this->actingAs($reader1)->get(route('magazine.show', $magazine->slug));
        $magazineShowResponse1->assertStatus(200);

        $magazineShowResponse2 = $this->actingAs($reader2)->get(route('magazine.show', $magazine->slug));
        $magazineShowResponse2->assertStatus(200);

        // Verify views were tracked
        $this->assertEquals(2, $magazine->views()->count());

        // Step 5: Readers like the magazine
        $this->actingAs($reader1)->post(route('like.toggle', [
            'type' => 'Magazine',
            'id' => $magazine->id
        ]));

        $this->actingAs($reader2)->post(route('like.toggle', [
            'type' => 'Magazine',
            'id' => $magazine->id
        ]));

        // Verify likes
        $this->assertEquals(2, $magazine->likers()->count());
        $this->assertTrue($reader1->hasLiked($magazine));
        $this->assertTrue($reader2->hasLiked($magazine));

        // Step 6: Readers comment on the magazine
        $this->actingAs($reader1)->post(route('comment.store', [
            'model' => 'Magazine',
            'contentId' => $magazine->id
        ]), [
            'body' => 'Excellent magazine! Very informative content.'
        ]);

        $this->actingAs($reader2)->post(route('comment.store', [
            'model' => 'Magazine',
            'contentId' => $magazine->id
        ]), [
            'body' => 'Great articles, especially the Laravel one!'
        ]);

        // Verify comments (should be pending approval)
        $this->assertEquals(2, $magazine->comments()->count());
        $this->assertEquals(0, $magazine->comments()->where('status', true)->count());

        // Step 7: Admin approves comments
        $pendingComments = Comment::where('status', false)->get();
        foreach ($pendingComments as $comment) {
            $this->actingAs($admin)->post(route('admin.comment.approve', $comment->id));
        }

        // Verify comments are approved
        $this->assertEquals(2, $magazine->comments()->where('status', true)->count());

        // Step 8: Readers view and interact with articles
        $article = $magazine->articles()->first();
        
        $articleResponse = $this->actingAs($reader1)->get(route('article.show', $article->slug));
        $articleResponse->assertStatus(200);

        // Like the article
        $this->actingAs($reader1)->post(route('like.toggle', [
            'type' => 'article',
            'id' => $article->id
        ]));

        // Comment on the article
        $this->actingAs($reader1)->post(route('comment.store', [
            'model' => 'Article',
            'contentId' => $article->id
        ]), [
            'body' => 'This Laravel article is exactly what I needed!'
        ]);

        // Verify article interactions
        $this->assertEquals(1, $article->likers()->count());
        $this->assertEquals(1, $article->comments()->count());
        $this->assertEquals(1, $article->views()->count());

        // Step 9: Search functionality
        $searchResponse = $this->get(route('search', [
            'search' => 'Laravel',
            'type' => 'all'
        ]));

        $searchResponse->assertStatus(200);
        $results = $searchResponse->viewData('results');
        $this->assertGreaterThan(0, $results->total());

        // Step 10: Category filtering
        $techArticles = Article::whereHas('categories', function($query) use ($techCategory) {
            $query->where('categories.id', $techCategory->id);
        })->get();

        $this->assertGreaterThan(0, $techArticles->count());

        // Step 11: Writer updates the magazine
        $newImageFile = UploadedFile::fake()->image('updated_magazine.jpg');
        
        $updateResponse = $this->actingAs($writer)->post(route('writer.magazine.update'), [
            'id' => $magazine->id,
            'title' => 'Advanced Programming Magazine - Updated',
            'body' => 'Updated description with more details',
            'image' => $newImageFile,
            'category' => [$techCategory->id], // Remove science category
            'articles' => [
                [
                    'title' => 'Laravel Best Practices - Updated',
                    'author' => 'John Doe',
                    'abstract' => 'Updated abstract with more details',
                    'body' => 'Updated article content with latest practices...'
                ]
            ]
        ]);

        $updateResponse->assertRedirect(route('home'));

        // Verify updates
        $updatedMagazine = $magazine->fresh();
        $this->assertEquals('Advanced Programming Magazine - Updated', $updatedMagazine->title);
        $this->assertEquals(1, $updatedMagazine->categories()->count());
        $this->assertEquals(1, $updatedMagazine->articles()->count());

        // Step 12: Admin views statistics
        $adminPanelResponse = $this->actingAs($admin)->get(route('admin.contents'));
        $adminPanelResponse->assertStatus(200);

        // Step 13: Test magazine deletion workflow
        $this->actingAs($writer)->delete(route('writer.magazine.destroy', $magazine->id));

        // Verify cascade deletion
        $this->assertDatabaseMissing('magazines', ['id' => $magazine->id]);
        $this->assertEquals(0, Article::where('magazine_id', $magazine->id)->count());
    }

    /** @test */
    public function magazine_with_multiple_categories_and_complex_interactions()
    {
        $writer = User::factory()->create();
        $writer->assignRole('writer');

        // Create multiple categories
        $categories = [];
        $categoryNames = ['Technology', 'Science', 'Education', 'Research', 'Innovation'];
        foreach ($categoryNames as $name) {
            $categories[] = Category::create(['name' => $name]);
        }

        // Create magazine with all categories
        $magazine = Magazine::factory()->create([
            'user_id' => $writer->id,
            'title' => 'Multi-Category Magazine'
        ]);

        $magazine->categories()->attach(collect($categories)->pluck('id')->toArray());

        // Create multiple articles with different category combinations
        $article1 = Article::factory()->create([
            'magazine_id' => $magazine->id,
            'title' => 'Tech Innovation Article'
        ]);
        $article1->categories()->attach([$categories[0]->id, $categories[4]->id]); // Tech + Innovation

        $article2 = Article::factory()->create([
            'magazine_id' => $magazine->id,
            'title' => 'Science Education Article'
        ]);
        $article2->categories()->attach([$categories[1]->id, $categories[2]->id]); // Science + Education

        // Create multiple users for interactions
        $users = User::factory()->count(5)->create();
        foreach ($users as $user) {
            $user->assignRole('user');
        }

        // Complex interaction patterns
        foreach ($users as $index => $user) {
            // Each user views the magazine
            $this->actingAs($user)->get(route('magazine.show', $magazine->slug));

            // Some users like the magazine
            if ($index % 2 === 0) {
                $this->actingAs($user)->post(route('like.toggle', [
                    'type' => 'Magazine',
                    'id' => $magazine->id
                ]));
            }

            // Users comment with different patterns
            $this->actingAs($user)->post(route('comment.store', [
                'model' => 'Magazine',
                'contentId' => $magazine->id
            ]), [
                'body' => "Comment from user {$index}: This magazine covers great topics!"
            ]);

            // Users interact with articles differently
            if ($index < 3) {
                $this->actingAs($user)->get(route('article.show', $article1->slug));
                $this->actingAs($user)->post(route('like.toggle', [
                    'type' => 'article',
                    'id' => $article1->id
                ]));
            }

            if ($index >= 2) {
                $this->actingAs($user)->get(route('article.show', $article2->slug));
                $this->actingAs($user)->post(route('comment.store', [
                    'model' => 'Article',
                    'contentId' => $article2->id
                ]), [
                    'body' => "Article comment from user {$index}"
                ]);
            }
        }

        // Verify complex interaction results
        $this->assertEquals(5, $magazine->views()->count());
        $this->assertEquals(3, $magazine->likers()->count()); // Users 0, 2, 4
        $this->assertEquals(5, $magazine->comments()->count());

        $this->assertEquals(3, $article1->views()->count()); // Users 0, 1, 2
        $this->assertEquals(3, $article1->likers()->count());

        $this->assertEquals(3, $article2->views()->count()); // Users 2, 3, 4
        $this->assertEquals(3, $article2->comments()->count());

        // Test category-based filtering
        $techMagazines = Magazine::whereHas('categories', function($query) use ($categories) {
            $query->where('categories.id', $categories[0]->id); // Technology
        })->get();

        $this->assertEquals(1, $techMagazines->count());
        $this->assertTrue($techMagazines->contains($magazine));

        // Test multi-category search
        $techAndScienceMagazines = Magazine::whereHas('categories', function($query) use ($categories) {
            $query->whereIn('categories.id', [$categories[0]->id, $categories[1]->id]);
        })->get();

        $this->assertEquals(1, $techAndScienceMagazines->count());
    }

    /** @test */
    public function magazine_workflow_with_permissions_and_access_control()
    {
        $superAdmin = User::factory()->create(['username' => 'superadmin']);
        $admin = User::factory()->create(['username' => 'admin']);
        $writer = User::factory()->create(['username' => 'writer']);
        $user = User::factory()->create(['username' => 'user']);

        $superAdmin->assignRole('super admin');
        $admin->assignRole('admin');
        $writer->assignRole('writer');
        $user->assignRole('user');

        // Writer creates magazine
        $magazine = Magazine::factory()->create([
            'user_id' => $writer->id,
            'title' => 'Permission Test Magazine'
        ]);

        // Test access permissions
        $writerCanEdit = $this->actingAs($writer)->get(route('writer.magazine.edit', $magazine->id));
        $writerCanEdit->assertStatus(200);

        $adminCanView = $this->actingAs($admin)->get(route('admin.contents'));
        $adminCanView->assertStatus(200);

        $superAdminCanViewPanel = $this->actingAs($superAdmin)->get(route('admin.panel'));
        $superAdminCanViewPanel->assertStatus(200);

        // Test comment approval workflow
        $this->actingAs($user)->post(route('comment.store', [
            'model' => 'Magazine',
            'contentId' => $magazine->id
        ]), [
            'body' => 'Test comment for approval'
        ]);

        $comment = Comment::where('body', 'Test comment for approval')->first();
        $this->assertFalse($comment->status);

        // Admin approves comment
        $this->actingAs($admin)->post(route('admin.comment.approve', $comment->id));
        
        $this->assertTrue($comment->fresh()->status);

        // Test bulk comment approval
        for ($i = 1; $i <= 3; $i++) {
            $this->actingAs($user)->post(route('comment.store', [
                'model' => 'Magazine',
                'contentId' => $magazine->id
            ]), [
                'body' => "Bulk comment {$i}"
            ]);
        }

        $this->assertEquals(3, Comment::where('status', false)->count());

        $this->actingAs($admin)->post(route('admin.comments.approve.all'));

        $this->assertEquals(0, Comment::where('status', false)->count());
    }
}