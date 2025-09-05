<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        User::factory()->create([
            'id' => 1,
            'email' => 'admin@clientmanagerdemo.com',
            'password' => Hash::make('password'),
            'is_admin' => 1,
        ]);
    }
}
