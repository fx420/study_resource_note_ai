<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Models\PromptTemplate;
use App\Helpers\PromptTemplateStore;
use App\Services\OpenRouterService;
use App\Helpers\StudyAgentTemplateStore;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ChatController extends Controller
{
    protected OpenRouterService $or;

    public function __construct(OpenRouterService $or)
    {
        $this->or = $or;
    }

    public function createSession(Request $r)
    {
        $data = $r->validate([
            'education_level' => 'required|string',
            'course'          => 'required|string',
            'subject'         => 'required|string',
            'topic'           => 'required|string',
            'prior_knowledge' => 'required|string',
            'learning_goal'   => 'required|string',
            'note_level'      => 'required|integer|min:1|max:5',
            'examples_count'  => 'required|integer|min:0',
            'content_format'  => 'required|string|in:text,bullet,table',
            'mode'            => 'required|string|in:direct,prompt',
            'prompt'          => 'nullable|string|max:2000',
            'file'            => 'nullable|file|mimes:txt,pdf,docx|max:5120',
        ]);

        $filePaths = [];
        $fileSnippet = null;
        if ($r->hasFile('file')) {
            $stored = $r->file('file')->store('uploads', 'public'); 
            $filePaths[] = storage_path('app/public/' . $stored);

            try {
                $fileSnippet = substr(file_get_contents($r->file('file')->getRealPath()), 0, 2000);
            } catch (\Throwable $e) {
                $fileSnippet = null;
            }
        }

        $template = [
            'course'   => $data['course'],
            'topic'    => $data['topic'],
            'mode'     => $data['mode'],
            'prompt'   => $data['mode'] === 'prompt' ? $data['prompt'] : null,
            'metadata' => [
                'education_level' => $data['education_level'],
                'prior_knowledge' => $data['prior_knowledge'],
                'learning_goals'  => $data['learning_goal'],
                'note_level'      => $data['note_level'],
                'example_count'   => $data['examples_count'],
                'preferred_format'=> $data['content_format'],
            ],
        ];

        $subjectSlug = Str::slug($data['subject'], '-');
        $templatesDir = base_path('study_resource_note_ai/study_agent/templates');
        if (! File::exists($templatesDir)) {
            File::makeDirectory($templatesDir, 0755, true);
        }
        $subjectFile = $templatesDir . DIRECTORY_SEPARATOR . $subjectSlug . '.json';
        $existing = [];
        if (File::exists($subjectFile)) {
            $existing = json_decode(File::get($subjectFile), true) ?: [];
        }

        $existing[] = [
            'type'       => 'template_saved',
            'created_at' => now()->toDateTimeString(),
            'template'   => $template,
        ];
        File::put($subjectFile, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        PromptTemplate::updateOrCreate(
            [
                'course'  => $data['course'],
                'subject' => $data['subject'],
                'topic'   => $data['topic'],
            ],
            [
                'metadata' => $template['metadata'],
                'mode'     => $template['mode'],
                'prompt'   => $template['prompt'] ?? null,
            ]
        );

        $session = ChatSession::create([
            'user_id'         => auth()->id(),
            'title'           => Str::limit($data['topic'], 50),
            'education_level' => $data['education_level'],
            'course'          => $data['course'],
            'subject'         => $data['subject'],
            'topic'           => $data['topic'],
            'prior_knowledge' => $data['prior_knowledge'],
            'learning_goal'   => $data['learning_goal'],
            'difficulty'      => $data['note_level'],
            'examples_count'  => $data['examples_count'] ?? 0,
            'content_format'  => $data['content_format'],
            'mode'            => $data['mode'],
        ]);

        $metadataLines = [
            "Education Level: {$data['education_level']}",
            "Course:          {$data['course']}",
            "Subject:         {$data['subject']}",
            "Topic:           {$data['topic']}",
            "Prior Knowledge: {$data['prior_knowledge']}",
            "Learning Goal:   {$data['learning_goal']}",
            "Difficulty:      {$data['note_level']}",
            sprintf("Examples Count:  %s", $data['examples_count'] ?? 0),
            "Format:          {$data['content_format']}",
            "Mode:            {$data['mode']}",
        ];
        $session->messages()->create([
            'sender'  => 'user',
            'message' => implode("\n", $metadataLines),
        ]);

        $systemContext = implode("\n", $metadataLines);

        if ($data['mode'] === 'prompt' && ! empty($data['prompt'])) {
            $userPrompt = $data['prompt'];
        } else {
            $userPrompt = "Generate study notes for the topic '{$data['topic']}' in {$data['course']}."
                . " Use the following metadata: Education level: {$data['education_level']}; Prior knowledge: {$data['prior_knowledge']}; Learning goal: {$data['learning_goal']}; Difficulty level: {$data['note_level']}; Examples: {$data['examples_count']}; Format: {$data['content_format']}."
                . " Output structure: 1) Overview (paragraph), 2) Key Points (point form), 3) Examples (point form), 4) Summary (paragraph).";
            if ($fileSnippet) {
                $userPrompt .= "\n\nFile excerpt:\n" . $fileSnippet;
            }
        }

        try {
            $reply = $this->or->chat([
                ['role' => 'system', 'content' => 'You are a helpful study assistant.'],
                ['role' => 'system', 'content' => $systemContext],
                ['role' => 'user',   'content' => $userPrompt],
            ]);
            Log::info('[createSession] AI replied: ' . Str::limit($reply, 200));

            if (! trim($reply)) {
                throw new \Exception('Empty AI response');
            }
        } catch (\Exception $e) {
            Log::error('AI chat error in createSession: ' . $e->getMessage());
            $reply = "⚠️ Sorry, I couldn't reach the AI right now. Please try again.";
        }

        $session->messages()->create([
            'sender'  => 'system',
            'message' => $reply,
        ]);

        $record = [
            'type'        => 'conversation',
            'created_at'  => now()->toDateTimeString(),
            'user_id'     => auth()->id(),
            'course'      => $data['course'],
            'topic'       => $data['topic'],
            'mode'        => $data['mode'],
            'metadata'    => $template['metadata'],
            'user_prompt' => $userPrompt,
            'ai_response' => $reply,
            'files'       => $filePaths,
        ];

        $existing = json_decode(File::get($subjectFile), true) ?: [];
        $existing[] = $record;
        File::put($subjectFile, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        if ($r->wantsJson() || $r->ajax()) {
            return response()->json([
                'session_id' => $session->id,
                'reply'      => $reply,
                'redirect'   => route('chat.session.show', $session),
            ]);
        }

        return redirect()->route('chat.session.show', $session);
    }

    public function showSession(ChatSession $session)
    {
        // $this->authorize('view', $session);

        $session->load(['messages' => function($q) {
            $q->orderBy('created_at');
        }]);

        return view('chat.show', compact('session'));
    }

    public function showCreateForm()
    {
        // Optionally you could filter by course & subject if those are known
        $templates = PromptTemplate::latest()->limit(10)->get();

        return view('chat.create', compact('templates'));
    }

    public function submitSession(Request $r, ChatSession $session)
    {
        $this->authorize('view', $session);

        $r->validate([
            'prompt' => 'nullable|string|max:1000',
            'file'   => 'nullable|file|mimes:txt,pdf,docx|max:5120',
        ]);

        $userMsg = $r->input('prompt', '');
        if ($r->hasFile('file')) {
            $txt = substr(file_get_contents($r->file('file')->getRealPath()), 0, 2000);
            $userMsg .= "\n\n[File content]\n{$txt}";
        }

        $session->messages()->create([
            'sender'  => 'user',
            'message' => $userMsg,
        ]);

        $messages = [
            ['role'=>'system','content'=>'You are a helpful study assistant.'],
            ['role'=>'system','content'=>"Education Level: {$session->education_level}"],
            ['role'=>'system','content'=>"Course: {$session->course}"],
            ['role'=>'system','content'=>"Subject: {$session->subject}"],
            ['role'=>'system','content'=>"Topic: {$session->topic}"],
            ['role'=>'system','content'=>"Prior Knowledge: {$session->prior_knowledge}"],
            ['role'=>'system','content'=>"Learning Goal: {$session->learning_goal}"],
            ['role'=>'system','content'=>"Difficulty Level: {$session->difficulty}"],
            ['role'=>'system','content'=>"Examples Count: {$session->examples_count}"],
            ['role'=>'system','content'=>"Content Format: {$session->content_format}"],
            ['role'=>'system','content'=>"Mode: {$session->mode}"],
        ];

        foreach ($session->messages()->orderBy('created_at')->get() as $msg) {
            $messages[] = [
                'role'    => $msg->sender === 'user' ? 'user' : 'assistant',
                'content' => $msg->message,
            ];
        }

        try {
            $reply = $this->or->chat($messages);
            $session->messages()->create([
                'sender'  => 'system',
                'message' => $reply,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'reply' => "⚠️ AI service error: " . $e->getMessage()
            ], 500);
        }

        return response()->json(['reply' => $reply]);
    }

    public function history()
    {
        $sessions = ChatSession::where('user_id', auth()->id())
                               ->with('messages')
                               ->latest()
                               ->get();
        return view('chat.history', compact('sessions'));
    }
}
