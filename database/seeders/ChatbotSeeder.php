<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChatbotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('chatbots')->insert([
            [
                'name' => 'SupportBot',
                'description' => 'Customer support chatbot for FAQs and queries',
                'platform' => 'Web',
                'language' => 'en',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'SalesBot',
                'description' => 'Helps customers with product recommendations',
                'platform' => 'WhatsApp',
                'language' => 'en',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
