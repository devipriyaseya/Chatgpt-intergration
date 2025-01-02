<?php

namespace App\Http\Controllers;

use App\Services\ChatGPTService;
use App\Models\Conversation;
use OpenAI\Laravel\Facades\OpenAI;

use Illuminate\Http\Request;

class ChatController extends Controller
{

    public function index()
    {
        return view('index');
    }



    public function sendMessage(Request $request, ChatGPTService $chatGPT)
    {
        $userMessage = ['role' => 'user', 'content' => $request->message];
        $conversationId = $request->session()->get('conversation_id');
        // \Log::info($conversationId);
        $conversationHistory = [];
        $conversation = null;

        if ($conversationId) {
            $conversation = Conversation::find($conversationId);
            if ($conversation) {
                $conversationHistory = json_decode($conversation->messages, true);
            }
        }

        if (empty($conversationHistory)) {
            $conversationHistory[] = ['role' => 'system', 'content' => 'You are a helpful assistant.'];
        }

        $conversationHistory[] = $userMessage;

        $response = $chatGPT->sendMessage($conversationHistory);
        $assistantReply = [
            'role' => 'assistant',
            'content' => $response['choices'][0]['message']['content'],
        ];

        $conversationHistory[] = $assistantReply;

        if ($conversation) {
            // Update existing conversation
            $conversation->update([
                'messages' => json_encode($conversationHistory),
            ]);
        } else {
            // Create a new conversation
            $conversation = Conversation::create([
                'messages' => json_encode($conversationHistory),
            ]);
            $request->session()->put('conversation_id', $conversation->id);
        }

        return response()->json([
            'message' => $assistantReply['content'],
            'conversation_id' => $conversation->id,
        ]);
    }
}
