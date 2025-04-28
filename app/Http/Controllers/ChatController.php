<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function submit(Request $request)
    {
        // 1) validate incoming
        $data = $request->validate([
            'prompt' => 'nullable|string|max:1000',
            'file'   => 'nullable|file|mimes:pdf,txt,docx|max:5120',
        ]);

        // 2) build full prompt
        $fullPrompt = trim($data['prompt'] ?? '');

        if ($file = $request->file('file')) {
            $contents = file_get_contents($file->getRealPath());
            $fullPrompt .= "\n\n[File content]\n" . substr($contents, 0, 2000);
        }

        // 3) send to OpenAI
        try {
            $response = Http::withOptions(['verify' => false])
                ->withToken(env('OPENAI_API_KEY'))
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model'       => 'gpt-3.5-turbo',
                    'messages'    => [
                        ['role'=>'system','content'=>'You are a helpful study assistant.'],
                        ['role'=>'user'  ,'content'=>$fullPrompt],
                    ],
                    'temperature' => 0.7,
                ]);

            if (! $response->successful()) {
                Log::error("[OpenAI] HTTP " . $response->status() .": ".$response->body());
                return response()->json(['reply'=>"⚠️ AI service returned an error. Please try again later."], 500);
            }

            $reply = $response['choices'][0]['message']['content'] ?? 'No answer.';
        } catch (\Exception $e) {
            Log::error("[ChatController] ".$e->getMessage());
            return response()->json(['reply'=>"⚠️ AI service returned an error. Please try again later."], 500);
        }

        // 4) return JSON
        return response()->json(['reply' => $reply]);
    }
}
