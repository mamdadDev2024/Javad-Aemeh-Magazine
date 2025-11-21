<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DownloadControllerTest extends TestCase
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
    public function it_can_download_existing_file()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $user->assignRole('user');

        // Create a fake file
        $filePath = 'test-file.pdf';
        Storage::disk('public')->put($filePath, 'fake content');

        $response = $this->actingAs($user)->post(route('download'), [
            'url' => $filePath,
        ]);

        $response->assertStatus(200);
        // In Laravel, download responses have specific headers
        $this->assertEquals('attachment; filename="test-file.pdf"', $response->headers->get('content-disposition'));
    }

    /** @test */
    public function it_handles_nonexistent_file()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->post(route('download'), [
            'url' => 'nonexistent-file.pdf',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function it_validates_download_request()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->post(route('download'), [
            'url' => '',
        ]);

        $response->assertSessionHasErrors(['url']);
    }

    /** @test */
    public function it_handles_download_errors_gracefully()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        // Simulate an error by passing invalid data
        $response = $this->actingAs($user)->post(route('download'), [
            'url' => null,
        ]);

        $response->assertRedirect();
        // Should handle gracefully without crashing
    }

    /** @test */
    public function download_requires_authentication()
    {
        Storage::fake('public');
        $filePath = 'test-file.pdf';
        Storage::disk('public')->put($filePath, 'fake content');

        $response = $this->post(route('download'), [
            'url' => $filePath,
        ]);

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function it_can_download_file_with_special_characters()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $user->assignRole('user');

        $filePath = 'test file with spaces.pdf';
        Storage::disk('public')->put($filePath, 'fake content');

        $response = $this->actingAs($user)->post(route('download'), [
            'url' => $filePath,
        ]);

        $response->assertStatus(200);
    }
}
