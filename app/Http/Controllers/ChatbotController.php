<?php

namespace App\Http\Controllers;

use App\Models\Chatbot;
use Illuminate\Http\Request;
use App\Http\Requests\ChatbotRequest;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\ChatbotController;


class ChatbotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $chatbots = Chatbot::all();
        return view('chatbots.index', compact('chatbots'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('chatbots.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ChatbotRequest $request)
    {
        // dd($request->all());
        $chatbot = Chatbot::create($request->all());

        // Redirect back to index page with success message
        return redirect()
            ->route('chatbots.index')
            ->with('success', 'Chatbot created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Chatbot $chatbot)
    {
        return view('chatbots.show', compact('chatbot'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($encryptedId)
    {
        $chatbot = Chatbot::findOrFail(Crypt::decryptString($encryptedId));
        return view('chatbots.edit', compact('chatbot'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ChatbotRequest $request, $encryptedId)
    {
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
    public function destroy($encryptedId)
    {
        $chatbot = Chatbot::findOrFail(Crypt::decryptString($encryptedId));
        $chatbot->delete();

        return redirect()
            ->route('chatbots.index')
            ->with('success', 'Chatbot deleted successfully!');
    }

    /**
     * Get chatbot list for DataTables (AJAX).
     */

    public function getChatbotList(Request $request)
    {
        $chatbots = Chatbot::select(['id', 'name', 'description', 'created_at', 'platform']);

        return DataTables::of($chatbots)
            ->addIndexColumn() // generates serial number DT_RowIndex
            ->addColumn('action', function($row){
                $encryptedId = Crypt::encryptString($row->id);
                $edit = '<a href="'.route('chatbots.edit', $encryptedId).'" class="btn btn-sm btn-primary">Edit</a>';
               $detailsForm = '<a href="'.route('chatbots.details', $encryptedId).'" class="btn btn-sm btn-info">Details</a>';
                $build_chatbot = '<a href="'.route('chatbots.build', $encryptedId).'" class="btn btn-sm btn-success">Build</a>';
                 $delete = '<form method="POST" action="'.route('chatbots.destroy', $encryptedId).'" style="display:inline-block;">
                            '.csrf_field().method_field('DELETE').'
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</button>
                        </form>';
                return $edit . ' ' . $delete . ' ' . $build_chatbot . ' ' . $detailsForm;
                
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Build chatbot react.
     */

    public function buildChatbot($encryptedId)
    {
        $chatbot = Chatbot::findOrFail(Crypt::decryptString($encryptedId));
        return view('chatbots.build_chatbot', compact('chatbot'));
    }

 public function details($encryptedId)
    {
        $chatbot = Chatbot::findOrFail(Crypt::decryptString($encryptedId));
        return view('chatbots.details', compact('chatbot'));
    }

}
