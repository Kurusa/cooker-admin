<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SourcesSeeder extends Seeder
{
    public function run(): void
    {
        $sources = [
            [
                'title' => '',
                'url' => '',
                'sitemap_urls' => [

                ],
            ]
        ];
    }
}
