<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_profile_fields_are_visible()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/profile');
        $this->assertStringContainsString('value="'.$user->name.'"', $response->getContent());
        $this->assertStringContainsString('value="'.$user->email.'"', $response->getContent());
    }
    public function test_profile_name_email_update_successful()
    {
        $user = User::factory()->create();
        $newData = [
            'name' => 'New name',
            'email' => 'new@email.com'
        ];
        $this->actingAs($user)->put('/profile', $newData);
        $this->assertDatabaseHas('users', $newData);
        // Check if the user is still able to log in - password unchanged
        $this->assertTrue(Auth::attempt([
            'email' => $user->email,
            'password' => 'password'
        ]));
    }
    public function test_profile_password_update_successful()
    {
        $user = User::factory()->create();
        $newData = [
            'name' => 'New name',
            'email' => 'new@email.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword'
        ];
        $this->actingAs($user)->put('/profile', $newData);
        // Check if the user is able to log in with the new password
        $this->assertTrue(Auth::attempt([
            'email' => $user->email,
            'password' => 'newpassword'
        ]));
    }
}
