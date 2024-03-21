<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class brandTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_reach_brands_page(): void
    {
        $response = $this->get('/brands');

        $response->assertStatus(302);
    }

    public function test_reach_cars_page(): void
    {
        $response = $this->get('/cars');

        $response->assertStatus(302);
    }
}
