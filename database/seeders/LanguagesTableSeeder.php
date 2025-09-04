<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;
use App\Models\User;

class LanguagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Language::factory()->create([
            [
                'id' => 1,
                'code' => 'en',
                'name' => 'English'
            ],
            [
                'id' => 2,
                'code' => 'zu',
                'name' => 'Zulu'
            ],
            [
                'id' => 3,
                'code' => 'af',
                'name' => 'Afrikaans'
            ],
            [
                'id' => 4,
                'code' => 'xh',
                'name' => 'Xhosa'
            ],
            [
                'id' => 5,
                'code' => 'se',
                'name' => 'Sesotho'
            ],
            [
                'id' => 5,
                'code' => 've',
                'name' => 'Tshivenda'
            ],
            [
                'id' => 6,
                'code' => 'nso',
                'name' => 'Sepedi'
            ],
            [
                'id' => 7,
                'code' => 'tn',
                'name' => 'Setswana'
            ],
            [
                'id' => 8,
                'code' => 'ss',
                'name' => 'siSwati'
            ],
            [
                'id' => 9,
                'code' => 'nr',
                'name' => 'isiNdebele'
            ],
            [
                'id' => 10,
                'code' => 'ts',
                'name' => 'Xitsonga'
            ],
            [
                'id' => 10,
                'code' => 'ts',
                'name' => 'Xitsonga'
            ]
        ]);
    }
}
