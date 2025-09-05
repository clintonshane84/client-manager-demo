<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InterestsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $interests = [
            'Cooking',
            'Travel',
            'Fitness',
            'Technology',
            'Gaming',
            'Music',
            'Movies & TV',
            'Reading',
            'Photography',
            'Art & Design',
            'DIY & Crafts',
            'Gardening',
            'Sports',
            'Outdoors & Hiking',
            'Food & Wine',
            'Pets & Animals',
            'Finance & Investing',
            'Cars & Motorbikes',
            'Fashion & Beauty',
            'Health & Wellness',
        ];

        $rows = array_map(fn ($name) => [
            'name'       => $name,
            'created_at' => $now,
            'updated_at' => $now,
        ], $interests);

        // Assumes a unique index on "name" (recommended).
        DB::table('interests')->upsert($rows, ['name'], ['updated_at']);
    }
}
