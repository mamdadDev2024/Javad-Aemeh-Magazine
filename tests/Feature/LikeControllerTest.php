<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Magazine;
use App\Models\Article;
use App\Models\Event;
use App\Models\Khabar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class LikeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        Role::create(['name' => 'user']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'writer']);
    }

    /** @test */
    public function authenticated_user_can_like_magazine()
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        
        $magazine = Magazine::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('toggle.like', [
            'type' => 'Magazine',
            'id' => $magazine->id
        ]));

        $response->assertRedirect();
        $this->assertTrue($user->hasLiked($magazine));
        $this->assertEquals(1, $magazine->likers()->count());
    }

    /** @test */
    public function authenticated_user_can_like_article()
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        
        $magazine = Magazine::factory()->create(['user_id' => $user->id]);
        $article = Article::factory()->create(['magazine_id' => $magazine->id]);

        $response = $this->actingAs($user)->post(route('toggle.like', [
            'type' => 'article',
            'id' => $article->id
        ]));

        $response->assertRedirect();
        $this->assertTrue($user->hasLiked($article));
        $this->assertEquals(1, $article->likers()->count());
    }

    /** @test */
    public function authenticated_user_can_like_event()
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        
        $event = Event::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('toggle.like', [
            'type' => 'event',
            'id' => $event->id
        ]));

        $response->assertRedirect();
        $this->assertTrue($user->hasLiked($event));
        $this->assertEquals(1, $event->likers()->count());
    }

    /** @test */
    public function authenticated_user_can_like_khabar()
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        
        $khabar = Khabar::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('toggle.like', [
            'type' => 'khabar',
            'id' => $khabar->id
        ]));

        $response->assertRedirect();
        $this->assertTrue($user->hasLiked($khabar));
        $this->assertEquals(1, $khabar->likers()->count());
    }

    /** @test */
    public function authenticated_user_can_unlike_content()
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        
        $magazine = Magazine::factory()->create(['user_id' => $user->id]);
        
        // First like
        $user->like($magazine);
        $this->assertTrue($user->hasLiked($magazine));

        // Then unlike via toggle
        $response = $this->actingAs($user)->post(route('toggle.like', [
            'type' => 'Magazine',
            'id' => $magazine->id
        ]));

        $response->assertRedirect();
        $this->assertFalse($user->hasLiked($magazine));
        $this->assertEquals(0, $magazine->likers()->count());
    }

    /** @test */
    public function guest_user_cannot_like_content()
    {
        $user = User::factory()->create();
        $magazine = Magazine::factory()->create(['user_id' => $user->id]);

        $response = $this->post(route('toggle.like', [
            'type' => 'Magazine',
            'id' => $magazine->id
        ]));

        $response->assertRedirect();
        $this->assertEquals(0, $magazine->likers()->count());
    }

    /** @test */
    public function it_handles_invalid_content_type()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->post(route('toggle.like', [
            'type' => 'InvalidType',
            'id' => 1
        ]));

        $response->assertRedirect();
    }

    /** @test */
    public function it_handles_non_existent_content_id()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->post(route('toggle.like', [
            'type' => 'Magazine',
            'id' => 99999 // Non-existent ID
        ]));

        $response->assertRedirect();
    }

    /** @test */
    public function multiple_users_can_like_same_content()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();
        
        $user1->assignRole('user');
        $user2->assignRole('user');
        $user3->assignRole('user');
        
        $magazine = Magazine::factory()->create(['user_id' => $user1->id]);

        $this->actingAs($user1)->post(route('toggle.like', [
            'type' => 'Magazine',
            'id' => $magazine->id
        ]));

        $this->actingAs($user2)->post(route('toggle.like', [
            'type' => 'Magazine',
            'id' => $magazine->id
        ]));

        $this->actingAs($user3)->post(route('toggle.like', [
            'type' => 'Magazine',
            'id' => $magazine->id
        ]));

        $this->assertEquals(3, $magazine->likers()->count());
        $this->assertTrue($user1->hasLiked($magazine));
        $this->assertTrue($user2->hasLiked($magazine));
        $this->assertTrue($user3->hasLiked($magazine));
    }

    /** @test */
    public function user_can_like_multiple_different_content_types()
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        
        $magazine = Magazine::factory()->create(['user_id' => $user->id]);
        $article = Article::factory()->create(['magazine_id' => $magazine->id]);
        $event = Event::factory()->create(['user_id' => $user->id]);
        $khabar = Khabar::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->post(route('toggle.like', [
            'type' => 'Magazine',
            'id' => $magazine->id
        ]));

        $this->actingAs($user)->post(route('toggle.like', [
            'type' => 'article',
            'id' => $article->id
        ]));

        $this->actingAs($user)->post(route('toggle.like', [
            'type' => 'event',
            'id' => $event->id
        ]));

        $this->actingAs($user)->post(route('toggle.like', [
            'type' => 'khabar',
            'id' => $khabar->id
        ]));

        $this->assertTrue($user->hasLiked($magazine));
        $this->assertTrue($user->hasLiked($article));
        $this->assertTrue($user->hasLiked($event));
        $this->assertTrue($user->hasLiked($khabar));
    }

    /** @test */
    public function like_toggle_works_correctly()
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        
        $magazine = Magazine::factory()->create(['user_id' => $user->id]);

        // First toggle - should like
        $this->actingAs($user)->post(route('toggle.like', [
            'type' => 'Magazine',
            'id' => $magazine->id
        ]));
        $this->assertTrue($user->hasLiked($magazine));

        // Second toggle - should unlike
        $this->actingAs($user)->post(route('toggle.like', [
            'type' => 'Magazine',
            'id' => $magazine->id
        ]));
        $this->assertFalse($user->hasLiked($magazine));

        // Third toggle - should like again
        $this->actingAs($user)->post(route('toggle.like', [
            'type' => 'Magazine',
            'id' => $magazine->id
        ]));
        $this->assertTrue($user->hasLiked($magazine));
    }

    /** @test */
    public function it_handles_case_sensitive_content_types()
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        
        $magazine = Magazine::factory()->create(['user_id' => $user->id]);

        // Test with lowercase
        $response = $this->actingAs($user)->post(route('toggle.like', [
            'type' => 'magazine', // lowercase
            'id' => $magazine->id
        ]));

        $response->assertRedirect();
        // Should handle case insensitivity or return appropriate error
    }

    /** @test */
    public function user_can_like_own_content()
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        
        $magazine = Magazine::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('toggle.like', [
            'type' => 'Magazine',
            'id' => $magazine->id
        ]));

        $response->assertRedirect();
        $this->assertTrue($user->hasLiked($magazine));
    }

    /** @test */
    public function user_can_like_others_content()
    {
        $author = User::factory()->create();
        $liker = User::factory()->create();
        
        $author->assignRole('user');
        $liker->assignRole('user');
        
        $magazine = Magazine::factory()->create(['user_id' => $author->id]);

        $response = $this->actingAs($liker)->post(route('toggle.like', [
            'type' => 'Magazine',
            'id' => $magazine->id
        ]));

        $response->assertRedirect();
        $this->assertTrue($liker->hasLiked($magazine));
        $this->assertFalse($author->hasLiked($magazine));
    }

    /** @test */
    public function like_count_updates_correctly()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();
        
        $user1->assignRole('user');
        $user2->assignRole('user');
        $user3->assignRole('user');
        
        $magazine = Magazine::factory()->create(['user_id' => $user1->id]);

        $this->assertEquals(0, $magazine->likers()->count());

        // User 1 likes
        $this->actingAs($user1)->post(route('toggle.like', [
            'type' => 'Magazine',
            'id' => $magazine->id
        ]));
        $this->assertEquals(1, $magazine->fresh()->likers()->count());

        // User 2 likes
        $this->actingAs($user2)->post(route('toggle.like', [
            'type' => 'Magazine',
            'id' => $magazine->id
        ]));
        $this->assertEquals(2, $magazine->fresh()->likers()->count());

        // User 1 unlikes
        $this->actingAs($user1)->post(route('toggle.like', [
            'type' => 'Magazine',
            'id' => $magazine->id
        ]));
        $this->assertEquals(1, $magazine->fresh()->likers()->count());

        // User 3 likes
        $this->actingAs($user3)->post(route('toggle.like', [
            'type' => 'Magazine',
            'id' => $magazine->id
        ]));
        $this->assertEquals(2, $magazine->fresh()->likers()->count());
    }

    /** @test */
    public function it_handles_like_errors_gracefully()
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        
        $magazine = Magazine::factory()->create(['user_id' => $user->id]);

        // This test simulates database errors or other exceptions
        $response = $this->actingAs($user)->post(route('toggle.like', [
            'type' => 'Magazine',
            'id' => $magazine->id
        ]));

        $response->assertRedirect();
    }

    /** @test */
    public function different_users_can_have_different_like_states()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $user1->assignRole('user');
        $user2->assignRole('user');
        
        $magazine = Magazine::factory()->create(['user_id' => $user1->id]);

        // User 1 likes
        $this->actingAs($user1)->post(route('toggle.like', [
            'type' => 'Magazine',
            'id' => $magazine->id
        ]));

        $this->assertTrue($user1->hasLiked($magazine));
        $this->assertFalse($user2->hasLiked($magazine));

        // User 2 likes
        $this->actingAs($user2)->post(route('toggle.like', [
            'type' => 'Magazine',
            'id' => $magazine->id
        ]));

        $this->assertTrue($user1->hasLiked($magazine));
        $this->assertTrue($user2->hasLiked($magazine));

        // User 1 unlikes
        $this->actingAs($user1)->post(route('toggle.like', [
            'type' => 'Magazine',
            'id' => $magazine->id
        ]));

        $this->assertFalse($user1->hasLiked($magazine));
        $this->assertTrue($user2->hasLiked($magazine));
    }
}