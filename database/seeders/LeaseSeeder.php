<?php

namespace Database\Seeders;

use App\Models\Lease;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Lease::factory()->count(10)->create();
    }
}
