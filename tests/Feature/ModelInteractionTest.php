<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Contact;
use App\Models\Event;
use App\Models\Khabar;
use App\Models\Magazine;
use App\Models\Recommend;
use App\Models\Scope;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ModelInteractionTest extends TestCase
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
    }

    public function test_complete_magazine_creation_workflow()
    {
        // Create a writer user
        $writer = User::factory()->create(['username' => 'writer_test']);
        $writer->assignRole('writer');

        // Create categories
        $techCategory = Category::create(['name' => 'Technology']);
        $scienceCategory = Category::create(['name' => 'Science']);

        // Create magazine
        $magazine = Magazine::factory()->create([
            'user_id' => $writer->id,
            'title' => 'Test Magazine',
            'slug' => 'test-magazine',
        ]);

        // Attach categories to magazine
        $magazine->categories()->attach([$techCategory->id, $scienceCategory->id]);

        // Create articles for the magazine
        $article1 = Article::factory()->create([
            'magazine_id' => $magazine->id,
            'title' => 'Laravel Article',
            'slug' => 'laravel-article',
            'author' => 'John Doe',
        ]);

        $article2 = Article::factory()->create([
            'magazine_id' => $magazine->id,
            'title' => 'PHP Article',
            'slug' => 'php-article',
            'author' => 'Jane Smith',
        ]);

        // Verify relationships
        $this->assertEquals(2, $magazine->articles()->count());
        $this->assertEquals(2, $magazine->categories()->count());
        $this->assertEquals($writer->id, $magazine->user_id);

        // Create readers
        $reader1 = User::factory()->create(['username' => 'reader1']);
        $reader2 = User::factory()->create(['username' => 'reader2']);
        $reader1->assignRole('user');
        $reader2->assignRole('user');

        // Readers like the magazine
        $reader1->like($magazine);
        $reader2->like($magazine);

        $this->assertEquals(2, $magazine->likers()->count());
        $this->assertTrue($reader1->hasLiked($magazine));
        $this->assertTrue($reader2->hasLiked($magazine));

        // Readers comment on magazine
        $comment1 = Comment::create([
            'body' => 'Great magazine!',
            'user_id' => $reader1->id,
            'commentable_id' => $magazine->id,
            'commentable_type' => Magazine::class,
            'status' => false,
        ]);

        $comment2 = Comment::create([
            'body' => 'Very informative content.',
            'user_id' => $reader2->id,
            'commentable_id' => $magazine->id,
            'commentable_type' => Magazine::class,
            'status' => false,
        ]);

        $this->assertEquals(2, $magazine->comments()->count());
        $this->assertEquals(0, $magazine->comments()->where('status', true)->count());

        // Admin approves comments
        $admin = User::factory()->create(['username' => 'admin_test']);
        $admin->assignRole('admin');

        $comment1->update(['status' => true]);
        $comment2->update(['status' => true]);

        $this->assertEquals(2, $magazine->comments()->where('status', true)->count());
    }

    public function test_news_and_events_workflow()
    {
        $writer = User::factory()->create(['username' => 'news_writer']);
        $writer->assignRole('writer');

        // Create scope for news
        $scope = Scope::create(['name' => 'Local News']);

        // Create news
        $news = Khabar::factory()->create([
            'user_id' => $writer->id,
            'scope_id' => $scope->id,
            'title' => 'Important News',
            'slug' => 'important-news',
        ]);

        // Create event
        $event = Event::factory()->create([
            'user_id' => $writer->id,
            'title' => 'Tech Conference',
            'slug' => 'tech-conference',
        ]);

        // Create categories
        $newsCategory = Category::create(['name' => 'Breaking News']);
        $eventCategory = Category::create(['name' => 'Conferences']);

        // Attach categories
        $news->categories()->attach($newsCategory->id);
        $event->categories()->attach($eventCategory->id);

        // Verify relationships
        $this->assertEquals($scope->id, $news->scope_id);
        $this->assertEquals($writer->id, $news->user_id);
        $this->assertEquals($writer->id, $event->user_id);
        $this->assertEquals(1, $news->categories()->count());
        $this->assertEquals(1, $event->categories()->count());

        // Test scope relationship
        $this->assertEquals(1, $scope->news()->count());
        $this->assertTrue($scope->news->contains($news));
    }

    public function test_user_interactions_and_permissions()
    {
        // Create users with different roles
        $superAdmin = User::factory()->create(['username' => 'superadmin']);
        $admin = User::factory()->create(['username' => 'admin']);
        $writer = User::factory()->create(['username' => 'writer']);
        $user = User::factory()->create(['username' => 'regular_user']);

        $superAdmin->assignRole('super admin');
        $admin->assignRole('admin');
        $writer->assignRole('writer');
        $user->assignRole('user');

        // Test role assignments
        $this->assertTrue($superAdmin->hasRole('super admin'));
        $this->assertTrue($admin->hasRole('admin'));
        $this->assertTrue($writer->hasRole('writer'));
        $this->assertTrue($user->hasRole('user'));

        // Test role hierarchy
        $this->assertTrue($superAdmin->hasAnyRole(['admin', 'super admin']));
        $this->assertTrue($admin->hasAnyRole(['admin', 'super admin']));
        $this->assertFalse($user->hasAnyRole(['admin', 'super admin']));

        // Create content
        $magazine = Magazine::factory()->create(['user_id' => $writer->id]);

        // User interactions
        $user->like($magazine);
        $this->assertTrue($user->hasLiked($magazine));

        // Create comment
        $comment = Comment::create([
            'body' => 'Test comment from user',
            'user_id' => $user->id,
            'commentable_id' => $magazine->id,
            'commentable_type' => Magazine::class,
            'status' => false,
        ]);

        $this->assertEquals(1, $magazine->comments()->count());
        $this->assertEquals(1, $user->comments()->count());
    }

    public function test_contact_and_recommend_functionality()
    {
        $user = User::factory()->create(['username' => 'contact_user']);
        $user->assignRole('user');

        // Test contact creation
        $contact = Contact::create([
            'body' => 'Test contact message',
            'phone' => '09123456789',
        ]);

        $this->assertDatabaseHas('contacts', [
            'body' => 'Test contact message',
            'phone' => '09123456789',
        ]);

        // Test recommend creation
        $recommend = Recommend::create([
            'title' => 'Test Recommendation',
            'slug' => 'test-recommendation',
            'pdf' => 'test.pdf',
            'word' => 'test.docx',
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('recommends', [
            'title' => 'Test Recommendation',
            'user_id' => $user->id,
        ]);

        $this->assertEquals(1, $user->recommends()->count());
    }

    public function test_category_content_relationships()
    {
        $user = User::factory()->create();
        $user->assignRole('writer');

        // Create category
        $category = Category::create(['name' => 'Technology']);

        // Create different types of content
        $magazine = Magazine::factory()->create(['user_id' => $user->id]);
        $article = Article::factory()->create(['magazine_id' => $magazine->id]);
        $event = Event::factory()->create(['user_id' => $user->id]);
        $news = Khabar::factory()->create(['user_id' => $user->id]);

        // Attach all content to category
        $category->magazines()->attach($magazine->id);
        $category->articles()->attach($article->id);
        $category->events()->attach($event->id);
        $category->news()->attach($news->id);

        // Verify relationships
        $this->assertEquals(1, $category->magazines()->count());
        $this->assertEquals(1, $category->articles()->count());
        $this->assertEquals(1, $category->events()->count());
        $this->assertEquals(1, $category->news()->count());

        // Test reverse relationships
        $this->assertTrue($magazine->categories->contains($category));
        $this->assertTrue($article->categories->contains($category));
        $this->assertTrue($event->categories->contains($category));
        $this->assertTrue($news->categories->contains($category));
    }

    public function test_like_system_across_content_types()
    {
        $user1 = User::factory()->create(['username' => 'liker1']);
        $user2 = User::factory()->create(['username' => 'liker2']);
        $writer = User::factory()->create(['username' => 'content_writer']);

        $user1->assignRole('user');
        $user2->assignRole('user');
        $writer->assignRole('writer');

        // Create different content types
        $magazine = Magazine::factory()->create(['user_id' => $writer->id]);
        $article = Article::factory()->create(['magazine_id' => $magazine->id]);
        $event = Event::factory()->create(['user_id' => $writer->id]);
        $news = Khabar::factory()->create(['user_id' => $writer->id]);

        // User1 likes all content
        $user1->like($magazine);
        $user1->like($article);
        $user1->like($event);
        $user1->like($news);

        // User2 likes some content
        $user2->like($magazine);
        $user2->like($event);

        // Verify like counts
        $this->assertEquals(2, $magazine->likers()->count());
        $this->assertEquals(1, $article->likers()->count());
        $this->assertEquals(2, $event->likers()->count());
        $this->assertEquals(1, $news->likers()->count());

        // Test unlike functionality
        $user1->unlike($magazine);
        $this->assertEquals(1, $magazine->likers()->count());
        $this->assertFalse($user1->hasLiked($magazine));
        $this->assertTrue($user2->hasLiked($magazine));
    }

    public function test_comment_system_across_content_types()
    {
        $user = User::factory()->create(['username' => 'commenter']);
        $writer = User::factory()->create(['username' => 'content_creator']);

        $user->assignRole('user');
        $writer->assignRole('writer');

        // Create content
        $magazine = Magazine::factory()->create(['user_id' => $writer->id]);
        $article = Article::factory()->create(['magazine_id' => $magazine->id]);
        $event = Event::factory()->create(['user_id' => $writer->id]);
        $news = Khabar::factory()->create(['user_id' => $writer->id]);

        // Create comments on different content types
        $magazineComment = Comment::create([
            'body' => 'Comment on magazine',
            'user_id' => $user->id,
            'commentable_id' => $magazine->id,
            'commentable_type' => Magazine::class,
        ]);

        $articleComment = Comment::create([
            'body' => 'Comment on article',
            'user_id' => $user->id,
            'commentable_id' => $article->id,
            'commentable_type' => Article::class,
        ]);

        $eventComment = Comment::create([
            'body' => 'Comment on event',
            'user_id' => $user->id,
            'commentable_id' => $event->id,
            'commentable_type' => Event::class,
        ]);

        $newsComment = Comment::create([
            'body' => 'Comment on news',
            'user_id' => $user->id,
            'commentable_id' => $news->id,
            'commentable_type' => Khabar::class,
        ]);

        // Verify comment relationships
        $this->assertEquals(1, $magazine->comments()->count());
        $this->assertEquals(1, $article->comments()->count());
        $this->assertEquals(1, $event->comments()->count());
        $this->assertEquals(1, $news->comments()->count());
        $this->assertEquals(4, $user->comments()->count());

        // Test comment polymorphic relationships
        $this->assertInstanceOf(Magazine::class, $magazineComment->commentable);
        $this->assertInstanceOf(Article::class, $articleComment->commentable);
        $this->assertInstanceOf(Event::class, $eventComment->commentable);
        $this->assertInstanceOf(Khabar::class, $newsComment->commentable);
    }

    public function test_cascade_deletions()
    {
        $user = User::factory()->create(['username' => 'test_user']);
        $user->assignRole('writer');

        // Create magazine with articles
        $magazine = Magazine::factory()->create(['user_id' => $user->id]);
        $article1 = Article::factory()->create(['magazine_id' => $magazine->id]);
        $article2 = Article::factory()->create(['magazine_id' => $magazine->id]);

        // Create comments
        $comment1 = Comment::create([
            'body' => 'Comment 1',
            'user_id' => $user->id,
            'commentable_id' => $magazine->id,
            'commentable_type' => Magazine::class,
        ]);

        $comment2 = Comment::create([
            'body' => 'Comment 2',
            'user_id' => $user->id,
            'commentable_id' => $article1->id,
            'commentable_type' => Article::class,
        ]);

        // Verify initial state
        $this->assertEquals(2, $magazine->articles()->count());
        $this->assertEquals(1, $magazine->comments()->count());
        $this->assertEquals(1, $article1->comments()->count());

        // Delete magazine - should cascade to articles
        $magazine->delete();

        $this->assertDatabaseMissing('magazines', ['id' => $magazine->id]);
        $this->assertDatabaseMissing('articles', ['id' => $article1->id]);
        $this->assertDatabaseMissing('articles', ['id' => $article2->id]);

        // Delete user - should cascade to comments
        $user->delete();

        $this->assertDatabaseMissing('comments', ['id' => $comment1->id]);
        $this->assertDatabaseMissing('comments', ['id' => $comment2->id]);
    }

    public function test_scope_and_news_relationship()
    {
        $user = User::factory()->create(['username' => 'news_writer']);
        $user->assignRole('writer');

        // Create scopes
        $localScope = Scope::create(['name' => 'Local']);
        $internationalScope = Scope::create(['name' => 'International']);

        // Create news with different scopes
        $localNews1 = Khabar::factory()->create([
            'user_id' => $user->id,
            'scope_id' => $localScope->id,
            'title' => 'Local News 1',
        ]);

        $localNews2 = Khabar::factory()->create([
            'user_id' => $user->id,
            'scope_id' => $localScope->id,
            'title' => 'Local News 2',
        ]);

        $internationalNews = Khabar::factory()->create([
            'user_id' => $user->id,
            'scope_id' => $internationalScope->id,
            'title' => 'International News',
        ]);

        // Test scope relationships
        $this->assertEquals(2, $localScope->news()->count());
        $this->assertEquals(1, $internationalScope->news()->count());

        // Test filtering by scope
        $localNewsFromDb = Khabar::where('scope_id', $localScope->id)->get();
        $this->assertEquals(2, $localNewsFromDb->count());

        // Delete scope - should cascade to news
        $localScope->delete();
        $this->assertDatabaseMissing('khabars', ['id' => $localNews1->id]);
        $this->assertDatabaseMissing('khabars', ['id' => $localNews2->id]);
        $this->assertDatabaseHas('khabars', ['id' => $internationalNews->id]);
    }

    public function test_complex_category_filtering()
    {
        $user = User::factory()->create(['username' => 'content_creator']);
        $user->assignRole('writer');

        // Create categories
        $tech = Category::create(['name' => 'Technology']);
        $ai = Category::create(['name' => 'AI']);
        $web = Category::create(['name' => 'Web Development']);

        // Create magazine and articles
        $magazine = Magazine::factory()->create(['user_id' => $user->id]);

        $article1 = Article::factory()->create([
            'magazine_id' => $magazine->id,
            'title' => 'AI in Web Development',
        ]);

        $article2 = Article::factory()->create([
            'magazine_id' => $magazine->id,
            'title' => 'Pure Technology Article',
        ]);

        // Create events and news
        $event = Event::factory()->create([
            'user_id' => $user->id,
            'title' => 'AI Conference',
        ]);

        $news = Khabar::factory()->create([
            'user_id' => $user->id,
            'title' => 'Web Technology News',
        ]);

        // Attach categories
        $article1->categories()->attach([$tech->id, $ai->id, $web->id]);
        $article2->categories()->attach([$tech->id]);
        $event->categories()->attach([$ai->id]);
        $news->categories()->attach([$tech->id, $web->id]);

        // Test category filtering
        $techContent = collect()
            ->merge($tech->articles)
            ->merge($tech->events)
            ->merge($tech->news)
            ->merge($tech->magazines);

        $this->assertGreaterThanOrEqual(3, $techContent->count());

        $aiContent = collect()
            ->merge($ai->articles)
            ->merge($ai->events);

        $this->assertEquals(2, $aiContent->count());

        // Test multi-category content
        $this->assertEquals(3, $article1->categories()->count());
        $this->assertEquals(1, $article2->categories()->count());
    }

    public function test_user_content_ownership()
    {
        $writer1 = User::factory()->create(['username' => 'writer1']);
        $writer2 = User::factory()->create(['username' => 'writer2']);

        $writer1->assignRole('writer');
        $writer2->assignRole('writer');

        // Writer1 creates content
        $magazine1 = Magazine::factory()->create(['user_id' => $writer1->id]);
        $event1 = Event::factory()->create(['user_id' => $writer1->id]);
        $news1 = Khabar::factory()->create(['user_id' => $writer1->id]);

        // Writer2 creates content
        $magazine2 = Magazine::factory()->create(['user_id' => $writer2->id]);
        $event2 = Event::factory()->create(['user_id' => $writer2->id]);
        $news2 = Khabar::factory()->create(['user_id' => $writer2->id]);

        // Test ownership
        $this->assertEquals(1, $writer1->magazines()->count());
        $this->assertEquals(1, $writer1->events()->count());
        $this->assertEquals(1, $writer1->news()->count());

        $this->assertEquals(1, $writer2->magazines()->count());
        $this->assertEquals(1, $writer2->events()->count());
        $this->assertEquals(1, $writer2->news()->count());

        // Test content belongs to correct user
        $this->assertEquals($writer1->id, $magazine1->user_id);
        $this->assertEquals($writer2->id, $magazine2->user_id);
    }

    public function test_polymorphic_relationships_integrity()
    {
        $user = User::factory()->create(['username' => 'poly_test']);
        $user->assignRole('writer');

        // Create content
        $magazine = Magazine::factory()->create(['user_id' => $user->id]);
        $article = Article::factory()->create(['magazine_id' => $magazine->id]);
        $event = Event::factory()->create(['user_id' => $user->id]);
        $news = Khabar::factory()->create(['user_id' => $user->id]);

        // Create comments for each content type
        $comments = [
            Comment::create([
                'body' => 'Magazine comment',
                'user_id' => $user->id,
                'commentable_id' => $magazine->id,
                'commentable_type' => Magazine::class,
            ]),
            Comment::create([
                'body' => 'Article comment',
                'user_id' => $user->id,
                'commentable_id' => $article->id,
                'commentable_type' => Article::class,
            ]),
            Comment::create([
                'body' => 'Event comment',
                'user_id' => $user->id,
                'commentable_id' => $event->id,
                'commentable_type' => Event::class,
            ]),
            Comment::create([
                'body' => 'News comment',
                'user_id' => $user->id,
                'commentable_id' => $news->id,
                'commentable_type' => Khabar::class,
            ]),
        ];

        // Test polymorphic relationships
        foreach ($comments as $comment) {
            $this->assertNotNull($comment->commentable);
            $this->assertEquals($user->id, $comment->user_id);
        }

        // Test that each content has its comment
        $this->assertEquals(1, $magazine->comments()->count());
        $this->assertEquals(1, $article->comments()->count());
        $this->assertEquals(1, $event->comments()->count());
        $this->assertEquals(1, $news->comments()->count());
    }
}
