<?php

namespace Database\Seeders;

use App\Models\Source\Source;
use Illuminate\Database\Seeder;

class SourcesSeeder extends Seeder
{
    public function run(): void
    {
        $sources = [
            [
                'title' => 'jisty',
                'url' => 'https://jisty.com.ua',
                'sitemaps' => [
                    'https://jisty.com.ua/post-sitemap2.xml',
                ],
            ]
        ];

        foreach ($sources as $sourceData) {
            if (Source::where('url', $sourceData['url'])->exists()) {
                continue;
            }

            /** @var Source $source */
            $source = Source::create([
                'title' => $sourceData['title'],
                'url' => $sourceData['url'],
            ]);

            foreach ($sourceData['sitemaps'] as $sitemap) {
                $source->sitemaps()->create([
                    'url' => $sitemap,
                ]);
            }
        }
    }
}
