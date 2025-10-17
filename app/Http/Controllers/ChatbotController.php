<?php

namespace App\Http\Controllers;

use App\Models\Chatbot;
use Illuminate\Http\Request;
use App\Http\Requests\ChatbotRequest;
use App\Imports\ProductsImport;
use App\Models\Publication;
use App\Models\PublicationHistory;
use App\Models\Conversation;
use App\Models\File;
use App\Models\Product;
use App\Models\ProductJson;
use App\Models\UploadJob;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        // dd($request->all());
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
        return view('chatbots.build_chatbot', compact('chatbot', 'encryptedId'));
    }

    public function details($encryptedId) {
        $chatbot = Chatbot::findOrFail(Crypt::decryptString($encryptedId));
        return view('chatbots.details', compact('chatbot'));
    }

    public function publish(Request $request) {
        $validated = $request->validate([
            'bot_id'       => 'required|string|max:100',
            'user_id'      => 'required|integer',
            'chatbot_id'   => 'required|integer',
            'json'         => 'required|array',
            'is_published' => 'sometimes|integer',
        ]);

        $isPublished = $validated['is_published'] ?? 0;


        // Check existing record
        $publication = Publication::where('bot_id', $validated['bot_id'])->first();

        if ($publication) {
            // Calculate next version
            $lastHistory = PublicationHistory::where('publication_id', $publication->id)
                ->orderByDesc('version')
                ->first();
            $nextVersion = $lastHistory ? $lastHistory->version + 1 : 1;

            // ✅ Update publication
            $publication->update([
                'user_id'      => $validated['user_id'],
                'chatbot_id'   => $validated['chatbot_id'],
                'payload'      => $validated['json'],
                'is_published' => $isPublished,
                'status'       => $isPublished,
            ]);

            // ✅ Save history ONLY if published
            if ($isPublished == 1) {
                PublicationHistory::create([
                    'publication_id' => $publication->id,
                    'old_payload'    => $publication->getOriginal('payload'),
                    'new_payload'    => $validated['json'],
                    'changed_by'     => $validated['user_id'],
                    'version'        => $nextVersion,
                    'is_published'   => $isPublished,
                ]);
            }

            $message = 'Chatbot updated successfully';
        } else {
            // Create new publication
            $publication = Publication::create([
                'bot_id'       => $validated['bot_id'],
                'user_id'      => $validated['user_id'],
                'chatbot_id'   => $validated['chatbot_id'],
                'payload'      => $validated['json'],
                'is_published' => $isPublished,
                'status'       => $isPublished,
            ]);

            // ✅ Only create history if published
            if ($isPublished == 1) {
                PublicationHistory::create([
                    'publication_id' => $publication->id,
                    'old_payload'    => [],
                    'new_payload'    => $validated['json'],
                    'changed_by'     => $validated['user_id'],
                    'version'        => 1,
                    'is_published'   => 1,
                ]);
            }

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
        $publication = Publication::where('bot_id', $bot_id)->first();

        if (!$publication) {
            return response()->json([
                'success' => false,
                'message' => 'Chatbot not found',
            ], 404);
        }

        // Fetch all histories ordered by version
        $histories = PublicationHistory::where('publication_id', $publication->id)
            ->orderBy('version', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'bot_id' => $bot_id,
            'current_payload' => $publication->payload,
            'histories' => $histories,
        ]);
    }

    public function getPublishedChatbot($chatbot_id) {
        // Find publication by bot_id
        $publication = Publication::where('chatbot_id', $chatbot_id)->first();
        // dd($publication);
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

    public function designChatbot($encryptedId) {
        $chatbot = Chatbot::findOrFail(Crypt::decryptString($encryptedId));
        return view('chatbots.design', compact('chatbot'));
    }

    public function settingChatbot($encryptedId) {
        $chatbot = Chatbot::findOrFail(Crypt::decryptString($encryptedId));
        return view('chatbots.settings', compact('chatbot'));
    }

    public function shareChatbot($encryptedId) {
        $chatbot = Chatbot::findOrFail(Crypt::decryptString($encryptedId));
        return view('chatbots.share', compact('chatbot'));
    }

    // public function analyzeChatbot($encryptedId) {
    //     // dd('analyzeChatbot');
    //     $chatbot = Chatbot::findOrFail(Crypt::decryptString($encryptedId));
    //     $chatbotId = $chatbot->id;
    //     $publishedBot = Publication::where('chatbot_id', $chatbotId)->where('is_published', 1)->first();
    //     if (!$publishedBot) {
    //         return redirect()->back()->with('error', 'No published version found for this chatbot.');
    //     }
    //     $botId = $publishedBot->bot_id;

    //     // Fetch all conversations for this bot, grouped by conversation_id
    //     $conversations = Conversation::where('bot_id', $botId)
    //         ->orderBy('conversation_id')
    //         ->orderBy('created_at')
    //         ->get()
    //         ->groupBy('conversation_id');


    //     // Prepare table data: bot messages as headers, user responses as rows
    //     $tableData = [];
    //     $currentBotMessage = [];
    //     foreach ($conversations as $convId => $messages) {
    //         $row = [];
    //         foreach ($messages as $msg) {
    //             if ($msg->sender === 'bot') {
    //                 $currentBotMessage = $msg->message;
    //                 if (!isset($tableData[$currentBotMessage])) {
    //                     $tableData[$currentBotMessage] = [];
    //                 }
    //             } elseif ($msg->sender === 'user') {
    //                 $tableData[$currentBotMessage][] = $msg->message;
    //             }
    //         }
    //     }

    //     dd($tableData);
    //     // Generic analytics
    //     $analytics = $this->getAnalytics($conversations);

    //     return view('chatbots.analyze', compact('tableData', 'analytics'));
    // }

    // public function analyzeChatbot($encryptedId) {
    //     $chatbot = Chatbot::findOrFail(Crypt::decryptString($encryptedId));
    //     $chatbotId = $chatbot->id;

    //     $publishedBot = Publication::where('chatbot_id', $chatbotId)
    //         ->where('is_published', 1)
    //         ->first();

    //     if (!$publishedBot) {
    //         return redirect()->back()->with('error', 'No published version found for this chatbot.');
    //     }

    //     $botId = $publishedBot->bot_id;

    //     // Fetch all conversations for this bot, grouped by conversation_id
    //     $conversations = Conversation::where('bot_id', $botId)
    //         ->orderBy('conversation_id')
    //         ->orderBy('created_at')
    //         ->get()
    //         ->groupBy('conversation_id');

    //     $tableData = [];

    //     foreach ($conversations as $convId => $messages) {
    //         $currentBotMessage = null;

    //         foreach ($messages as $msg) {
    //             if ($msg->sender === 'bot') {
    //                 $currentBotMessage = $msg->message;

    //                 // Detect JSON and extract "question" field
    //                 $decoded = json_decode($currentBotMessage, true);
    //                 if (json_last_error() === JSON_ERROR_NONE && isset($decoded['question'])) {
    //                     $currentBotMessage = $decoded['question'];
    //                 }
    //             }

    //             if ($msg->sender === 'user') {
    //                 $tableData[] = [
    //                     'conversation_id' => $convId,
    //                     'bot_message' => $currentBotMessage,
    //                     'user_reply' => $msg->message
    //                 ];
    //             }
    //         }

    //         // Check if last bot message has no user reply
    //         if ($messages->last()->sender === 'bot') {
    //             $lastBotMessage = $messages->last()->message;
    //             $decoded = json_decode($lastBotMessage, true);
    //             if (json_last_error() === JSON_ERROR_NONE && isset($decoded['question'])) {
    //                 $lastBotMessage = $decoded['question'];
    //             }

    //             $tableData[] = [
    //                 'conversation_id' => $convId,
    //                 'bot_message' => $lastBotMessage,
    //                 'user_reply' => 'N/A'
    //             ];
    //         }
    //     }

    //     // Optional analytics
    //     $analytics = $this->getAnalytics($conversations);

    //     return view('chatbots.analyze', compact('tableData', 'analytics'));
    // }
    // public function analyzeChatbot($encryptedId) {
    //     $chatbot = Chatbot::findOrFail(Crypt::decryptString($encryptedId));
    //     $chatbotId = $chatbot->id;

    //     $publishedBot = Publication::where('chatbot_id', $chatbotId)
    //         ->where('is_published', 1)
    //         ->first();

    //     if (!$publishedBot) {
    //         return redirect()->back()->with('error', 'No published version found for this chatbot.');
    //     }

    //     $botId = $publishedBot->bot_id;

    //     // Fetch all conversations for this bot
    //     $conversations = Conversation::where('bot_id', $botId)
    //         ->orderBy('conversation_id')
    //         ->orderBy('created_at')
    //         ->get()
    //         ->groupBy('conversation_id');

    //     $allBotMessages = [];
    //     $tableData = [];

    //     // Step 1: Collect all unique bot messages across all conversations
    //     foreach ($conversations as $convId => $messages) {
    //         foreach ($messages as $msg) {
    //             if ($msg->sender === 'bot') {
    //                 $botMessage = $msg->message;

    //                 // Detect JSON question
    //                 $decoded = json_decode($botMessage, true);
    //                 if (json_last_error() === JSON_ERROR_NONE && isset($decoded['question'])) {
    //                     $botMessage = $decoded['question'];
    //                 }

    //                 if (!in_array($botMessage, $allBotMessages)) {
    //                     $allBotMessages[] = $botMessage;
    //                 }
    //             }
    //         }
    //     }

    //     // Step 2: Prepare table rows per conversation
    //     foreach ($conversations as $convId => $messages) {
    //         $row = ['conversation_id' => $convId];

    //         // Initialize all bot columns as N/A
    //         foreach ($allBotMessages as $botMessage) {
    //             $row[$botMessage] = 'N/A';
    //         }

    //         // Pair each bot with its next user reply
    //         $botQueue = []; // queue to store bot messages waiting for reply
    //         foreach ($messages as $msg) {
    //             if ($msg->sender === 'bot') {
    //                 $botMessage = $msg->message;
    //                 $decoded = json_decode($botMessage, true);
    //                 if (json_last_error() === JSON_ERROR_NONE && isset($decoded['question'])) {
    //                     $botMessage = $decoded['question'];
    //                 }
    //                 $botQueue[] = $botMessage;
    //             } elseif ($msg->sender === 'user' && !empty($botQueue)) {
    //                 // assign the first bot message waiting in the queue
    //                 $currentBot = array_shift($botQueue);
    //                 $row[$currentBot] = $msg->message;
    //             }
    //         }

    //         $tableData[] = $row;
    //     }
    //     // Optional analytics
    //     $analytics = $this->getAnalytics($conversations);

    //     return view('chatbots.analyze', compact('tableData', 'allBotMessages', 'analytics'));
    // }



    // /**
    //  * Generic analytics suitable for any bot
    //  */
    // protected function getAnalytics($conversations) {
    //     $totalConversations = $conversations->count();
    //     $totalMessages = $conversations->sum(fn($msgs) => $msgs->count());
    //     $botMessages = $conversations->sum(fn($msgs) => $msgs->where('sender', 'bot')->count());
    //     $userMessages = $conversations->sum(fn($msgs) => $msgs->where('sender', 'user')->count());

    //     return [
    //         'total_conversations' => $totalConversations,
    //         'total_messages' => $totalMessages,
    //         'bot_messages' => $botMessages,
    //         'user_messages' => $userMessages,
    //         'average_user_messages_per_conversation' => $totalConversations ? round($userMessages / $totalConversations, 2) : 0,
    //     ];
    // }


    public function analyzeChatbot($encryptedId) {
        $chatbot = Chatbot::findOrFail(Crypt::decryptString($encryptedId));
        $chatbotId = $chatbot->id;

        $publishedBot = Publication::where('chatbot_id', $chatbotId)
            ->where('is_published', 1)
            ->first();

        if (!$publishedBot) {
            return redirect()->back()->with('error', 'No published version found for this chatbot.');
        }

        $botId = $publishedBot->bot_id;

        // Fetch all conversations for this bot
        $conversations = Conversation::where('bot_id', $botId)
            ->orderBy('conversation_id')
            ->orderBy('created_at')
            ->get()
            ->groupBy('conversation_id');

        $allBotMessages = [];
        $tableData = [];

        // Collect unique bot messages
        foreach ($conversations as $convId => $messages) {
            foreach ($messages as $msg) {
                if ($msg->sender === 'bot') {
                    $botMessage = $msg->message;
                    $decoded = json_decode($botMessage, true);
                    if (json_last_error() === JSON_ERROR_NONE && isset($decoded['question'])) {
                        $botMessage = $decoded['question'];
                    }
                    if (!in_array($botMessage, $allBotMessages)) {
                        $allBotMessages[] = $botMessage;
                    }
                }
            }
        }

        // Prepare pivot table rows
        foreach ($conversations as $convId => $messages) {
            $row = ['conversation_id' => $convId];

            foreach ($allBotMessages as $botMessage) {
                $row[$botMessage] = 'N/A';
            }

            $botQueue = [];
            foreach ($messages as $msg) {
                if ($msg->sender === 'bot') {
                    $botMessage = $msg->message;
                    $decoded = json_decode($botMessage, true);
                    if (json_last_error() === JSON_ERROR_NONE && isset($decoded['question'])) {
                        $botMessage = $decoded['question'];
                    }
                    $botQueue[] = $botMessage;
                } elseif ($msg->sender === 'user' && !empty($botQueue)) {
                    $currentBot = array_shift($botQueue);
                    $row[$currentBot] = $msg->message;
                }
            }

            $tableData[] = $row;
        }

        // Analytics
        $analytics = $this->getAnalytics($conversations);

        // Response Rate per bot message
        $responseRate = [];
        foreach ($allBotMessages as $botMessage) {
            $answered = 0;
            $notAnswered = 0;
            foreach ($tableData as $row) {
                if ($row[$botMessage] !== 'N/A') $answered++;
                else $notAnswered++;
            }
            $responseRate[] = [
                'label' => $botMessage,
                'answered' => $answered,
                'not_answered' => $notAnswered,
            ];
        }

        // Top Bot Messages by Replies
        $topBotMessages = [];
        foreach ($responseRate as $item) {
            $topBotMessages[] = [
                'label' => $item['label'],
                'replies' => $item['answered'],
            ];
        }

        return view('chatbots.analyze', compact(
            'tableData',
            'allBotMessages',
            'analytics',
            'responseRate',
            'topBotMessages'
        ));
    }

    /**
     * Generic analytics suitable for any bot
     */
    protected function getAnalytics($conversations) {
        $totalConversations = $conversations->count();
        $totalMessages = $conversations->sum(fn($msgs) => $msgs->count());
        $botMessages = $conversations->sum(fn($msgs) => $msgs->where('sender', 'bot')->count());
        $userMessages = $conversations->sum(fn($msgs) => $msgs->where('sender', 'user')->count());

        return [
            'total_conversations' => $totalConversations,
            'total_messages' => $totalMessages,
            'bot_messages' => $botMessages,
            'user_messages' => $userMessages,
            'average_user_messages_per_conversation' => $totalConversations ? round($userMessages / $totalConversations, 2) : 0,
        ];
    }




    public function uploadProducts(Request $request) {
        $validator = Validator::make($request->all(), [
            'files' => 'required|file|mimes:csv,xls,xlsx|max:102400', // 100MB
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $chatbot = Chatbot::create([
            'user_id' => Auth::id(),
            'name' => 'MSME Bot ' . time(),
            'description' => 'Auto-generated bot for MSME product upload',
            'platform' => '2',
        ]);

        $file = $request->file('files');
        $originalName = $file->getClientOriginalName();
        $ext = $file->getClientOriginalExtension();
        $safeName = pathinfo($originalName, PATHINFO_FILENAME);
        $storedName = $safeName . '_' . time() . '_' . Str::random(6) . '.' . $ext;
        $storePath = "public/msme_uploads/{$chatbot->id}/";
        $storedPath = $file->storeAs($storePath, $storedName);

        // Save file record
        $fileRecord = File::create([
            'chatbot_id' => $chatbot->id,
            'file_name' => $originalName,
            'file_path' => $storedPath,
            'file_type' => $ext,
            'file_size' => $file->getSize(),
        ]);

        // Estimate rows
        $totalRows = $this->estimateRowCount(storage_path('app/' . $storedPath), $ext);

        // Create upload job
        $uploadUuid = (string) Str::uuid();
        $job = UploadJob::create([
            'upload_uuid' => $uploadUuid,
            'chatbot_id' => $chatbot->id,
            'file_record_id' => $fileRecord->id,
            'total_rows' => $totalRows,
            'processed_rows' => 0,
            'inserted' => 0,
            'updated' => 0,
            'status' => 'queued',
        ]);

        // Queue the import
        $import = new ProductsImport($chatbot->id, $uploadUuid);
        Excel::queueImport($import, $storedPath, 'local');

        return response()->json([
            'success' => true,
            'message' => 'File uploaded and import queued successfully.',
            'upload_uuid' => $uploadUuid,
            'job_id' => $job->id
        ]);
    }

    /**
     * Poll upload status
     */
    public function uploadStatus(string $uploadUuid) {
        $job = UploadJob::where('upload_uuid', $uploadUuid)->first();

        if (!$job) {
            return response()->json(['success' => false, 'message' => 'Upload job not found.'], 404);
        }

        $snapshot = null;
        if ($job->status === 'done') {
            $json = ProductJson::where('chatbot_id', $job->chatbot_id)->first();
            $snapshot = $json ? $json->products : null;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'status' => $job->status,
                'total_rows' => $job->total_rows,
                'processed_rows' => $job->processed_rows,
                'inserted' => $job->inserted,
                'updated' => $job->updated,
                'snapshot' => $snapshot
            ]
        ]);
    }

    /**
     * Estimate total rows
     */
    private function estimateRowCount(string $fullPath, string $ext): int {
        try {
            if ($ext === 'csv') {
                $count = 0;
                $f = fopen($fullPath, 'r');
                while (!feof($f)) {
                    $line = fgets($f);
                    if ($line !== false) $count++;
                }
                fclose($f);
                return max(0, $count - 1);
            } else {
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($fullPath);
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($fullPath);
                $sheet = $spreadsheet->getActiveSheet();
                return max(0, $sheet->getHighestDataRow() - 1);
            }
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Download dummy CSV/Excel
     */
    public function downloadDummyFile(): StreamedResponse {
        $filename = "dummy_products.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        return response()->stream(function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Product Name', 'Product Unique ID', 'Product Image', 'Description', 'Price', 'Tags', 'Product Link']);

            // Sample products
            for ($i = 1; $i <= 1000; $i++) {
                fputcsv($file, [
                    "Product {$i}",
                    "P100{$i}",
                    "https://example.com/image{$i}.jpg",
                    "Description for product {$i}",
                    rand(10, 100),
                    "tag{$i},tag{$i}a",
                    "https://example.com/product{$i}"
                ]);
            }

            fclose($file);
        }, 200, $headers);
    }

    // /**
    //  * Handle MSME CSV/Excel upload
    //  */
    // public function uploadProducts(Request $request) {
    //     // dd('here');
    //     $validator = Validator::make($request->all(), [
    //         // 'chatbot_id' => 'required|integer|exists:chatbots,id',
    //         'files' => 'required|file|mimes:csv,xls,xlsx|max:102400' // 100MB in KB
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     // dd($request->all());

    //     $createChatbot = Chatbot::create([
    //         'user_id' => Auth::user()->id,
    //         'name' => 'MSME Bot ' . time(),
    //         'description' => 'Auto-generated bot for MSME product upload',
    //         'platform' => '2',
    //     ]);

    //     $chatbotId = $createChatbot->id;
    //     $file = $request->file('files');

    //     // Generate unique stored file name
    //     $originalName = $file->getClientOriginalName();
    //     $ext = strtolower($file->getClientOriginalExtension());
    //     $safeName = pathinfo($originalName, PATHINFO_FILENAME);
    //     $storedName = $safeName . '_' . time() . '_' . Str::random(6) . '.' . $ext;
    //     $storePath = "public/msme_uploads/{$chatbotId}/";
    //     $storedPath = $file->storeAs($storePath, $storedName);

    //     // Save original file record
    //     $fileRecord = File::create([
    //         'chatbot_id' => $chatbotId,
    //         'file_name' => $originalName,
    //         'file_path' => $storedPath,
    //         'file_type' => $ext,
    //         'file_size' => $file->getSize()
    //     ]);

    //     // Estimate total rows (for progress tracking)
    //     $totalRows = $this->estimateRowCount(storage_path('app/' . $storedPath), $ext);

    //     // Create UploadJob
    //     $uploadUuid = (string) Str::uuid();
    //     $job = UploadJob::create([
    //         'upload_uuid' => $uploadUuid,
    //         'chatbot_id' => $chatbotId,
    //         'file_record_id' => $fileRecord->id,
    //         'total_rows' => $totalRows,
    //         'processed_rows' => 0,
    //         'inserted' => 0,
    //         'updated' => 0,
    //         'status' => 'queued',
    //     ]);

    //     // Queue the import (ProductsImport handles chunked reading & JSON snapshot)
    //     $import = new ProductsImport($chatbotId, $uploadUuid);
    //     Excel::queueImport($import, $storedPath, 'local');

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'File uploaded and import queued successfully.',
    //         'upload_uuid' => $uploadUuid,
    //         'job_id' => $job->id
    //     ], 200);
    // }

    // /**
    //  * Poll upload status
    //  */
    // public function uploadStatus(string $uploadUuid) {
    //     $job = UploadJob::where('upload_uuid', $uploadUuid)->first();

    //     if (!$job) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Upload job not found.'
    //         ], 404);
    //     }

    //     // Optional: fetch JSON snapshot if done
    //     $snapshot = null;
    //     if ($job->status === 'done') {
    //         $json = \App\Models\ProductJson::where('chatbot_id', $job->chatbot_id)->first();
    //         $snapshot = $json ? $json->products : null;
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'data' => [
    //             'status' => $job->status,
    //             'total_rows' => $job->total_rows,
    //             'processed_rows' => $job->processed_rows,
    //             'inserted' => $job->inserted,
    //             'updated' => $job->updated,
    //             'error' => $job->error,
    //             'snapshot' => $snapshot
    //         ]
    //     ]);
    // }

    // /**
    //  * Estimate total rows for progress tracking
    //  */
    // private function estimateRowCount(string $fullPath, string $ext): int {
    //     try {
    //         if ($ext === 'csv') {
    //             $count = 0;
    //             $f = fopen($fullPath, 'r');
    //             while (!feof($f)) {
    //                 $line = fgets($f);
    //                 if ($line !== false) $count++;
    //             }
    //             fclose($f);
    //             return max(0, $count - 1); // minus header
    //         } else {
    //             // XLS/XLSX: read highest row of first sheet
    //             $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($fullPath);
    //             $reader->setReadDataOnly(true);
    //             $spreadsheet = $reader->load($fullPath);
    //             $sheet = $spreadsheet->getActiveSheet();
    //             return max(0, $sheet->getHighestDataRow() - 1);
    //         }
    //     } catch (\Exception $e) {
    //         return 0;
    //     }
    // }

    // public function downloadDummyFile() {
    //     $dummyData = [
    //         ['Product Name', 'Product Unique ID', 'Product Image', 'Description', 'Price', 'Tags', 'Product Link'],
    //         ['Smartphone X100', 'P001', 'https://example.com/images/smartphone.jpg', 'Latest smartphone with 6GB RAM', 299.99, 'smartphone,electronics', 'https://example.com/product/P001'],
    //         ['Wireless Headphones W200', 'P002', 'https://example.com/images/headphones.jpg', 'Noise-cancelling wireless headphones', 99.50, 'headphones,audio', 'https://example.com/product/P002'],
    //         ['Gaming Laptop G500', 'P003', 'https://example.com/images/laptop.jpg', 'High performance laptop for gaming', 1200.00, 'laptop,gaming', 'https://example.com/product/P003'],
    //         ['Smartwatch S10', 'P004', 'https://example.com/images/smartwatch.jpg', 'Fitness smartwatch with heart rate monitor', 149.75, 'smartwatch,wearable', 'https://example.com/product/P004'],
    //         ['Digital Camera D300', 'P005', 'https://example.com/images/camera.jpg', '24MP digital camera with 4K video', 450.00, 'camera,photography', 'https://example.com/product/P005'],
    //     ];

    //     // Using Laravel-Excel
    //     return \Maatwebsite\Excel\Facades\Excel::download(
    //         new class($dummyData) implements \Maatwebsite\Excel\Concerns\FromArray {
    //             protected $data;
    //             public function __construct($data) {
    //                 $this->data = $data;
    //             }
    //             public function array(): array {
    //                 return $this->data;
    //             }
    //         },
    //         'dummy_products.xlsx'
    //     );
    // }

    // /**
    //  * Upload endpoint: Save file, create UploadJob, queue import (Maatwebsite)
    //  */
    // public function uploadProducts(Request $request) {
    //     $validator = Validator::make($request->all(), [
    //         'chatbot_id' => 'required|integer|exists:chatbots,id',
    //         'file' => 'required|file|mimes:csv,xls,xlsx|max:102400' // 100MB in KB
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
    //     }

    //     $chatbotId = (int) $request->input('chatbot_id');

    //     $file = $request->file('file');
    //     $originalName = $file->getClientOriginalName();
    //     $ext = strtolower($file->getClientOriginalExtension());
    //     $safeName = pathinfo($originalName, PATHINFO_FILENAME);
    //     $storedName = $safeName . '_' . time() . '_' . Str::random(6) . '.' . $ext;
    //     $storePath = "public/msme_uploads/{$chatbotId}/";
    //     $storedPath = $file->storeAs($storePath, $storedName);

    //     // Save file record
    //     $fileRecord = File::create([
    //         'chatbot_id' => $chatbotId,
    //         'file_name' => $originalName,
    //         'file_path' => $storedPath,
    //         'file_type' => $ext,
    //         'file_size' => $file->getSize()
    //     ]);

    //     // Count rows roughly (for CSV we can stream count; for xlsx it's heavier)
    //     $totalRows = $this->estimateRowCount(storage_path('app/' . $storedPath), $ext);

    //     // Create UploadJob
    //     $uploadUuid = (string) Str::uuid();
    //     $job = UploadJob::create([
    //         'upload_uuid' => $uploadUuid,
    //         'chatbot_id' => $chatbotId,
    //         'file_record_id' => $fileRecord->id,
    //         'total_rows' => $totalRows,
    //         'processed_rows' => 0,
    //         'inserted' => 0,
    //         'updated' => 0,
    //         'status' => 'queued',
    //     ]);

    //     // Queue the import using maatwebsite excel queue import
    //     // ProductsImport implements ShouldQueue and WithChunkReading
    //     $import = new ProductsImport($chatbotId, $uploadUuid);

    //     // Use queueImport to ensure chunks are queued (Maatwebsite)
    //     Excel::queueImport($import, $storedPath, 'local'); // disk 'local' since stored under storage/app

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'File uploaded and import queued.',
    //         'upload_uuid' => $uploadUuid,
    //         'job_id' => $job->id
    //     ], 200);
    // }

    // /**
    //  * Simple status endpoint for frontend polling
    //  */
    // public function uploadStatus($upload_uuid) {
    //     $job = UploadJob::where('upload_uuid', $upload_uuid)->first();
    //     if (!$job) {
    //         return response()->json(['success' => false, 'message' => 'Job not found'], 404);
    //     }

    //     // If status done, build final JSON snapshot if not already
    //     if ($job->status === 'done') {
    //         $json = ProductJson::where('chatbot_id', $job->chatbot_id)->first();
    //         $snapshot = $json ? $json->products : null;
    //     } else {
    //         $snapshot = null;
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'data' => [
    //             'status' => $job->status,
    //             'total_rows' => $job->total_rows,
    //             'processed_rows' => $job->processed_rows,
    //             'inserted' => $job->inserted,
    //             'updated' => $job->updated,
    //             'error' => $job->error,
    //             'snapshot' => $snapshot
    //         ]
    //     ]);
    // }

    // /**
    //  * Utility: estimate row count for CSV (fast) or XLSX (best-effort)
    //  */
    // private function estimateRowCount($fullPath, $ext) {
    //     try {
    //         if ($ext === 'csv') {
    //             $count = 0;
    //             $f = fopen($fullPath, 'r');
    //             while (!feof($f)) {
    //                 $line = fgets($f);
    //                 if ($line !== false) $count++;
    //             }
    //             fclose($f);
    //             // subtract header row
    //             return max(0, $count - 1);
    //         } else {
    //             // for xls/xlsx, attempt to read with PhpSpreadsheet but don't load fully:
    //             $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($fullPath);
    //             $reader->setReadDataOnly(true);
    //             $spreadsheet = $reader->load($fullPath);
    //             $sheet = $spreadsheet->getActiveSheet();
    //             return max(0, $sheet->getHighestDataRow() - 1);
    //         }
    //     } catch (\Exception $ex) {
    //         return 0;
    //     }
    // }
}
