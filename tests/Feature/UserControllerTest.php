<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserControllerTest extends TestCase
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
    public function it_shows_change_password_page()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->get(route('user.change.password'));

        $response->assertStatus(200);
        $response->assertViewIs('user-panel.change_password');
        $response->assertViewHas('user_id', $user->id);
    }

    /** @test */
    public function it_can_change_password_with_valid_data()
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword'),
        ]);
        $user->assignRole('user');

        $response = $this->actingAs($user)->post(route('user.do.change.password'), [
            'id' => $user->id,
            'current_password' => 'oldpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect();
        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    /** @test */
    public function it_validates_change_password_request()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->post(route('user.do.change.password'), [
            'id' => $user->id,
            'password' => '123',
            'password_confirmation' => '456',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function it_prevents_unauthorized_password_change()
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $user->assignRole('user');
        $anotherUser->assignRole('user');

        $response = $this->actingAs($user)->post(route('user.do.change.password'), [
            'id' => $anotherUser->id,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function it_shows_profile_page()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->get(route('user.profile'));

        $response->assertStatus(200);
        $response->assertViewIs('user-panel.profile');
        $response->assertViewHas('user', $user);
    }

    /** @test */
    public function it_can_update_profile()
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);
        $user->assignRole('user');

        $response = $this->actingAs($user)->post(route('profile'), [
            'name' => 'New Name',
            'email' => 'new@example.com',
            'number' => '09123456789',
        ]);

        $response->assertRedirect();
        $user->refresh();
        $this->assertEquals('New Name', $user->name);
        $this->assertEquals('new@example.com', $user->email);
    }

    /** @test */
    public function it_can_upload_profile_image()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $user->assignRole('user');

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($user)->post(route('profile'), [
            'image' => $file,
        ]);

        $response->assertRedirect();
        $user->refresh();
        $this->assertNotNull($user->image);
        Storage::disk('public')->assertExists($user->image);
    }

    /** @test */
    public function it_shows_suggest_page()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->get(route('user.create'));

        $response->assertStatus(200);
        $response->assertViewIs('user-panel.suggest');
    }

    /** @test */
    public function it_can_create_recommend_with_valid_data()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $user->assignRole('user');

        $pdfFile = UploadedFile::fake()->create('document.pdf', 1000);
        $wordFile = UploadedFile::fake()->create('document.docx', 1000);

        $response = $this->actingAs($user)->post(route('user.do.suggest'), [
            'title' => 'Test Recommendation',
            'pdf' => $pdfFile,
            'word' => $wordFile,
            'g-recaptcha-response' => 'valid-captcha',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('recommends', [
            'title' => 'Test Recommendation',
            'user_id' => $user->id,
        ]);
        Storage::disk('public')->assertExists('attachments/document.pdf');
        Storage::disk('public')->assertExists('attachments/document.docx');
    }

    /** @test */
    public function it_validates_suggest_request()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->post(route('user.do.suggest'), [
            'title' => '',
            'g-recaptcha-response' => 'valid-captcha',
        ]);

        $response->assertSessionHasErrors(['title', 'pdf']);
    }

    /** @test */
    public function it_can_delete_own_account()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->delete(route('user.delete', $user->id));

        $response->assertRedirect();
        $this->assertSoftDeleted($user);
    }

    /** @test */
    public function it_prevents_deleting_super_admin_account()
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super admin');

        $response = $this->actingAs($superAdmin)->delete(route('user.delete', $superAdmin->id));

        $response->assertRedirect();
        $this->assertNotSoftDeleted($superAdmin);
    }

    /** @test */
    public function it_prevents_deleting_other_users_account()
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $user->assignRole('user');
        $anotherUser->assignRole('user');

        $response = $this->actingAs($user)->delete(route('user.delete', $anotherUser->id));

        $response->assertRedirect();
        $this->assertNotSoftDeleted($anotherUser);
    }
}
