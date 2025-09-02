<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class IdentityTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        DB::table('identity_types')->insert([
            [
                'code' => 'za_id',
                'name' => 'South African ID',
                'validation_pattern' => '^\d{13}$', // 13-digit numeric SA ID
                'is_sensitive' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'passport',
                'name' => 'Passport',
                'validation_pattern' => '^[A-Z0-9]{6,12}$',
                'is_sensitive' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'drivers_license',
                'name' => 'Driver’s License',
                'validation_pattern' => '^[A-Z0-9\-]{6,15}$',
                'is_sensitive' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
