<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_users_can_open_the_homepage(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('homepage'));

        $response->assertOk();
        $response->assertSee('Selamat datang', false);
    }

    public function test_dashboard_redirects_to_homepage(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertRedirect(route('homepage'));
    }
}