<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenRouterService
{
    protected string $base = 'https://openrouter.ai/api/v1';

    public function chat(
        array $messages,
        string $model       = 'deepseek/deepseek-r1:free',
        float  $temperature = 0.3,
        int    $maxTokens   = 1024
    ): string {
        $payload = [
            'model'       => $model,
            'messages'    => $messages,
            'temperature' => $temperature,
            'max_tokens'  => $maxTokens,
        ];

        $resp = Http::withToken(config('services.openrouter.key'))
            ->timeout(60)
            ->post("{$this->base}/chat/completions", $payload);

        if ($resp->successful()) {
            return $resp->json('choices.0.message.content', '');
        }

        throw new \Exception("OpenRouter error [{$resp->status()}]: " . $resp->body());
    }
}
