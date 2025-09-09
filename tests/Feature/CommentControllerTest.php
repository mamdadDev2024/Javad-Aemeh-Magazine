<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Magazine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'user']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'super admin']);
        Role::create(['name' => 'writer']);
    }

    /** @test */
    public function it_can_create_comment_on_article()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $magazine = Magazine::factory()->create(['user_id' => $user->id]);
        $article = Article::factory()->create([
            'magazine_id' => $magazine->id,
            'title' => 'Test Article'
        ]);

        $response = $this->actingAs($user)->post(route('create.comment', [
            'model' => 'Article',
            'contentId' => $article->id
        ]), [
            'body' => 'This is a test comment',
            'g-recaptcha-response' => 'valid-captcha'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'body' => 'This is a test comment',
            'user_id' => $user->id,
            'commentable_id' => $article->id,
            'commentable_type' => Article::class
        ]);
    }

    /** @test */
    public function it_validates_comment_creation()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $article = Article::factory()->create();

        $response = $this->actingAs($user)->post(route('create.comment', [
            'model' => 'Article',
            'contentId' => $article->id
        ]), [
            'body' => '',
            'g-recaptcha-response' => 'valid-captcha'
        ]);

        $response->assertSessionHasErrors(['body']);
    }

    /** @test */
    public function it_requires_authentication_to_comment()
    {
        $article = Article::factory()->create();

        $response = $this->post(route('create.comment', [
            'model' => 'Article',
            'contentId' => $article->id
        ]), [
            'body' => 'Test comment',
            'g-recaptcha-response' => 'valid-captcha'
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('comments', [
            'body' => 'Test comment'
        ]);
    }

    /** @test */
    public function it_handles_invalid_model_type()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->post(route('create.comment', [
            'model' => 'InvalidModel',
            'contentId' => 1
        ]), [
            'body' => 'Test comment',
            'g-recaptcha-response' => 'valid-captcha'
        ]);

        $response->assertRedirect();
        // Should handle gracefully, perhaps redirect back with error
    }

    /** @test */
    public function it_handles_nonexistent_content()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->post(route('create.comment', [
            'model' => 'Article',
            'contentId' => 999
        ]), [
            'body' => 'Test comment',
            'g-recaptcha-response' => 'valid-captcha'
        ]);

        $response->assertRedirect();
        // Should handle gracefully
    }

    /** @test */
    public function comment_belongs_to_user()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $article = Article::factory()->create();
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_id' => $article->id,
            'commentable_type' => Article::class
        ]);

        $this->assertEquals($user->id, $comment->user->id);
        $this->assertTrue($user->comments()->exists());
    }

    /** @test */
    public function comment_belongs_to_commentable()
    {
        $article = Article::factory()->create();
        $comment = Comment::factory()->create([
            'commentable_id' => $article->id,
            'commentable_type' => Article::class
        ]);

        $this->assertEquals($article->id, $comment->commentable->id);
        $this->assertTrue($article->comments()->exists());
    }
}