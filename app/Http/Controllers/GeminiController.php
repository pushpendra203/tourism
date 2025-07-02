<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiController extends Controller
{
public function ask(Request $request)
{
    $message = $request->input('message');
    if (!$message) {
        return response()->json(['reply' => 'Please enter a question.']);
    }

    $apiKey = env('GEMINI_API_KEY');
    $apiUrl = "https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key={$apiKey}";

    try {
        $response = Http::post($apiUrl, [
            'contents' => [[
                'parts' => [['text' => $message]]
            ]]
        ]);

        if ($response->successful()) {
            $reply = $response->json('candidates.0.content.parts.0.text');
            return response()->json(['reply' => $reply]);
        } else {
            Log::error('Gemini API error', ['status' => $response->status(), 'body' => $response->body()]);
            return response()->json(['reply' => 'Gemini API error: ' . $response->status()]);
        }
    } catch (\Exception $e) {
        Log::error('Gemini Exception', ['error' => $e->getMessage()]);
        return response()->json(['reply' => 'Something went wrong.']);
    }
}
}