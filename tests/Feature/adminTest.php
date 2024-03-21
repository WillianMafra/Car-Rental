<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class adminTest extends TestCase
{

    # sail php artisan test --filter=test_only_admins_can_reach_brands_page
    public function test_only_admins_can_reach_brands_page(): void
    {
        // Create a fake user with role_id = 1 (admin)
        $user = User::factory()->make(['role_id' => 1]);

        // Log in with the false user
        $this->actingAs($user);

        // Access the page
        $response = $this->get('/brands');
    
        // Success?
        $response->assertStatus(200);

        // Create a fake user with role_id = 2 (user)
        $user = User::factory()->make(['role_id' => 2]);

        // Log in with the false user
        $this->actingAs($user);

        // Access the page
        $response = $this->get('/brands');
    
        // Verify if the user is redirected to home page
        $response->assertStatus(302);
        $response->assertLocation('home');
    }
    
}
