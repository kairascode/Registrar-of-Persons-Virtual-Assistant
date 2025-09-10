<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Services\AiService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AssistantController extends Controller
{
    protected $aiService;

    public function __construct(AiService $aiService)
    {
        $this->aiService = $aiService;
    }

     public function index()
    {
        return view('assistant.index');
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userMessage = $request->input('message');
        $sessionId = $request->session()->get('session_id', Str::uuid()->toString());
        $request->session()->put('session_id', $sessionId);

        $response = $this->aiService->getResponse($userMessage);

        Conversation::create([
            'user_message' => $userMessage,
            'assistant_response' => $response,
            'session_id' => $sessionId,
        ]);

        return response()->json([
            'message' => $userMessage,
            'response' => $response,
        ]);
    }

    public function history(Request $request)
    {
        $sessionId = $request->session()->get('session_id');
        $conversations = Conversation::where('session_id', $sessionId)->get();
        return response()->json($conversations);
    }
    
}
