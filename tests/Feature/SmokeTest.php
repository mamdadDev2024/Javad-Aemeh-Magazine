<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate');
    }

    /** @test */
    public function home_page_loads()
    {
        $this->get('/')->assertStatus(200);
    }

    /** @test */
    public function listing_pages_load()
    {
        $this->get(route('news'))->assertStatus(200);
        $this->get(route('events'))->assertStatus(200);
        $this->get(route('magazines'))->assertStatus(200);
    }

    /** @test */
    public function search_works_with_all_types()
    {
        $this->get(route('search', ['type' => 'all']))->assertStatus(200);
        $this->get(route('search', ['type' => 'Magazine']))->assertStatus(200);
        $this->get(route('search', ['type' => 'Event']))->assertStatus(200);
        $this->get(route('search', ['type' => 'Khabar']))->assertStatus(200);
        $this->get(route('search', ['type' => 'Article']))->assertStatus(200);
    }

    /** @test */
    public function guest_auth_pages_load()
    {
        $this->get(route('login'))->assertStatus(200);
        $this->get(route('register'))->assertStatus(200);
        $this->get(route('forget'))->assertStatus(200);
        $this->get(route('reset'))->assertStatus(302); // requires session id
    }
}
