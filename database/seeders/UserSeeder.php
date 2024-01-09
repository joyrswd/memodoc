<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => env('ADMIN_USER_NAME'),
            'password' => bcrypt(env('ADMIN_USER_PASSWORD')),
            'email' => bcrypt(env('ADMIN_USER_EMAIL')),
        ]);
    }
}
