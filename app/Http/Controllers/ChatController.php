<?php

namespace App\Http\Controllers;

use App\Services\ChatGPTService;
use App\Models\Conversation;

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
        $systemMessage = ['role' => 'system', 'content' => 'You are a helpful assistant.'];
        $messages = [$systemMessage, $userMessage];
        $response = $chatGPT->sendMessage($messages);

        $assistantReply = [
            'role' => 'assistant',
            'content' => $response['choices'][0]['message']['content'],
        ];
        $conversationMessages = [
            'user' => $userMessage['content'],
            'assistant' => $assistantReply['content'],
        ];
        Conversation::create([
            'messages' => json_encode($conversationMessages),
        ]);
        return response()->json(['reply' => $assistantReply['content']]);
    }

}
