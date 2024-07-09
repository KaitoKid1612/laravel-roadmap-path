<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class WebRoutesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Task 1: point the main "/" URL to the HomeController method "index".
     */
    public function test_home_route()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertViewIs('welcome');
    }

    /**
     * Test Task 2: point the GET URL "/user/[name]" to the UserController method "show".
     */
    public function test_user_route()
    {
        $response = $this->get('/users/john');
        $response->assertStatus(200);
        $response->assertViewIs('user');
        $response->assertViewHas('name', 'john');
    }

    /**
     * Test Task 3: point the GET URL "/about" to the view.
     */
    public function test_about_route()
    {
        $response = $this->get('/about');
        $response->assertStatus(200);
        $response->assertViewIs('pages.about');
    }

    /**
     * Test Task 4: redirect the GET URL "log-in" to a URL "login".
     */
    public function test_log_in_redirect_route()
    {
        $response = $this->get('/log-in');
        $response->assertRedirect('/login');
    }

    /**
     * Test authenticated routes.
     */
    public function test_authenticated_routes()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/app/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('welcome');

        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    /**
     * Test admin routes with is_admin middleware.
     */
    public function test_admin_routes()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
    }
}
