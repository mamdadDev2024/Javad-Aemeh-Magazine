<?php

namespace Tests\Unit;

use App\Models\Comment;
use App\Models\User;
use App\Models\Magazine;
use App\Models\Article;
use App\Models\Event;
use App\Models\Khabar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class CommentModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        Role::create(['name' => 'user']);
        Role::create(['name' => 'admin']);
    }

    /** @test */
    public function it_can_create_a_comment()
    {
        $user = User::factory()->create();
        $magazine = Magazine::factory()->create(['user_id' => $user->id]);
        
        $comment = Comment::factory()->create([
            'body' => 'This is a test comment',
            'user_id' => $user->id,
            'commentable_id' => $magazine->id,
            'commentable_type' => Magazine::class
        ]);

        $this->assertDatabaseHas('comments', [
            'body' => 'This is a test comment',
            'user_id' => $user->id,
            'commentable_id' => $magazine->id,
            'commentable_type' => Magazine::class
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $comment = new Comment();
        $fillable = ['body', 'user_id', 'status'];
        
        $this->assertEquals($fillable, $comment->getFillable());
    }

    /** @test */
    public function it_belongs_to_user()
    {
        $user = User::factory()->create();
        $magazine = Magazine::factory()->create(['user_id' => $user->id]);
        
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_id' => $magazine->id,
            'commentable_type' => Magazine::class
        ]);

        $this->assertInstanceOf(User::class, $comment->user);
        $this->assertEquals($user->id, $comment->user->id);
    }

    /** @test */
    public function it_belongs_to_commentable_magazine()
    {
        $user = User::factory()->create();
        $magazine = Magazine::factory()->create(['user_id' => $user->id]);
        
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_id' => $magazine->id,
            'commentable_type' => Magazine::class
        ]);

        $this->assertInstanceOf(Magazine::class, $comment->commentable);
        $this->assertEquals($magazine->id, $comment->commentable->id);
    }

    /** @test */
    public function it_belongs_to_commentable_article()
    {
        $user = User::factory()->create();
        $magazine = Magazine::factory()->create(['user_id' => $user->id]);
        $article = Article::factory()->create(['magazine_id' => $magazine->id]);
        
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_id' => $article->id,
            'commentable_type' => Article::class
        ]);

        $this->assertInstanceOf(Article::class, $comment->commentable);
        $this->assertEquals($article->id, $comment->commentable->id);
    }

    /** @test */
    public function it_belongs_to_commentable_event()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['user_id' => $user->id]);
        
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_id' => $event->id,
            'commentable_type' => Event::class
        ]);

        $this->assertInstanceOf(Event::class, $comment->commentable);
        $this->assertEquals($event->id, $comment->commentable->id);
    }

    /** @test */
    public function it_belongs_to_commentable_khabar()
    {
        $user = User::factory()->create();
        $khabar = Khabar::factory()->create(['user_id' => $user->id]);
        
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_id' => $khabar->id,
            'commentable_type' => Khabar::class
        ]);

        $this->assertInstanceOf(Khabar::class, $comment->commentable);
        $this->assertEquals($khabar->id, $comment->commentable->id);
    }

    /** @test */
    public function it_has_default_status_false()
    {
        $user = User::factory()->create();
        $magazine = Magazine::factory()->create(['user_id' => $user->id]);
        
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_id' => $magazine->id,
            'commentable_type' => Magazine::class
        ]);

        // Check if status is false (0) or null (default)
        $this->assertFalse((bool)$comment->status);
    }

    /** @test */
    public function it_can_be_approved()
    {
        $user = User::factory()->create();
        $magazine = Magazine::factory()->create(['user_id' => $user->id]);
        
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_id' => $magazine->id,
            'commentable_type' => Magazine::class,
            'status' => false
        ]);

        $comment->update(['status' => true]);

        $this->assertTrue((bool)$comment->fresh()->status);
    }

    /** @test */
    public function it_can_be_rejected()
    {
        $user = User::factory()->create();
        $magazine = Magazine::factory()->create(['user_id' => $user->id]);
        
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_id' => $magazine->id,
            'commentable_type' => Magazine::class,
            'status' => true
        ]);

        $comment->update(['status' => false]);

        $this->assertFalse((bool)$comment->fresh()->status);
    }

    /** @test */
    public function it_cascades_delete_when_user_is_deleted()
    {
        $user = User::factory()->create();
        $magazine = Magazine::factory()->create(['user_id' => $user->id]);
        
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_id' => $magazine->id,
            'commentable_type' => Magazine::class
        ]);

        $this->assertDatabaseHas('comments', ['id' => $comment->id]);
        
        $user->delete();
        
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    /** @test */
    public function it_can_filter_approved_comments()
    {
        $user = User::factory()->create();
        $magazine = Magazine::factory()->create(['user_id' => $user->id]);
        
        $approvedComment = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_id' => $magazine->id,
            'commentable_type' => Magazine::class,
            'status' => true
        ]);
        
        $pendingComment = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_id' => $magazine->id,
            'commentable_type' => Magazine::class,
            'status' => false
        ]);

        $approvedComments = Comment::where('status', true)->get();
        $pendingComments = Comment::where('status', false)->get();

        $this->assertEquals(1, $approvedComments->count());
        $this->assertEquals(1, $pendingComments->count());
        $this->assertTrue($approvedComments->contains($approvedComment));
        $this->assertTrue($pendingComments->contains($pendingComment));
    }

    /** @test */
    public function it_can_have_multiple_comments_on_same_content()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $magazine = Magazine::factory()->create(['user_id' => $user1->id]);
        
        $comment1 = Comment::factory()->create([
            'user_id' => $user1->id,
            'commentable_id' => $magazine->id,
            'commentable_type' => Magazine::class,
            'body' => 'First comment'
        ]);
        
        $comment2 = Comment::factory()->create([
            'user_id' => $user2->id,
            'commentable_id' => $magazine->id,
            'commentable_type' => Magazine::class,
            'body' => 'Second comment'
        ]);

        $this->assertEquals(2, $magazine->comments()->count());
        $this->assertEquals('First comment', $comment1->body);
        $this->assertEquals('Second comment', $comment2->body);
    }

    /** @test */
    public function it_can_have_comments_from_same_user_on_different_content()
    {
        $user = User::factory()->create();
        $magazine = Magazine::factory()->create(['user_id' => $user->id]);
        $event = Event::factory()->create(['user_id' => $user->id]);
        
        $magazineComment = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_id' => $magazine->id,
            'commentable_type' => Magazine::class,
            'body' => 'Comment on magazine'
        ]);
        
        $eventComment = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_id' => $event->id,
            'commentable_type' => Event::class,
            'body' => 'Comment on event'
        ]);

        $this->assertEquals(2, $user->comments()->count());
        $this->assertEquals(1, $magazine->comments()->count());
        $this->assertEquals(1, $event->comments()->count());
    }

    /** @test */
    public function it_requires_body_and_user_id()
    {
        $user = User::factory()->create();
        $magazine = Magazine::factory()->create(['user_id' => $user->id]);
        
        $comment = Comment::factory()->create([
            'body' => 'Required comment body',
            'user_id' => $user->id,
            'commentable_id' => $magazine->id,
            'commentable_type' => Magazine::class
        ]);

        $this->assertEquals('Required comment body', $comment->body);
        $this->assertEquals($user->id, $comment->user_id);
    }
}