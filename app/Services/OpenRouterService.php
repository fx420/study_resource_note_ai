<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenRouterService
{
    protected $base = 'https://openrouter.ai/api/v1';

    public function chat(array $messages, string $model = 'deepseek/deepseek-r1:free')
    {
        $resp = Http::withToken(config('services.openrouter.key'))
            ->post("{$this->base}/chat/completions", [
                'model'    => $model,
                'messages' => $messages,
            ]);

        if ($resp->ok()) {
            return $resp->json('choices.0.message.content');
        }

        throw new \Exception("OpenRouter error: ".$resp->body());
    }
}
