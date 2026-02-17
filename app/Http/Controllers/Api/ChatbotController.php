<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Chatbot\ChatbotService;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
public function chatbot(Request $request)
{
    $request->validate([
        'message' => 'required|string|max:1000'
    ]);

    $reply = (new ChatbotService())->respond($request->message);

    return response()->json([
        'reply' => $reply
    ]);
}
}