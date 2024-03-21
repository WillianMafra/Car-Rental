<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class userSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new User();

        $user->name = 'Test Admin';
        $user->email = 'test@admin.com';
        $user->role_id = 1;
        $user->password = bcrypt('12345');
        $user->save();

        $user = new User();

        $user->name = 'Test User';
        $user->email = 'test@user.com';
        $user->role_id = 2;
        $user->password = bcrypt('12345');
        $user->save();

    }
}
