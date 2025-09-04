<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LanguagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('languages')->insert([
            [
                'id' => 1,
                'code' => 'en',
                'name' => 'English'
            ],
            [
                'id' => 2,
                'code' => 'zu',
                'name' => 'isiZulu'
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
                'id' => 6,
                'code' => 've',
                'name' => 'Tshivenda'
            ],
            [
                'id' => 7,
                'code' => 'nso',
                'name' => 'Sepedi'
            ],
            [
                'id' => 8,
                'code' => 'tn',
                'name' => 'Setswana'
            ],
            [
                'id' => 9,
                'code' => 'ss',
                'name' => 'siSwati'
            ],
            [
                'id' => 10,
                'code' => 'nr',
                'name' => 'isiNdebele'
            ],
            [
                'id' => 11,
                'code' => 'ts',
                'name' => 'Xitsonga'
            ]
        ]);
    }
}
