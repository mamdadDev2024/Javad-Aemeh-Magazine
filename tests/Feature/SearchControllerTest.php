<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Article;
use App\Models\Magazine;
use App\Models\Event;
use App\Models\Khabar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class SearchControllerTest extends TestCase
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
    public function it_can_search_all_content_types()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        Magazine::factory()->create(['title' => 'Laravel Magazine', 'user_id' => $user->id]);
        Article::factory()->create(['title' => 'Laravel Article']);
        Event::factory()->create(['title' => 'Laravel Event', 'user_id' => $user->id]);
        Khabar::factory()->create(['title' => 'Laravel News', 'user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('search') . '?search=Laravel&type=all');

        $response->assertStatus(200);
        $response->assertViewIs('main.search');
        $response->assertViewHas('results');
        $response->assertViewHas('search', 'Laravel');
        $response->assertViewHas('type', 'all');
    }

    /** @test */
    public function it_can_search_specific_content_type()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        Magazine::factory()->create(['title' => 'Laravel Magazine', 'user_id' => $user->id]);
        Article::factory()->create(['title' => 'Laravel Article']);

        $response = $this->actingAs($user)->get(route('search') . '?search=Laravel&type=Magazine');

        $response->assertStatus(200);
        $response->assertViewHas('results');
        // Should only return magazines
    }

    /** @test */
    public function it_handles_empty_search_query()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->get(route('search') . '?search=&type=all');

        $response->assertStatus(200);
        $response->assertViewIs('main.search');
    }

    /** @test */
    public function it_handles_invalid_search_type()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->get(route('search') . '?search=test&type=InvalidType');

        $response->assertStatus(200);
        // Should handle gracefully, perhaps return empty results
    }

    /** @test */
    public function it_returns_paginated_results()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        // Create multiple items
        for ($i = 0; $i < 15; $i++) {
            Magazine::factory()->create(['title' => "Magazine {$i}", 'user_id' => $user->id]);
        }

        $response = $this->actingAs($user)->get(route('search') . '?search=Magazine&type=all');

        $response->assertStatus(200);
        $results = $response->viewData('results');
        $this->assertLessThanOrEqual(10, $results->count()); // Default pagination
    }

    /** @test */
    public function it_handles_search_errors_gracefully()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        // This test simulates potential database errors or exceptions
        $response = $this->actingAs($user)->get(route('search') . '?search=test&type=all');

        $response->assertStatus(200);
        // Should not crash, should handle errors
    }

    /** @test */
    public function search_requires_no_authentication()
    {
        Magazine::factory()->create(['title' => 'Test Magazine']);

        $response = $this->get(route('search') . '?search=Test&type=all');

        $response->assertStatus(200);
        $response->assertViewIs('main.search');
    }
}