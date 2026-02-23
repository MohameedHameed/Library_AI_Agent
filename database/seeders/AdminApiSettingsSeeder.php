<?php

namespace Database\Seeders;

use App\Models\AdminApiSetting;
use Illuminate\Database\Seeder;

class AdminApiSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $apis = [
            [
                'api_name'     => 'openlibrary',
                'display_name' => 'OpenLibrary API',
                'api_key'      => null, // no key required
                'api_url'      => 'https://openlibrary.org',
                'status'       => 'approved',
                'notes'        => 'Free API — no key required. Approved by default.',
            ],
            [
                'api_name'     => 'gutenberg',
                'display_name' => 'Project Gutenberg (Gutendex)',
                'api_key'      => null, // no key required
                'api_url'      => 'https://gutendex.com',
                'status'       => 'approved',
                'notes'        => 'Free API — no key required. Approved by default.',
            ],
            [
                'api_name'     => 'google_books',
                'display_name' => 'Google Books API',
                'api_key'      => null, // set via admin panel or .env GOOGLE_BOOKS_API_KEY
                'api_url'      => 'https://www.googleapis.com/books/v1',
                'status'       => 'pending',
                'notes'        => 'Requires a Google Books API key. Set the key and approve to activate.',
            ],
        ];

        foreach ($apis as $api) {
            AdminApiSetting::firstOrCreate(
                ['api_name' => $api['api_name']],
                $api
            );
        }
    }
}
