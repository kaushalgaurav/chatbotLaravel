<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TemplateSeeder extends Seeder {
    public function run(): void {
        $content = [
            [
                "id" => "1",
                "data" => [],
                "type" => "starting",
                "measured" => ["width" => 176, "height" => 69],
                "position" => ["x" => 100, "y" => 400],
                "draggable" => false,
                "selectable" => false
            ],
            [
                "id" => "1759831556120",
                "data" => [
                    "label" => "Ask for a name. Hi! Thanks for showing interest in us! To get started, could you please tell us your name?",
                    "varName" => null
                ],
                "type" => "question",
                "dragging" => false,
                "measured" => ["width" => 277, "height" => 89],
                "position" => ["x" => 362, "y" => 395],
                "selected" => false
            ],
            [
                "id" => "1759831619753",
                "data" => [
                    "label" => "What's your email?",
                    "varName" => null
                ],
                "type" => "question",
                "dragging" => false,
                "measured" => ["width" => 277, "height" => 89],
                "position" => ["x" => 747, "y" => 400],
                "selected" => false
            ],
            [
                "id" => "1759831660954",
                "data" => [
                    "label" => "where we can reach you?",
                    "varName" => null
                ],
                "type" => "question",
                "dragging" => false,
                "measured" => ["width" => 277, "height" => 89],
                "position" => ["x" => 1102, "y" => 399],
                "selected" => false
            ],
            [
                "id" => "1759831688423",
                "data" => [
                    "options" => ["Property listings", "Property management"],
                    "varName" => null,
                    "question" => "What services are you interested in?",
                    "fallbackLabel" => "Any of the above"
                ],
                "type" => "buttons",
                "dragging" => false,
                "measured" => ["width" => 293, "height" => 218],
                "position" => ["x" => 1493, "y" => 363],
                "selected" => false
            ],
            [
                "id" => "1759831718265",
                "data" => [
                    "text" => "Thank you! A member of our team will reach out to you shortly\n\nHave a good one!"
                ],
                "type" => "message",
                "dragging" => false,
                "measured" => ["width" => 267, "height" => 106],
                "position" => ["x" => 1384, "y" => 685],
                "selected" => true
            ]
        ];

        DB::table('templates')->insert([
            'title'      => 'Lead Gen Template',
            'content'    => json_encode($content, JSON_UNESCAPED_SLASHES), // store as JSON
            'category'   => 'Normal',
            'is_active'  => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
