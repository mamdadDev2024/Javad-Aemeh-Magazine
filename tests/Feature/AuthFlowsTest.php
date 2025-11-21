<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthFlowsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'name' => 'john',
            'password' => Hash::make('secret123'),
        ]);

        $this->post(route('do_login'), [
            'name' => 'john',
            'password' => 'secret123',
            // bypass captcha in tests if package supports, otherwise assert redirect back
        ])->assertOk();
    }
}
