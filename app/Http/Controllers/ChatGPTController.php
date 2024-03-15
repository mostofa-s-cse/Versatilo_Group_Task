<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\ChatGptResponse;
use Illuminate\Http\JsonResponse;

class ChatGPTController extends Controller
{
    public function generateResponse(Request $request): JsonResponse
    {
        // Check if the user is authenticated
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Extract the 'search' parameter from the request
        $search = $request->input('search');

        try {
            // Make a request to OpenAI API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer Your-API-Key'
            ])->post("https://api.openai.com/v1/chat/completions", [
                "model" => "gpt-3.5-turbo",
                'messages' => [
                    [
                        "role" => "user",
                        "content" => $search
                    ]
                ],
                'temperature' => 0.5,
                "max_tokens" => 500,
                "top_p" => 1.0,
                "frequency_penalty" => 0.52,
                "presence_penalty" => 0.5,
                "stop" => ["11."],
            ]);

            // Get user ID
            $user = auth()->user()->id;

            // Extract the response data from the API response
            $data = $response['choices'][0]['message'];

            // Create a record of the chat response
            $chatGptResponse = ChatGptResponse::create([
                'response' => $data['content'],
                'user_id' => $user,
                'search' => $search
            ]);

            // Return the response to the client
            return response()->json($response['choices'][0]['message'], 200, array(), JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            // Handle exceptions (e.g., log the error)
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getallHistory()
    {
        try {
            $id = auth()->user()->id;
            $data = ChatGptResponse::where('user_id', $id)
            ->orderBy('id', 'desc')
            ->get();
            return response()->json( [
                'success'=>true,
                'message'=>'Successfully get data',
                'data'=>$data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>$e->getMessage(),
            ], 500);
        }
    }

}
