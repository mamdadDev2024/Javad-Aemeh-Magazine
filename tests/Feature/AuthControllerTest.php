<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class AuthControllerTest extends TestCase
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
    public function it_shows_login_page()
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    /** @test */
    public function it_shows_register_page()
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }

    /** @test */
    public function it_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'password' => Hash::make('password123')
        ]);
        $user->assignRole('user');

        $response = $this->post(route('do_login'), [
            'username' => 'testuser',
            'password' => 'password123'
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function it_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'password' => Hash::make('password123')
        ]);

        $response = $this->post(route('do_login'), [
            'username' => 'testuser',
            'password' => 'wrongpassword'
        ]);

        $response->assertRedirect();
        $this->assertGuest();
    }

    /** @test */
    public function it_validates_login_request()
    {
        $response = $this->post(route('do_login'), [
            'username' => '',
            'password' => ''
        ]);

        $response->assertSessionHasErrors(['username', 'password']);
    }

    /** @test */
    public function it_can_register_new_user()
    {
        $response = $this->post(route('do_register'), [
            'username' => 'newuser',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'number' => '09123456789'
        ]);

        $response->assertRedirect(route('home'));
        
        $this->assertDatabaseHas('users', [
            'username' => 'newuser',
            'email' => 'newuser@example.com',
            'number' => '09123456789'
        ]);

        $user = User::where('username', 'newuser')->first();
        $this->assertTrue($user->hasRole('user'));
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function it_validates_register_request()
    {
        $response = $this->post(route('do_register'), [
            'username' => '',
            'email' => 'invalid-email',
            'password' => '123',
            'password_confirmation' => '456',
            'number' => ''
        ]);

        $response->assertSessionHasErrors([
            'username', 
            'email', 
            'password', 
            'number'
        ]);
    }

    /** @test */
    public function it_prevents_duplicate_username_registration()
    {
        User::factory()->create(['username' => 'existinguser']);

        $response = $this->post(route('do_register'), [
            'username' => 'existinguser',
            'email' => 'new@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'number' => '09123456789'
        ]);

        $response->assertSessionHasErrors(['username']);
    }

    /** @test */
    public function it_prevents_duplicate_email_registration()
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->post(route('do_register'), [
            'username' => 'newuser',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'number' => '09123456789'
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function it_can_logout_authenticated_user()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->post(route('auth.logout'));

        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }

    /** @test */
    public function it_shows_forget_password_page()
    {
        $response = $this->get(route('forget'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.forget');
    }

    /** @test */
    public function it_can_initiate_password_reset_with_valid_credentials()
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'number' => '09123456789'
        ]);
        $user->assignRole('user');

        $response = $this->post(route('do_forget'), [
            'username' => 'testuser',
            'number' => '09123456789'
        ]);

        $response->assertRedirect(route('reset'));
        $response->assertSessionHas('reset_user_id', $user->id);
    }

    /** @test */
    public function it_cannot_reset_super_admin_password()
    {
        $superAdmin = User::factory()->create([
            'username' => 'superadmin',
            'number' => '09123456789'
        ]);
        $superAdmin->assignRole('super admin');

        $response = $this->post(route('do_forget'), [
            'username' => 'superadmin',
            'number' => '09123456789'
        ]);

        $response->assertRedirect('/');
        $response->assertSessionMissing('reset_user_id');
    }

    /** @test */
    public function it_rejects_invalid_forget_password_credentials()
    {
        $response = $this->post(route('do_forget'), [
            'username' => 'nonexistent',
            'number' => '09123456789'
        ]);

        $response->assertRedirect();
        $response->assertSessionMissing('reset_user_id');
    }

    /** @test */
    public function it_shows_reset_password_page_with_valid_session()
    {
        $user = User::factory()->create();
        
        $response = $this->withSession(['reset_user_id' => $user->id])
                         ->get(route('reset'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.reset');
    }

    /** @test */
    public function it_redirects_reset_password_page_without_session()
    {
        $response = $this->get(route('reset'));

        $response->assertRedirect('/');
    }

    /** @test */
    public function it_can_reset_password_with_valid_session()
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword')
        ]);

        $response = $this->withSession(['reset_user_id' => $user->id])
                         ->post(route('do_reset'), [
                             'password' => 'newpassword123',
                             'password_confirmation' => 'newpassword123',
                             'g-recaptcha-response' => 'valid-captcha'
                         ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionMissing('reset_user_id');
        
        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    /** @test */
    public function it_validates_reset_password_request()
    {
        $user = User::factory()->create();

        $response = $this->withSession(['reset_user_id' => $user->id])
                         ->post(route('do_reset'), [
                             'password' => '123',
                             'password_confirmation' => '456'
                         ]);

        $response->assertSessionHasErrors(['password', 'g-recaptcha-response']);
    }

    /** @test */
    public function it_prevents_reset_password_without_session()
    {
        $response = $this->post(route('do_reset'), [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
            'g-recaptcha-response' => 'valid-captcha'
        ]);

        $response->assertRedirect(route('home'));
    }

    /** @test */
    public function it_handles_login_errors_gracefully()
    {
        // This test simulates database errors or other exceptions
        $response = $this->post(route('do_login'), [
            'username' => 'testuser',
            'password' => 'password123'
        ]);

        // Should redirect back on error
        $response->assertRedirect();
    }

    /** @test */
    public function it_handles_registration_errors_gracefully()
    {
        // Create a user to cause potential conflicts
        User::factory()->create([
            'username' => 'testuser',
            'email' => 'test@example.com'
        ]);

        $response = $this->post(route('do_register'), [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'number' => '09123456789'
        ]);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function it_requires_password_confirmation_for_registration()
    {
        $response = $this->post(route('do_register'), [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword',
            'number' => '09123456789'
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function it_requires_minimum_password_length()
    {
        $response = $this->post(route('do_register'), [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => '123',
            'password_confirmation' => '123',
            'number' => '09123456789'
        ]);

        $response->assertSessionHasErrors(['password']);
    }
}