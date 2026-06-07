<?php

namespace App\Services;

use App\Support\AiConfig;
use Illuminate\Support\Facades\Log;

class AiCompletionService
{
    /**
     * @param  array<int, array{role: string, content: string}>  $messages
     * @return array{ok: true, content: string, provider: string}|array{ok: false, error: string}
     */
    public function complete(array $messages, int $maxTokens = 2048, ?string $model = null, bool $jsonMode = false): array
    {
        $provider = AiConfig::primaryChatProvider();
        if ($provider === null) {
            return ['ok' => false, 'error' => 'No AI chat provider is enabled and configured.'];
        }

        return match ($provider) {
            'openai' => $this->completeOpenAiCompatible(
                'https://api.openai.com/v1/chat/completions',
                AiConfig::openaiApiKey(),
                $model ?: AiConfig::openaiModel(),
                $messages,
                $maxTokens,
                $jsonMode,
                'openai'
            ),
            'deepseek' => $this->completeOpenAiCompatible(
                'https://api.deepseek.com/v1/chat/completions',
                AiConfig::deepseekApiKey(),
                $model ?: AiConfig::deepseekModel(),
                $messages,
                $maxTokens,
                $jsonMode,
                'deepseek'
            ),
            'custom' => $this->completeOpenAiCompatible(
                rtrim(AiConfig::customBaseUrl(), '/').'/chat/completions',
                AiConfig::customApiKey(),
                $model ?: AiConfig::customModel(),
                $messages,
                $maxTokens,
                $jsonMode,
                'custom'
            ),
            'gemini' => $this->completeGemini(
                $model ?: AiConfig::geminiModel(),
                $messages,
                $maxTokens,
                $jsonMode
            ),
            default => ['ok' => false, 'error' => 'Unsupported AI provider: '.$provider],
        };
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $messages
     * @return array{ok: true, content: string, provider: string}|array{ok: false, error: string}
     */
    private function completeOpenAiCompatible(
        string $endpoint,
        string $apiKey,
        string $model,
        array $messages,
        int $maxTokens,
        bool $jsonMode,
        string $provider
    ): array {
        if (trim($apiKey) === '') {
            return ['ok' => false, 'error' => ucfirst($provider).' API key is not configured.'];
        }

        $payload = [
            'model' => $model,
            'messages' => $messages,
            'max_tokens' => max(256, min(8192, $maxTokens)),
            'temperature' => 0.3,
        ];
        if ($jsonMode) {
            $payload['response_format'] = ['type' => 'json_object'];
        }

        $response = $this->postJson($endpoint, [
            'Content-Type: application/json',
            'Authorization: Bearer '.$apiKey,
        ], $payload);

        if (! ($response['ok'] ?? false)) {
            return $response;
        }

        $body = $response['body'];
        if ($jsonMode && isset($body->error)) {
            unset($payload['response_format']);
            $response = $this->postJson($endpoint, [
                'Content-Type: application/json',
                'Authorization: Bearer '.$apiKey,
            ], $payload);
            if (! ($response['ok'] ?? false)) {
                return $response;
            }
            $body = $response['body'];
        }

        if (! is_object($body)) {
            return ['ok' => false, 'error' => 'No response from '.$provider.'.'];
        }
        if (isset($body->error)) {
            $msg = is_object($body->error) ? (string) ($body->error->message ?? 'API error') : (string) $body->error;

            return ['ok' => false, 'error' => ucfirst($provider).' API error: '.$msg];
        }

        $content = trim((string) ($body->choices[0]->message->content ?? ''));
        if ($content === '') {
            return ['ok' => false, 'error' => 'Empty response from '.$provider.'.'];
        }

        return ['ok' => true, 'content' => $content, 'provider' => $provider];
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $messages
     * @return array{ok: true, content: string, provider: string}|array{ok: false, error: string}
     */
    private function completeGemini(string $model, array $messages, int $maxTokens, bool $jsonMode): array
    {
        $apiKey = AiConfig::geminiApiKey();
        if (trim($apiKey) === '') {
            return ['ok' => false, 'error' => 'Gemini API key is not configured.'];
        }

        $contents = [];
        foreach ($messages as $message) {
            $role = ($message['role'] ?? 'user') === 'assistant' ? 'model' : 'user';
            $contents[] = [
                'role' => $role,
                'parts' => [['text' => (string) ($message['content'] ?? '')]],
            ];
        }

        $payload = [
            'contents' => $contents,
            'generationConfig' => [
                'maxOutputTokens' => max(256, min(8192, $maxTokens)),
                'temperature' => 0.3,
            ],
        ];
        if ($jsonMode) {
            $payload['generationConfig']['responseMimeType'] = 'application/json';
        }

        $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/'
            .rawurlencode($model).':generateContent?key='.urlencode($apiKey);

        $response = $this->postJson($endpoint, ['Content-Type: application/json'], $payload);
        if (! ($response['ok'] ?? false)) {
            return $response;
        }

        $body = $response['body'];
        if (! is_object($body)) {
            return ['ok' => false, 'error' => 'No response from Gemini.'];
        }
        if (isset($body->error)) {
            $msg = is_object($body->error) ? (string) ($body->error->message ?? 'API error') : (string) $body->error;

            return ['ok' => false, 'error' => 'Gemini API error: '.$msg];
        }

        $content = trim((string) ($body->candidates[0]->content->parts[0]->text ?? ''));
        if ($content === '') {
            return ['ok' => false, 'error' => 'Empty response from Gemini.'];
        }

        return ['ok' => true, 'content' => $content, 'provider' => 'gemini'];
    }

    /**
     * @return array{ok: true, body: object}|array{ok: false, error: string}
     */
    private function postJson(string $endpoint, array $headers, array $payload): array
    {
        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 120,
        ]);

        $raw = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($errno) {
            Log::warning('AI completion request failed', ['endpoint' => $endpoint, 'error' => $error]);

            return ['ok' => false, 'error' => 'AI request failed: '.$error];
        }

        $decoded = json_decode((string) $raw);
        if ($status >= 400) {
            $msg = is_object($decoded) && isset($decoded->error)
                ? (is_object($decoded->error) ? ($decoded->error->message ?? 'HTTP '.$status) : $decoded->error)
                : 'HTTP '.$status;

            return ['ok' => false, 'error' => (string) $msg];
        }

        return ['ok' => true, 'body' => is_object($decoded) ? $decoded : (object) []];
    }
}
