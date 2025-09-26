<?php

namespace App\Http\Controllers;

use App\Models\Chatbot;
use Illuminate\Http\Request;
use App\Http\Requests\ChatbotRequest;
use App\Models\ChatbotPublication;
use App\Models\ChatbotPublicationHistory;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;


class ChatbotController extends Controller {
    /**
     * Display a listing of the resource.
     */

    // public function workspace() {
    //     return view('workspace.index');
    // }

    public function index() {
        $chatbots = Chatbot::select(['id', 'name', 'description', 'created_at', 'platform', 'created_at', 'updated_at'])->where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();

        return view('workspace.index', compact('chatbots'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        return view('chatbots.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ChatbotRequest $request) {

        $chatbot = Chatbot::create($request->all());

        // Redirect back to index page with success message
        return response()->json([
            'success' => true,
            'message' => 'Chatbot created successfully',
            'bot_id'  => Crypt::encryptString($chatbot->id),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Chatbot $chatbot) {
        return view('chatbots.show', compact('chatbot'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($encryptedId) {
        $chatbot = Chatbot::findOrFail(Crypt::decryptString($encryptedId));
        return view('chatbots.edit', compact('chatbot'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ChatbotRequest $request, $encryptedId) {
        // dd($request->all());
        $chatbot = Chatbot::findOrFail(Crypt::decryptString($encryptedId));
        $chatbot->update($request->validated());

        return redirect()
            ->route('chatbots.index')
            ->with('success', 'Chatbot updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($encryptedId) {
        $chatbot = Chatbot::findOrFail(Crypt::decryptString($encryptedId));
        $chatbot->delete();

        return redirect()
            ->route('chatbots.index')
            ->with('success', 'Chatbot deleted successfully!');
    }

    /**
     * Get chatbot list for DataTables (AJAX).
     */

    // public function getChatbotList(Request $request) {
    //     $chatbots = Chatbot::select(['id', 'name', 'description', 'created_at', 'platform']);

    //     return DataTables::of($chatbots)
    //         ->addIndexColumn() // generates serial number DT_RowIndex
    //         ->addColumn('action', function ($row) {
    //             $encryptedId = Crypt::encryptString($row->id);
    //             $edit = '<a href="' . route('chatbots.edit', $encryptedId) . '" class="btn btn-sm btn-primary">Edit</a>';
    //             $detailsForm = '<a href="' . route('chatbots.details', $encryptedId) . '" class="btn btn-sm btn-info">Details</a>';
    //             $build_chatbot = '<a href="' . route('chatbots.build', $encryptedId) . '" class="btn btn-sm btn-success">Build</a>';
    //             $delete = '<form method="POST" action="' . route('chatbots.destroy', $encryptedId) . '" style="display:inline-block;">
    //                         ' . csrf_field() . method_field('DELETE') . '
    //                         <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</button>
    //                     </form>';
    //             return $edit . ' ' . $delete . ' ' . $build_chatbot . ' ' . $detailsForm;
    //         })
    //         ->rawColumns(['action'])
    //         ->make(true);
    // }

    /**
     * Build chatbot react.
     */

    public function buildChatbot($encryptedId) {
        $chatbot = Chatbot::findOrFail(Crypt::decryptString($encryptedId));
        return view('chatbots.build_chatbot', compact('chatbot'));
    }

    public function details($encryptedId) {
        $chatbot = Chatbot::findOrFail(Crypt::decryptString($encryptedId));
        return view('chatbots.details', compact('chatbot'));
    }

    public function publish(Request $request) {
        $validated = $request->validate([
            'bot_id'     => 'required|string|max:100',
            'user_id'    => 'required|integer',
            'chatbot_id' => 'required|integer',
            'json'       => 'required|array',
        ]);

        // Check existing record
        $publication = ChatbotPublication::where('bot_id', $validated['bot_id'])->first();

        if ($publication) {
            // Calculate next version
            $lastHistory = ChatbotPublicationHistory::where('publication_id', $publication->id)
                ->orderByDesc('version')
                ->first();
            $nextVersion = $lastHistory ? $lastHistory->version + 1 : 1;

            // Save history with version
            ChatbotPublicationHistory::create([
                'publication_id' => $publication->id,
                'old_payload'    => $publication->payload,
                'new_payload'    => $validated['json'],
                'changed_by'     => $validated['user_id'],
                'version'        => $nextVersion,
            ]);

            // Update publication
            $publication->update([
                'user_id'    => $validated['user_id'],
                'chatbot_id' => $validated['chatbot_id'],
                'payload'    => $validated['json'],
            ]);

            $message = 'Chatbot updated successfully';
        } else {
            // Create new publication
            $publication = ChatbotPublication::create([
                'bot_id'     => $validated['bot_id'],
                'user_id'    => $validated['user_id'],
                'chatbot_id' => $validated['chatbot_id'],
                'payload'    => $validated['json'],
            ]);

            // first history entry (v1)
            ChatbotPublicationHistory::create([
                'publication_id' => $publication->id,
                'old_payload'    => [],
                'new_payload'    => $validated['json'],
                'changed_by'     => $validated['user_id'],
                'version'        => 1,
            ]);

            $message = 'Chatbot published successfully';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $publication,
        ], 200);
    }

    public function history($bot_id) {
        // Find the publication
        $publication = ChatbotPublication::where('bot_id', $bot_id)->first();

        if (!$publication) {
            return response()->json([
                'success' => false,
                'message' => 'Chatbot not found',
            ], 404);
        }

        // Fetch all histories ordered by version
        $histories = ChatbotPublicationHistory::where('publication_id', $publication->id)
            ->orderBy('version', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'bot_id' => $bot_id,
            'current_payload' => $publication->payload,
            'histories' => $histories,
        ]);
    }

    public function getPublishedChatbot($bot_id) {
        // Find publication by bot_id
        $publication = ChatbotPublication::where('bot_id', $bot_id)->first();

        if (!$publication) {
            return response()->json([
                'success' => false,
                'message' => 'Chatbot not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'bot_id'      => $publication->bot_id,
                'user_id'     => $publication->user_id,
                'chatbot_id'  => $publication->chatbot_id,
                'payload'     => $publication->payload,
                'updated_at'  => $publication->updated_at,
                'created_at'  => $publication->created_at,
            ]
        ]);
    }
}
