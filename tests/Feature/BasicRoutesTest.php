<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Event;
use App\Models\Khabar;
use App\Models\Magazine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BasicRoutesTest extends TestCase
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

    public function test_home_page_loads()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_news_page_loads()
    {
        $response = $this->get(route('news'));
        $response->assertStatus(200);
    }

    public function test_events_page_loads()
    {
        $response = $this->get(route('events'));
        $response->assertStatus(200);
    }

    public function test_magazines_page_loads()
    {
        $response = $this->get(route('magazines'));
        $response->assertStatus(200);
    }

    public function test_search_page_loads()
    {
        $response = $this->get(route('search'));
        $response->assertStatus(200);
    }

    public function test_contact_page_loads()
    {
        $response = $this->get(route('contact'));
        $response->assertStatus(200);
    }

    public function test_login_page_loads()
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
    }

    public function test_register_page_loads()
    {
        $response = $this->get(route('register'));
        $response->assertStatus(200);
    }

    public function test_magazine_show_page_loads()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $magazine = Magazine::factory()->create([
            'user_id' => $user->id,
            'slug' => 'test-magazine',
        ]);

        $response = $this->get(route('Magazine.show', $magazine->slug));
        $response->assertStatus(200);
    }

    public function test_article_show_page_loads()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $magazine = Magazine::factory()->create(['user_id' => $user->id]);
        $article = Article::factory()->create([
            'magazine_id' => $magazine->id,
            'slug' => 'test-article',
        ]);

        $response = $this->get(route('Article.show', $article->slug));
        $response->assertStatus(200);
    }

    public function test_event_show_page_loads()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $event = Event::factory()->create([
            'user_id' => $user->id,
            'slug' => 'test-event',
        ]);

        $response = $this->get(route('Event.show', $event->slug));
        $response->assertStatus(200);
    }

    public function test_news_show_page_loads()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $khabar = Khabar::factory()->create([
            'user_id' => $user->id,
            'slug' => 'test-news',
        ]);

        $response = $this->get(route('Khabar.show', $khabar->slug));
        $response->assertStatus(200);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'password' => bcrypt('password123'),
        ]);
        $user->assignRole('user');

        $response = $this->post(route('do_login'), [
            'username' => 'testuser',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_register()
    {
        $response = $this->post(route('do_register'), [
            'username' => 'newuser',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'number' => '09123456789',
        ]);

        $response->assertRedirect(route('home'));

        $this->assertDatabaseHas('users', [
            'username' => 'newuser',
            'email' => 'newuser@example.com',
        ]);
    }

    public function test_authenticated_user_can_comment()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $magazine = Magazine::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('create.comment', [
            'model' => 'Magazine',
            'contentId' => $magazine->id,
        ]), [
            'body' => 'This is a test comment',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'body' => 'This is a test comment',
            'user_id' => $user->id,
        ]);
    }

    public function test_authenticated_user_can_like_content()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $magazine = Magazine::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('toggle.like', [
            'type' => 'Magazine',
            'id' => $magazine->id,
        ]));

        $response->assertRedirect();
        $this->assertTrue($user->hasLiked($magazine));
    }

    public function test_search_works()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        Magazine::factory()->create([
            'user_id' => $user->id,
            'title' => 'Laravel Magazine',
        ]);

        $response = $this->get(route('search', [
            'search' => 'Laravel',
            'type' => 'all',
        ]));

        $response->assertStatus(200);
    }

    public function test_contact_form_works()
    {
        $response = $this->post(route('do.contact'), [
            'body' => 'Test contact message',
            'phone' => '09123456789',
        ]);

        $response->assertRedirect(route('home'));

        $this->assertDatabaseHas('contacts', [
            'body' => 'Test contact message',
            'phone' => '09123456789',
        ]);
    }
}
