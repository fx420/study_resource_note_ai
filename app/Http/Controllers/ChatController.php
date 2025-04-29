<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $request->validate([
          'prompt' => 'nullable|string|max:1000',
          'file'   => 'nullable|file|mimes:txt,pdf,docx|max:5120',
        ]);

        $messages = [
          ['role'=>'system','content'=>'You are a helpful study assistant.'],
          ['role'=>'user','content'=>$request->input('prompt','')],
        ];

        if ($file = $request->file('file')) {
          $txt = substr(file_get_contents($file->getRealPath()), 0, 2000);
          $messages[] = ['role'=>'user','content'=>"[File content]\n{$txt}"];
        }

        try {
          $reply = $this->or->chat($messages);
        } catch (\Exception $e) {
          return response()->json(['reply'=>"⚠️ AI service error: ".$e->getMessage()], 500);
        }

        return response()->json(['reply'=>$reply]);
    }
}
