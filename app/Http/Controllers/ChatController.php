<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Note;
use App\Services\OpenRouterService;

class ChatController extends Controller
{
    protected $or;

    public function __construct(OpenRouterService $or)
    {
        $this->or = $or;
    }

    public function submit(Request $request)
    {
        // 1) Validate incoming prompt or file
        $request->validate([
            'prompt' => 'nullable|string|max:1000',
            'file'   => 'nullable|file|mimes:txt,pdf,docx|max:5120',
        ]);

        // 2) Build the chat messages array
        $messages = [
            ['role' => 'system', 'content' => 'You are a helpful study assistant.'],
            ['role' => 'user',   'content' => $request->input('prompt', '')],
        ];

        if ($file = $request->file('file')) {
            $txt = substr(file_get_contents($file->getRealPath()), 0, 2000);
            $messages[] = [
              'role'    => 'user',
              'content' => "[File content]\n{$txt}"
            ];
        }

        // 3) Send to OpenRouter / LLM
        try {
            $reply = $this->or->chat($messages);
        } catch (\Exception $e) {
            return response()->json([
                'reply' => "⚠️ AI service error: " . $e->getMessage()
            ], 500);
        }

        // 4) Persist the generated note
        $title = Str::limit($request->input('prompt', 'Quick Note'), 50);
        Note::create([
            'user_id' => Auth::id(),
            'title'   => $title,
            'content' => $reply,
        ]);

        // 5) Return JSON back to the front-end
        return response()->json(['reply' => $reply]);
    }
}
