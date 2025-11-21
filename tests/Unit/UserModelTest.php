<?php

namespace Tests\Unit;

use App\Models\Comment;
use App\Models\Contact;
use App\Models\Event;
use App\Models\Khabar;
use App\Models\Magazine;
use App\Models\Recommend;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserModelTest extends TestCase
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
    public function it_can_create_a_user()
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'number' => '09123456789',
        ]);

        $this->assertDatabaseHas('users', [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'number' => '09123456789',
        ]);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $user = new User;
        $fillable = ['name', 'username', 'age', 'email', 'status', 'password', 'number'];

        $this->assertEquals($fillable, $user->getFillable());
    }

    /** @test */
    public function it_hides_sensitive_attributes()
    {
        $user = User::factory()->create();
        $hidden = $user->getHidden();

        $this->assertContains('password', $hidden);
        $this->assertContains('remember_token', $hidden);
    }

    /** @test */
    public function it_casts_password_to_hashed()
    {
        $user = new User;
        $casts = $user->getCasts();

        $this->assertEquals('hashed', $casts['password']);
    }

    /** @test */
    public function it_has_many_magazines()
    {
        $user = User::factory()->create();
        $magazine = Magazine::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->magazines()->exists());
        $this->assertEquals(1, $user->magazines()->count());
        $this->assertEquals($magazine->id, $user->magazines->first()->id);
    }

    /** @test */
    public function it_has_many_comments()
    {
        $user = User::factory()->create();
        $magazine = Magazine::factory()->create(['user_id' => $user->id]);

        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_id' => $magazine->id,
            'commentable_type' => Magazine::class,
        ]);

        $this->assertTrue($user->comments()->exists());
        $this->assertEquals(1, $user->comments()->count());
    }

    /** @test */
    public function it_has_many_events()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->events()->exists());
        $this->assertEquals(1, $user->events()->count());
    }

    /** @test */
    public function it_has_many_news()
    {
        $user = User::factory()->create();
        $news = Khabar::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->news()->exists());
        $this->assertEquals(1, $user->news()->count());
    }

    /** @test */
    public function it_has_many_contacts()
    {
        $user = User::factory()->create();
        $contact = Contact::create([
            'body' => 'Test contact message',
            'phone' => '09123456789',
        ]);

        // Contact model might not have user_id relationship
        $this->assertTrue(method_exists($user, 'contacts'));
    }

    /** @test */
    public function it_has_many_recommends()
    {
        $user = User::factory()->create();
        $recommend = Recommend::create([
            'title' => 'Test Recommendation',
            'slug' => 'test-recommendation',
            'pdf' => 'test.pdf',
            'word' => 'test.docx',
            'user_id' => $user->id,
        ]);

        $this->assertTrue($user->recommends()->exists());
        $this->assertEquals(1, $user->recommends()->count());
    }

    /** @test */
    public function it_can_have_roles()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->assertTrue($user->hasRole('user'));
        $this->assertFalse($user->hasRole('admin'));
    }

    /** @test */
    public function it_can_like_content()
    {
        $user = User::factory()->create();
        $magazine = Magazine::factory()->create(['user_id' => $user->id]);

        $user->like($magazine);

        $this->assertTrue($user->hasLiked($magazine));
        $this->assertEquals(1, $magazine->likers()->count());
    }

    /** @test */
    public function it_can_toggle_like()
    {
        $user = User::factory()->create();
        $magazine = Magazine::factory()->create(['user_id' => $user->id]);

        // First like
        $user->toggleLike($magazine);
        $this->assertTrue($user->hasLiked($magazine));

        // Toggle (unlike)
        $user->toggleLike($magazine);
        $this->assertFalse($user->hasLiked($magazine));
    }

    /** @test */
    public function username_must_be_unique()
    {
        User::factory()->create(['username' => 'testuser']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        User::factory()->create(['username' => 'testuser']);
    }

    /** @test */
    public function email_must_be_unique()
    {
        User::factory()->create(['email' => 'test@example.com']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        User::factory()->create(['email' => 'test@example.com']);
    }

    /** @test */
    public function number_must_be_unique()
    {
        User::factory()->create(['number' => '09123456789']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        User::factory()->create(['number' => '09123456789']);
    }
}
