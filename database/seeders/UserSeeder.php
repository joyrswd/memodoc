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
            'name' => config('app.admin.name'),
            'password' => bcrypt(config('app.admin.password')),
            'email' => bcrypt(config('app.admin.email')),
        ]);
    }
}
