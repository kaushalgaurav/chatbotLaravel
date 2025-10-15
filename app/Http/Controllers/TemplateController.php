<?php

namespace App\Http\Controllers;

use App\Models\Chatbot;
use App\Models\Template;
use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class TemplateController extends Controller {
    // Get all templates
    public function index() {
        $templates = Template::latest()->get();
        // dd($templates);
        return response()->json($templates);
    }

    // Store new template
    public function store(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'nullable|string|max:255',
        ]);

        $template = Template::create($validated);

        return response()->json([
            'message' => 'Template created successfully',
            'data' => $template,
        ]);
    }

    public function copyToChatbot(Request $request, $templateId) {

        $validated = $request->validate([
            'chatbot_id' => 'required|exists:chatbots,id',
        ]);

        $template = Template::findOrFail($templateId);

        // Create a new chatbot based on the template
        $chatbot = Chatbot::insertGetId([
            'name' => $template->title,
            'user_id' => Auth::id(),
            'description' => 'Template based chatbot',
            'platform' => 'Web',
            'language' => 'en',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $publication = Publication::create([
            'user_id'      => Auth::id(),
            'chatbot_id'   => $chatbot,
            'bot_id'       => 'v-' . time(),
            'payload'      => json_decode($template->content, true), // decode JSON to array
            'is_published' => 0,
            'status'       => '1',
        ]);
        // Redirect back to index page with success message
        return response()->json([
            'success' => true,
            'message' => 'Chatbot created successfully',
            'bot_id'  => Crypt::encryptString($chatbot),
        ], 201);
    }
}
