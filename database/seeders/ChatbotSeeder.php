<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChatbotSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        DB::table('chatbots')->insert([
            [
                'name' => 'SupportBot',
                'user_id' => 1,
                'description' => 'Customer support chatbot for FAQs and queries',
                'platform' => 'Web',
                'language' => 'en',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'SalesBot',
                'user_id' => 1,
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
