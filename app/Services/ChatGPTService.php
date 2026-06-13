<?php
namespace App\Services;

use App\Support\AiConfig;
use App\Support\HealthTopicSourceCatalog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatGPTService implements AIModel{

    /**
     * Normalize a title to professional title case using AI.
     * Returns null on any failure so callers can use deterministic fallback.
     */
    public function formatTitleCase(string $title): ?string
    {
        $title = trim($title);
        if ($title === '' || mb_strlen($title) > 300) {
            return null;
        }

        if (AiConfig::resolveChatProviderForFeature('title_formatting') === null) {
            return null;
        }

        $guide = 'Rewrite the input as a clean publication/discussion title in English title case. '
            .'Capitalize major words, keep short function words lower-case in the middle (for, and, of, to, in, on, at, by, with, from, a, an, the), '
            .'but always capitalize the first and last word. Preserve acronyms and numbers. '
            .'Do not add or remove meaning. Return only the rewritten title text with no quotes and no explanation.';

        $result = app(AiCompletionService::class)->completeForFeature('title_formatting', [
            ['role' => 'user', 'content' => $guide],
            ['role' => 'user', 'content' => $title],
        ], 120);

        if (! ($result['ok'] ?? false)) {
            return null;
        }

        $content = $result['content'] ?? '';
        if (trim($content) === '') {
            return null;
        }

        $out = trim((string) $content);
        $out = preg_replace('/^```[a-zA-Z]*\s*/', '', $out);
        $out = preg_replace('/```\s*$/', '', $out);
        $out = trim(strip_tags($out), " \t\n\r\0\x0B\"'");

        return $out !== '' ? $out : null;
    }

    /**
     * @return array{driver: string, endpoint?: string, headers?: array<int, string>, model?: string, feature?: string, provider?: string}|null
     */
    private function resolveProviderTransport(string $feature, ?string $providerId = null): ?array
    {
        $providers = $providerId !== null
            ? [$providerId]
            : AiConfig::chatProviderFallbackChain($feature);

        foreach ($providers as $provider) {
            $transport = $this->buildProviderTransport($provider, $feature);
            if ($transport !== null) {
                return $transport;
            }
        }

        return null;
    }

    /**
     * @return array{driver: string, endpoint?: string, headers?: array<int, string>, model?: string, feature?: string, provider?: string}|null
     */
    private function buildProviderTransport(string $provider, string $feature): ?array
    {
        if (! AiConfig::providerAvailable($provider)) {
            return null;
        }

        $creds = AiConfig::providerCredentials($provider);
        if ($creds === null) {
            return null;
        }

        if ($creds['driver'] === 'gemini') {
            return [
                'driver' => 'gemini',
                'feature' => $feature,
                'model' => $creds['model'],
                'provider' => $provider,
            ];
        }

        $baseUrl = rtrim($creds['base_url'], '/');
        if ($baseUrl === '' || trim($creds['api_key']) === '' || trim($creds['model']) === '') {
            return null;
        }

        return [
            'driver' => 'openai_compatible',
            'endpoint' => $baseUrl.'/chat/completions',
            'headers' => [
                'Content-Type: application/json',
                'Authorization: Bearer '.$creds['api_key'],
            ],
            'model' => $creds['model'],
            'feature' => $feature,
            'provider' => $provider,
        ];
    }

    private function notConfiguredHtml(string $feature): string
    {
        $features = AiConfig::chatProviderFallbackChain($feature) === []
            ? 'forums, chat, or AI search'
            : $feature;

        return '<div class="alert alert-danger">AI is not configured for this feature. '
            .'An administrator must enable a chat provider (OpenAI, Gemini, DeepSeek, or custom) '
            .'under Admin → Settings → AI integrations and assign it to '.$features.'. '
            .'If keys are only in <code>.env</code>, run <code>php artisan config:cache</code> after updating them.</div>';
    }

    private function providerErrorsHtml(array $errors): string
    {
        $last = (string) (end($errors) ?: '');
        $message = $last;

        if (preg_match('/:\s*(.+)$/s', $last, $m)) {
            $message = trim($m[1]);
        }

        $lower = strtolower($message);
        if (str_contains($lower, 'quota') || str_contains($lower, 'billing') || str_contains($lower, 'insufficient')) {
            return '<div class="alert alert-danger">The configured AI provider has exceeded its quota or billing limit. '
                .'Please ask an administrator to renew the API plan or switch provider in Admin → Settings → AI integrations.</div>';
        }

        if (str_contains($lower, 'api key') || str_contains($lower, 'incorrect api key') || str_contains($lower, 'invalid_api_key')) {
            return '<div class="alert alert-danger">The AI API key is invalid or expired. '
                .'Update it in Admin → Settings → AI integrations or in <code>.env</code> (<code>OPEN_API_KEY</code>), then run <code>php artisan config:cache</code>.</div>';
        }

        if ($message !== '') {
            return '<div class="alert alert-danger">'.htmlspecialchars($message, ENT_QUOTES, 'UTF-8').'</div>';
        }

        return '<div class="alert alert-danger">Could not reach the AI service. Please try again in a moment.</div>';
    }

    private function wrapContentAsOpenAiResponse(string $content): object
    {
        return (object) [
            'choices' => [
                (object) ['message' => (object) ['content' => $content]],
            ],
        ];
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $messages
     */
    private function completeMessagesAsOpenAiResponse(string $feature, array $messages, int $maxTokens = 3096, bool $jsonMode = false): ?object
    {
        $result = app(AiCompletionService::class)->completeForFeatureWithFallback($feature, $messages, $maxTokens, null, $jsonMode);
        if (! ($result['ok'] ?? false)) {
            Log::warning('AI completion failed', ['feature' => $feature, 'error' => $result['error'] ?? 'unknown']);

            return null;
        }

        return $this->wrapContentAsOpenAiResponse((string) ($result['content'] ?? ''));
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $messages
     */
    private function streamMessagesForFeature(string $feature, array $messages, callable $onChunk, int $maxTokens = 3096): void
    {
        $providers = AiConfig::chatProviderFallbackChain($feature);
        if ($providers === []) {
            $onChunk($this->notConfiguredHtml($feature));

            return;
        }

        $errors = [];
        foreach ($providers as $provider) {
            $transport = $this->resolveProviderTransport($feature, $provider);
            if ($transport === null) {
                continue;
            }

            if ($transport['driver'] === 'gemini') {
                $response = $this->completeMessagesAsOpenAiResponse($feature, $messages, $maxTokens);
                $content = $this->extractOpenAiMessageContent($response);
                if ($content !== null && $content !== '') {
                    $onChunk($content);

                    return;
                }
                $errors[] = $provider.': empty response';

                continue;
            }

            $streamError = null;
            $streamBody = '';
            $this->streamOpenAiCompatibleTransport($transport, $messages, $onChunk, $maxTokens, $streamError, $streamBody);
            if ($streamError === null) {
                return;
            }

            $parsed = $this->parseApiErrorMessage($streamBody);
            $errors[] = $provider.': '.($parsed ?: $streamError);
            Log::warning('AI stream provider failed, trying fallback', [
                'feature' => $feature,
                'provider' => $provider,
                'error' => $streamError,
            ]);
        }

        if ($errors !== []) {
            Log::error('AI stream exhausted providers', ['feature' => $feature, 'errors' => $errors]);
        }

        $onChunk($this->providerErrorsHtml($errors));
    }

    private function parseApiErrorMessage(string $raw): ?string
    {
        $raw = trim($raw);
        if ($raw === '') {
            return null;
        }

        $decoded = json_decode($raw, true);
        if (is_array($decoded) && isset($decoded['error'])) {
            $err = $decoded['error'];
            if (is_array($err) && ! empty($err['message'])) {
                return (string) $err['message'];
            }
            if (is_string($err)) {
                return $err;
            }
        }

        return null;
    }

    /**
     * @param  array{driver: string, endpoint?: string, headers?: array<int, string>, model?: string}  $transport
     * @param  array<int, array{role: string, content: string}>  $messages
     */
    private function streamOpenAiCompatibleTransport(
        array $transport,
        array $messages,
        callable $onChunk,
        int $maxTokens,
        ?string &$error,
        ?string &$responseBody = null
    ): void {
        $error = null;
        $responseBody = '';
        $payload = [
            'model' => $transport['model'],
            'messages' => $messages,
            'max_tokens' => $maxTokens,
            'stream' => true,
        ];
        $jsonData = json_encode($payload);
        $headers = $transport['headers'];
        $headers[] = 'Content-Length: '.strlen($jsonData);

        $ch = curl_init($transport['endpoint']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $data) use ($onChunk, &$responseBody) {
            $responseBody .= $data;
            $trim = ltrim($responseBody);
            if ($trim !== '' && $trim[0] === '{') {
                return strlen($data);
            }
            $len = strlen($data);
            if ($len > 0) {
                $this->parseSSELine($data, $onChunk);
            }

            return $len;
        });

        curl_exec($ch);
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            Log::error('AI stream error: '.$error);
            curl_close($ch);

            return;
        }

        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 400) {
            $parsed = $this->parseApiErrorMessage($responseBody);
            $error = $parsed ?: ('HTTP '.$httpCode);

            return;
        }

        if (ltrim($responseBody) !== '' && ltrim($responseBody)[0] === '{') {
            $parsed = $this->parseApiErrorMessage($responseBody);
            if ($parsed !== null) {
                $error = $parsed;
            }
        }
    }

    function prompt($question = null, string $feature = 'chat')
    {
        $systemContent = 'You are to act as a high accuracy content development and reveiw expert,  providing accurate comprehensive summarization and or comparison without being ridiculously brief and not mentioning specific sections in the document but you can still use bullets and headings , comparison and enrichment of content given to you.If Attached content (\'attached_content:<content here>\') contains data, work on that first but ignoring table of contents and unreadble characters,for atatched content remember to mention that the section u are summarising is from the attachment. Make sure you use only factual data to guide and engage. '
            .'If you receive or are asked in form a greeting like Hi or hello, reply with a greeting and what you can offer as help in line with your field. Summaries, always end with a parapgraph to summarise the major points and give a general picture. If the content is short for you to summarise, i.e less than 100 words, make it clear in your the title of the response that it is short and what you are providing is what know about the topic. '
            .'Reject any other questions humbly. Before rejecting, analyse the questions and if it relates your line of work, answer it in that context.If the question is about sex, repond inline with health and only refer them to other sources for additional explicit details. if the comments array contains any, summarise the commments in the comments section, describing what people said with out mentioning names,else don\'t talk about comments at all. Only and only use the given data in your summarisation, don\'t make assumptions. '
            .'Always return responses in raw html format in a div, ignore html,head and body tags, use nice styling especially using  lists,headings and paragraphs, don\'t use any h1 and h2 tags. Use teal color for headings and bold words.For short content given for summarising, always respond saying there\'s not enough content to be summarised,remember to make your summaries rich enough, to atleast aquarter of what you are given but not less. and avoid using background colors. Translate the summary to the summary ;anguage if provided. Only do comparison if it is a comparison question.';

        $messages = [
            ['role' => 'user', 'content' => $systemContent],
            ['role' => 'user', 'content' => (string) $question],
        ];

        $transport = $this->resolveProviderTransport($feature);
        if ($transport === null) {
            return null;
        }

        if ($transport['driver'] === 'gemini') {
            return $this->completeMessagesAsOpenAiResponse($feature, $messages, 3096);
        }

        $payload = [
            'messages' => $messages,
            'model' => $transport['model'],
            'max_tokens' => 3096,
        ];

        return $this->sendRequest($transport['endpoint'], $transport['headers'], $payload);
    }

    
    function summarize($resource,$language=null,$additional_prompt=null){

        $question = "Summarise for me this: ". $resource;
        
        if($additional_prompt)
            $question .= " Pay attention to this: ".$additional_prompt;
       
        $question .= "Don't forget to translate to ".$language." if provided ";

        return $this->prompt($question);

    }

    function compare($resource,$other_resource,$additional_prompt=null){

        $question = "Compare the following two for me : ". $resource ." and ".$other_resource;
        if($additional_prompt)
            $question .= " Pay attention to this: ".$additional_prompt;
        return $this->prompt($question);

    }

    /**
     * Stream completion: call $onChunk(string $content) for each delta.
     * Uses OpenAI stream: true (SSE). Parses data: lines and extracts delta.content.
     */
    public function promptStream(string $question, callable $onChunk, string $feature = 'chat'): void
    {
        $systemContent = "You are to act as a high accuracy content development and review expert, providing accurate comprehensive summarization and or comparison without being ridiculously brief and not mentioning specific sections in the document but you can still use bullets and headings , comparison and enrichment of content given to you.If Attached content ('attached_content:<content here>') contains data, work on that first but ignoring table of contents and unreadable characters,for attached content remember to mention that the section u are summarising is from the attachment. Make sure you use only factual data to guide and engage. If the content is short for you to summarise, i.e less than 100 words, make it clear in the title of the response. Always return responses in raw html format in a div, ignore html,head and body tags, use nice styling especially using lists,headings and paragraphs, don't use h1 and h2 tags. Use teal color for headings and bold words. Avoid using background colors. Translate the summary to the summary language if provided.";

        $this->streamMessagesForFeature($feature, [
            ['role' => 'system', 'content' => $systemContent],
            ['role' => 'user', 'content' => $question],
        ], $onChunk);
    }

    /**
     * Multi-turn chat with streaming (e.g. Khub AI for non-PDF resources).
     *
     * @param  array<int, array{role: string, content: string}>  $messages
     */
    public function chatMessagesStream(array $messages, callable $onChunk, string $feature = 'chat'): void
    {
        $this->streamMessagesForFeature($feature, $messages, $onChunk);
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $messages
     */
    public function chatMessagesComplete(array $messages, string $feature = 'chat'): string
    {
        $result = app(AiCompletionService::class)->completeForFeatureWithFallback($feature, $messages, 3096);
        if ($result['ok'] ?? false) {
            return (string) ($result['content'] ?? '');
        }

        $error = (string) ($result['error'] ?? 'No response from AI.');

        return $this->providerErrorsHtml(['error: '.$error]);
    }

    private function parseSSELine(string $data, callable $onChunk): void
    {
        $lines = explode("\n", $data);
        foreach ($lines as $line) {
            $line = trim($line);
            if (strpos($line, 'data: ') === 0) {
                $json = substr($line, 6);
                if ($json === '[DONE]') {
                    return;
                }
                $decoded = json_decode($json, true);
                if (isset($decoded['choices'][0]['delta']['content'])) {
                    $onChunk($decoded['choices'][0]['delta']['content']);
                }
            }
        }
    }

    private function sendRequest($url, $headers, $body) {
        // Initialize cURL session
        $ch = curl_init($url);
    
        // Convert the body array to JSON format
        $jsonData  = json_encode($body);
        $headers[] ='Content-Length: ' . strlen($jsonData);

        // Set cURL options (match forum summarisation: bounded timeout, reliable POST)
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);

        // Execute cURL request and get the response
        $response = curl_exec($ch);

        // Check for cURL errors
        if ($response === false) {
            Log::error('OpenAI cURL error: '.curl_error($ch));
            $response = null;
        }

        // Close cURL session
        curl_close($ch);

        if ($response !== null && $response !== '') {
            Log::info('OpenAI response received', ['bytes' => strlen($response)]);
        }

        if ($response === null || $response === '') {
            return null;
        }

        return json_decode($response);
    }

    /**
     * Translate UI strings (English values) into a target language via chat completions.
     * Large groups are split into chunks to reduce truncation risk.
     *
     * @param  array<string, string>  $englishKeyed  key => English text
     * @return array{ok: bool, translations?: array<string, string>, error?: string}
     */
    public function translateUiStringBatch(string $targetLanguageLabel, array $englishKeyed): array
    {
        if ($englishKeyed === []) {
            return ['ok' => true, 'translations' => []];
        }

        $merged = [];
        // Smaller chunks + same transport as forum summaries reduces truncation / invalid JSON
        $chunks = array_chunk($englishKeyed, 12, true);

        foreach ($chunks as $index => $chunk) {
            $part = $this->translateUiStringChunk($targetLanguageLabel, $chunk);
            $missing = array_diff_key($chunk, $part);
            if ($missing !== []) {
                $retry = $this->translateUiStringChunk($targetLanguageLabel, $missing, true);
                $part = array_merge($part, $retry);
            }

            $stillMissing = array_diff_key($chunk, $part);
            if ($stillMissing !== []) {
                foreach ($stillMissing as $key => $englishSource) {
                    $one = $this->translateSingleUiLabel($targetLanguageLabel, (string) $key, (string) $englishSource);
                    if ($one !== null && $one !== '') {
                        $part[$key] = $one;
                    }
                }
            }

            foreach (array_keys($chunk) as $key) {
                if (! array_key_exists($key, $part)) {
                    return [
                        'ok' => false,
                        'error' => 'OpenAI did not return usable translations for batch '.((int) $index + 1).' (missing key: '.$key.'). Check model output length (OPENAI_MODEL) and logs.',
                    ];
                }
            }

            foreach ($part as $k => $v) {
                if (! array_key_exists($k, $chunk)) {
                    continue;
                }
                $merged[$k] = is_string($v) ? $v : (string) $v;
            }
        }

        return ['ok' => true, 'translations' => $merged];
    }

    /**
     * Same Chat Completions request shape as {@see prompt()}: two "user" messages + sendRequest + message content.
     * Parsing follows {@see AIService::parseMetadata()} (clean_unicode, strip fences) with JSON and TAB fallbacks.
     *
     * @param  array<string, string>  $chunk
     * @return array<string, string>
     */
    private function translateUiStringChunk(string $targetLanguageLabel, array $chunk, bool $retryPass = false): array
    {
        if ($chunk === []) {
            return [];
        }

        $requiredKeys = implode(', ', array_keys($chunk));

        $guide = 'You translate short UI strings for a public health knowledge hub (navigation labels, buttons, footer links). '
            .'Target language: '.$targetLanguageLabel.'. '
            .'Preserve placeholders exactly: :name, :attribute, :year, %s, {0}, etc. Keep labels concise. '
            .'You MUST include every key listed below — do not omit any key (including the first one). '
            .'Reply using ONE of these formats only (prefer the first): '
            .'(1) A single JSON object whose keys match the input exactly (same spelling and snake_case) and values are translations. '
            .'(2) If JSON is awkward, one line per key: KEY<TAB>translated text (real tab character). '
            .'No markdown code fences, no explanation, no HTML.';

        if ($retryPass) {
            $guide .= ' This is a second pass: include every listed key exactly once.';
        }

        $userTask = ($retryPass ? 'Translate only these remaining keys.' : 'Translate all string values from English.')
            ."\n\nRequired keys (exact spelling, all of them): ".$requiredKeys
            ."\n\n".json_encode($chunk, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        $result = app(AiCompletionService::class)->completeForFeature('translation', [
            ['role' => 'user', 'content' => $guide],
            ['role' => 'user', 'content' => $userTask],
        ], 4096);

        $content = ($result['ok'] ?? false) ? ($result['content'] ?? '') : null;
        if ($content === null || $content === '') {
            Log::warning('translateUiStringChunk: empty OpenAI content', [
                'retry' => $retryPass,
                'keys' => array_keys($chunk),
            ]);

            return [];
        }

        $parsed = $this->parseTranslationMap($content, array_keys($chunk));
        if (count($parsed) < count($chunk)) {
            Log::warning('translateUiStringChunk: incomplete parse', [
                'retry' => $retryPass,
                'expected' => count($chunk),
                'got' => count($parsed),
                'missing' => array_values(array_diff(array_keys($chunk), array_keys($parsed))),
                'snippet' => mb_substr($content, 0, 1500),
            ]);
        }

        return $parsed;
    }

    /**
     * One label, plain-text reply — reliable fallback when batch JSON omits keys (e.g. "home").
     */
    private function translateSingleUiLabel(string $targetLanguageLabel, string $key, string $englishSource): ?string
    {
        $guide = 'You translate one short UI label for a public health website. '
            .'Target language: '.$targetLanguageLabel.'. '
            .'Output ONLY the translated label text on one line. No quotes, no JSON, no key name, no explanation.';

        $userTask = 'Context key (do not translate this word, it is only context): '.$key."\n"
            .'English label to translate: '.$englishSource;

        $result = app(AiCompletionService::class)->completeForFeature('translation', [
            ['role' => 'user', 'content' => $guide],
            ['role' => 'user', 'content' => $userTask],
        ], 256);

        if (! ($result['ok'] ?? false)) {
            return null;
        }

        $content = $result['content'] ?? null;
        if ($content === null) {
            return null;
        }

        $parts = preg_split("/\R/u", trim($content), 2);
        $line = isset($parts[0]) ? trim($parts[0]) : '';
        $line = trim($line, " \t\"'");

        return $line !== '' ? $line : null;
    }

    private function logTranslateFinishReason($response): void
    {
        if (! is_object($response)) {
            return;
        }
        $choice = $response->choices[0] ?? null;
        if (! is_object($choice)) {
            return;
        }
        $reason = $choice->finish_reason ?? null;
        if ($reason !== null && $reason !== 'stop') {
            Log::notice('OpenAI translate finish_reason: '.(string) $reason);
        }
    }

    /**
     * Extract assistant text the same way as {@see AIService::formatResponse()}.
     */
    private function extractOpenAiMessageContent($response): ?string
    {
        if (! is_object($response)) {
            return null;
        }
        if (isset($response->choices[0]->message->content)) {
            return (string) $response->choices[0]->message->content;
        }
        if (isset($response->error)) {
            $msg = is_object($response->error)
                ? (string) ($response->error->message ?? 'API error')
                : (string) $response->error;
            Log::warning('OpenAI translate response error field: '.$msg);
        }

        return null;
    }

    /**
     * @param  list<string>  $expectedKeys
     * @return array<string, string>
     */
    private function parseTranslationMap(string $raw, array $expectedKeys): array
    {
        $text = function_exists('clean_unicode') ? clean_unicode($raw) : $raw;
        $text = preg_replace('/```json\s*/i', '', (string) $text);
        $text = preg_replace('/```\s*/', '', $text);
        $text = trim($text);

        $expected = array_fill_keys($expectedKeys, true);
        $merged = [];

        // Full JSON
        $decoded = json_decode($text, true, 512, JSON_INVALID_UTF8_SUBSTITUTE);
        if (is_array($decoded) && json_last_error() === JSON_ERROR_NONE) {
            if (isset($decoded['translations']) && is_array($decoded['translations'])) {
                $decoded = $decoded['translations'];
            }
            $merged = array_merge($merged, $this->pickExpectedTranslations($decoded, $expected));
        }

        // Shallow JSON object match (same idea as AIService::parseMetadata)
        if (preg_match('/\{[^{}]*(?:\{[^{}]*\}[^{}]*)*\}/s', $text, $matches)) {
            $decoded = json_decode($matches[0], true, 512, JSON_INVALID_UTF8_SUBSTITUTE);
            if (is_array($decoded) && json_last_error() === JSON_ERROR_NONE) {
                if (isset($decoded['translations']) && is_array($decoded['translations'])) {
                    $decoded = $decoded['translations'];
                }
                $merged = array_merge($merged, $this->pickExpectedTranslations($decoded, $expected));
            }
        }

        // Brace slice fallback
        $start = strpos($text, '{');
        $end = strrpos($text, '}');
        if ($start !== false && $end !== false && $end > $start) {
            $slice = substr($text, $start, $end - $start + 1);
            $decoded = json_decode($slice, true, 512, JSON_INVALID_UTF8_SUBSTITUTE);
            if (is_array($decoded) && json_last_error() === JSON_ERROR_NONE) {
                if (isset($decoded['translations']) && is_array($decoded['translations'])) {
                    $decoded = $decoded['translations'];
                }
                $merged = array_merge($merged, $this->pickExpectedTranslations($decoded, $expected));
            }
        }

        // TAB lines: KEY<TAB>value (case-insensitive key match)
        $lowerToCanonical = $this->expectedKeyLowerMap($expectedKeys);
        foreach (preg_split("/\R/u", $text) as $line) {
            $line = trim($line);
            if ($line === '' || (strpos($line, '#') === 0)) {
                continue;
            }
            if (strpos($line, "\t") === false) {
                continue;
            }
            [$k, $v] = explode("\t", $line, 2);
            $k = trim($k);
            $canonical = $lowerToCanonical[strtolower($k)] ?? null;
            if ($canonical !== null) {
                $merged[$canonical] = trim($v);
            }
        }

        // Final pass: only expected keys, last writer wins
        $out = [];
        foreach ($expectedKeys as $ek) {
            if (array_key_exists($ek, $merged)) {
                $out[$ek] = $merged[$ek];
            }
        }

        return $out;
    }

    /**
     * @param  list<string>  $expectedKeys
     * @return array<string, string> lower => canonical
     */
    private function expectedKeyLowerMap(array $expectedKeys): array
    {
        $map = [];
        foreach ($expectedKeys as $ek) {
            $map[strtolower($ek)] = $ek;
        }

        return $map;
    }

    /**
     * @param  array<string, mixed>  $decoded
     * @param  array<string, true>  $expected
     * @return array<string, string>
     */
    private function pickExpectedTranslations(array $decoded, array $expected): array
    {
        $lowerToCanonical = $this->expectedKeyLowerMap(array_keys($expected));

        $out = [];
        foreach ($decoded as $k => $v) {
            $keyStr = is_string($k) ? $k : (is_int($k) ? (string) $k : null);
            if ($keyStr === null) {
                continue;
            }
            $canonical = null;
            if (isset($expected[$keyStr])) {
                $canonical = $keyStr;
            } else {
                $canonical = $lowerToCanonical[strtolower(trim($keyStr))] ?? null;
            }
            if ($canonical === null) {
                continue;
            }
            if ($v === null) {
                continue;
            }
            $out[$canonical] = is_string($v) ? $v : (is_scalar($v) ? (string) $v : json_encode($v));
        }

        return $out;
    }

    /**
     * Grammar / spelling / punctuation only; preserve HTML structure and meaning.
     *
     * @return array{ok: true, html: string}|array{ok: false, error: string}
     */
    public function proofreadHtmlForGrammar(string $html): array
    {
        if (AiConfig::resolveChatProviderForFeature('chat') === null) {
            return ['ok' => false, 'error' => 'No AI chat provider is configured.'];
        }

        $html = $html ?? '';
        if (mb_strlen($html) > 120000) {
            return ['ok' => false, 'error' => 'Content is too long for AI proofreading (max 120,000 characters).'];
        }

        $system = 'You are a careful copy-editor for a public health discussion forum. '
            .'Fix only grammar, spelling, punctuation, and obvious typos. '
            .'Do not change meaning, facts, opinions, numbers, dates, names, or the author\'s tone. '
            .'Do not add, remove, or reorder ideas; do not summarize or expand. '
            .'Preserve ALL HTML tags, attributes, link URLs (href), lists, and block structure—only change visible text inside elements when needed for correctness. '
            .'Keep the same language as the source. '
            .'Output only the corrected HTML fragment with no markdown code fences and no explanation before or after.';

        $response = $this->completeMessagesAsOpenAiResponse('chat', [
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' => "Proofread this HTML:\n\n".$html],
        ], 8192);
        $content = $this->extractOpenAiMessageContent($response);
        if ($content === null || $content === '') {
            Log::warning('proofreadHtmlForGrammar: empty OpenAI response');

            return ['ok' => false, 'error' => 'No usable response from the AI. Check the API key, model, and logs.'];
        }

        $out = trim($content);
        $out = preg_replace('/^```html\s*/i', '', $out);
        $out = preg_replace('/^```\s*/', '', $out);
        $out = preg_replace('/```\s*$/', '', $out);
        $out = trim($out);

        return ['ok' => true, 'html' => $out];
    }

    /**
     * Generate Africa-focused public health facts for the site “Did you know?” module.
     *
     * @return array{ok: true, facts: list<array{title: string, summary: string, description: string}>}|array{ok: false, error: string}
     */
    public function generateAfricaHealthFacts(int $count = 10): array
    {
        $count = max(10, min(15, $count));
        if (AiConfig::resolveChatProviderForFeature('insights') === null) {
            return ['ok' => false, 'error' => 'No AI provider is configured for insights.'];
        }

        $system = 'You are an evidence-focused public health editor for the Africa CDC Knowledge Hub. '
            .'Your job is to write short “Did you know?” facts about **real health issues, programmes, and research in Africa**. '
            .'Every fact must be grounded in information that is **actually reported or published** by reputable institutions—never invent numbers. '
            .'**Prioritize statistics and quantitative claims** (coverage rates, mortality reductions, people on treatment, programme scale, budgets, survey findings, DALYs, etc.) when you can attribute them to a credible source type below. '
            .'**Draw themes and figures from these kinds of sources (by name in the text where appropriate):** '
            .'WHO and **WHO Regional Office for Africa (WHO AFRO)**; **World Bank** (e.g. health expenditure, development indicators); **USAID** health and global health security programmes; **PEPFAR** (HIV treatment, prevention, lab support); '
            .'**national and sub-national Ministry of Health** initiatives across African countries; **Africa CDC** and AU-aligned health initiatives; '
            .'**African universities and research institutions** (e.g. national medical schools, INDEPTH Network sites, African-led clinical and public health studies) and partners such as **IHME/GBD** where they report Africa-relevant estimates. '
            .'**Rules:** (1) Include **at least one specific number, percentage, ratio, or count** in the summary or description whenever a well-cited figure exists in the public record; if only a **range or order of magnitude** is consistently reported, state it carefully and name the source type. '
            .'(2) **Name the institution or programme family** (e.g. “WHO AFRO”, “World Bank”, “PEPFAR”, “USAID”, “national Ministry of Health”, a named university or survey such as DHS/MICS) so readers see where the knowledge comes from. '
            .'(3) **Do not fabricate** exact statistics, years, or study names. If you cannot recall a defensible figure, use a **qualitative fact** still tied to a real programme or report type, or a **broad documented trend** without a fake number. '
            .'(4) Cover **diverse topics**: HIV/TB/malaria, immunization, maternal-newborn-child health, NCDs, mental health, WASH, health financing, human resources, labs, surveillance, outbreak response, and **Africa-led research**. '
            .'(5) Each fact must be **distinct** and **Africa-focused** (region, subregion, or named African countries). Tone: professional, precise, and hopeful. '
            .'(6) The **detail text (description)** is the main read: it must be **substantive and informative**—not a thin repeat of the summary. Pack in **several statistics or quantitative comparisons** where the public record supports them (e.g. coverage vs target, change over a documented period, regional vs global contrast, burden in DALYs or deaths, cohort sizes from major surveys). '
            .'Add **brief context** (who benefits, which countries or populations, programme mechanism or policy lever) so a general reader understands **why the numbers matter**.';

        $user = 'Return a single JSON object with key "facts" whose value is an array of exactly '.$count.' objects. '
            .'Each object must have: '
            .'"title" (short headline, max 90 characters; may include a key number if it fits); '
            .'"summary" (1–2 sentences for a card teaser, max 380 characters; **must include at least one statistic or quantitative comparison** when a supportable figure exists, plus a **source cue** such as WHO, World Bank, USAID, PEPFAR, Ministry of Health, Africa CDC, or an African university/research body); '
            .'"description" (the **full detail view**): write **8–12 sentences**, max **4500 characters**. '
            .'Requirements for description: (a) include **at least three distinct quantitative elements** when supportable—e.g. percentages, counts, rates, ratios, years of comparison, or survey-based estimates—not the same number repeated; '
            .'(b) weave in **why it matters** for health equity, systems, or outcomes in Africa; '
            .'(c) name **geographic scope** (region, multiple countries, or one country if the fact is national); '
            .'(d) mention **relevant programmes, data types, or institution families** (WHO AFRO, World Bank WDI, DHS/MICS, PEPFAR, USAID, MoH, Africa CDC, university or research network) without inventing specific report titles; '
            .'(e) where useful, add **one line of policy or programme implication** (scale-up, financing, integration)—still factual, not advocacy slogans. '
            .'Do not pad with filler; every sentence should add information. Output only valid JSON, no markdown fences.';

        $result = $this->chatCompletionJson([
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' => $user],
        ], 8192, null, 'insights');
        if (! ($result['ok'] ?? false)) {
            return ['ok' => false, 'error' => $result['error'] ?? 'AI request failed.'];
        }
        $content = $result['content'] ?? '';
        if (trim($content) === '') {
            return ['ok' => false, 'error' => 'Empty response from AI.'];
        }

        $facts = $this->parseAfricaHealthFactsJson($content);
        if (count($facts) < 10) {
            return ['ok' => false, 'error' => 'OpenAI returned too few usable facts ('.count($facts).').'];
        }

        $normalized = [];
        foreach (array_slice($facts, 0, $count) as $row) {
            $title = Str::limit(trim((string) ($row['title'] ?? '')), 255, '');
            $summary = Str::limit(trim((string) ($row['summary'] ?? '')), 2000, '');
            $description = trim((string) ($row['description'] ?? $row['summary'] ?? ''));
            $description = Str::limit($description, 8000, '');
            if ($title === '' || $summary === '') {
                continue;
            }
            if ($description === '') {
                $description = $summary;
            }
            $normalized[] = [
                'title' => $title,
                'summary' => $summary,
                'description' => $description,
            ];
        }

        if (count($normalized) < 10) {
            return ['ok' => false, 'error' => 'Too few facts after normalization ('.count($normalized).').'];
        }

        return ['ok' => true, 'facts' => array_slice($normalized, 0, $count)];
    }

    /**
     * Generate unique health topics (diseases, conditions) with HTML overviews.
     *
     * @param  list<string>  $existingTagNames
     * @param  list<string>  $referenceTopics
     * @return array{ok: true, topics: list<array{tag_text: string, overview: string}>}|array{ok: false, error: string}
     */
    public function generateHealthTopics(array $existingTagNames, array $referenceTopics, int $count = 15): array
    {
        $count = max(5, min(40, $count));
        if (AiConfig::resolveChatProviderForFeature('insights') === null) {
            return ['ok' => false, 'error' => 'No AI provider is configured for insights.'];
        }

        $seen = [];
        foreach ($existingTagNames as $name) {
            $key = HealthTopicSourceCatalog::normalizeTagKey((string) $name);
            if ($key !== '') {
                $seen[$key] = true;
            }
        }

        $referenceSample = array_slice(array_values(array_unique(array_filter(array_map('strval', $referenceTopics)))), 0, 50);
        $existingSample = array_slice(array_values(array_unique(array_filter(array_map('strval', $existingTagNames)))), 0, 80);

        $normalized = [];
        $batchSize = 5;
        $errors = [];

        while (count($normalized) < $count) {
            $need = min($batchSize, $count - count($normalized));
            $batch = $this->generateHealthTopicsBatch($need, $existingSample, $referenceSample, array_keys($seen));
            if (! ($batch['ok'] ?? false)) {
                $errors[] = $batch['error'] ?? 'Unknown batch error';
                break;
            }

            $addedInBatch = 0;
            foreach ($batch['topics'] as $row) {
                $tagText = Str::limit(trim((string) ($row['tag_text'] ?? '')), 255, '');
                $overview = trim((string) ($row['overview'] ?? ''));
                if ($tagText === '' || $overview === '') {
                    continue;
                }
                $key = HealthTopicSourceCatalog::normalizeTagKey($tagText);
                if ($key === '' || isset($seen[$key])) {
                    continue;
                }
                $seen[$key] = true;
                $existingSample[] = $tagText;
                $normalized[] = [
                    'tag_text' => $tagText,
                    'overview' => Str::limit($overview, 16000, ''),
                ];
                $addedInBatch++;
            }

            if ($addedInBatch === 0) {
                break;
            }
        }

        if ($normalized === []) {
            $detail = $errors !== [] ? implode(' ', array_unique($errors)) : 'No topics returned.';

            return ['ok' => false, 'error' => $detail];
        }

        return ['ok' => true, 'topics' => array_slice($normalized, 0, $count)];
    }

    /**
     * Build a rich HTML overview for an existing health topic using WHO factsheet material.
     *
     * @param  array{
     *     excerpt?: string,
     *     sections?: list<array{heading: string, body: string}>,
     *     references?: list<array{label: string, url: string}>,
     *     fact_sheet_url?: ?string,
     *     health_topic_url?: ?string
     * }  $whoSource
     * @return array{ok: true, overview: string, references: list<array{label: string, url: string}>}|array{ok: false, error: string}
     */
    public function generateHealthTopicOverview(string $tagText, array $whoSource, ?string $existingOverview = null): array
    {
        $tagText = trim($tagText);
        if ($tagText === '') {
            return ['ok' => false, 'error' => 'Topic name is required.'];
        }

        if (AiConfig::resolveChatProviderForFeature('insights') === null) {
            return ['ok' => false, 'error' => 'No AI provider is configured for insights.'];
        }

        $excerpt = trim((string) ($whoSource['excerpt'] ?? ''));
        if ($excerpt === '') {
            return ['ok' => false, 'error' => 'No WHO factsheet content found for this topic.'];
        }

        $references = $whoSource['references'] ?? [];
        if ($references === []) {
            $references = [
                ['label' => 'WHO Health Topics', 'url' => 'https://www.who.int/health-topics'],
            ];
        }

        $instruction = 'You write comprehensive health topic overviews for the Africa CDC Knowledge Hub. '
            .'Use ONLY the WHO source material provided — do not invent statistics, case counts, or study names. '
            .'Frame content for African public health audiences where relevant (burden, prevention, health systems) but stay factual. '
            .'If the source lacks Africa-specific data, say so briefly without fabricating numbers. '
            .$this->healthTopicOverviewHtmlGuide()
            .' Return ONLY valid JSON: {"overview":"<div>...</div>"}.';

        $userParts = [
            'Topic: '.$tagText,
            'WHO source material:',
            Str::limit($excerpt, 10000, ''),
            'Include these reference links in the References section:',
            json_encode($references, JSON_UNESCAPED_UNICODE),
        ];

        if ($existingOverview && trim(strip_tags($existingOverview)) !== '') {
            $userParts[] = 'Existing overview (improve and expand — keep accurate facts, add structure and references):';
            $userParts[] = Str::limit(strip_tags($existingOverview), 2000, '');
        }

        $messages = [
            ['role' => 'system', 'content' => $instruction],
            ['role' => 'user', 'content' => implode("\n\n", $userParts)],
        ];

        $result = $this->chatCompletionJson($messages, 4096, null, 'insights');
        if (! ($result['ok'] ?? false)) {
            return ['ok' => false, 'error' => $result['error'] ?? 'AI request failed.'];
        }

        $overview = $this->parseHealthTopicOverviewJson($result['content']);
        if ($overview === '') {
            return ['ok' => false, 'error' => 'Could not parse overview HTML from AI.'];
        }

        $overview = $this->ensureReferencesSection($overview, $references);

        return [
            'ok' => true,
            'overview' => Str::limit($overview, 16000, ''),
            'references' => $references,
        ];
    }

    /**
     * @param  list<array{tag_id: int, tag_text: string, overview?: ?string}>  $tags
     * @return array{ok: true, items: list<array<string, mixed>>}|array{ok: false, error: string, items?: list<array<string, mixed>>}
     */
    public function generateHealthTopicOverviewsForTags(array $tags, WhoFactsheetFetcher $whoFetcher): array
    {
        if ($tags === []) {
            return ['ok' => false, 'error' => 'No tags selected.'];
        }

        $items = [];
        $errors = [];

        foreach ($tags as $row) {
            $tagId = (int) ($row['tag_id'] ?? 0);
            $tagText = trim((string) ($row['tag_text'] ?? ''));
            $existing = (string) ($row['overview'] ?? '');

            if ($tagId <= 0 || $tagText === '') {
                continue;
            }

            $who = $whoFetcher->fetchForTopic($tagText);
            if (! ($who['ok'] ?? false)) {
                $errors[] = $tagText.': WHO factsheet not found.';
                $items[] = [
                    'tag_id' => $tagId,
                    'tag_text' => $tagText,
                    'ok' => false,
                    'error' => 'WHO factsheet not found.',
                    'existing_length' => mb_strlen(trim(strip_tags($existing))),
                ];
                continue;
            }

            $generated = $this->generateHealthTopicOverview($tagText, $who, $existing);
            if (! ($generated['ok'] ?? false)) {
                $errors[] = $tagText.': '.($generated['error'] ?? 'Generation failed.');
                $items[] = [
                    'tag_id' => $tagId,
                    'tag_text' => $tagText,
                    'ok' => false,
                    'error' => $generated['error'] ?? 'Generation failed.',
                    'existing_length' => mb_strlen(trim(strip_tags($existing))),
                ];
                continue;
            }

            $newOverview = (string) ($generated['overview'] ?? '');
            $items[] = [
                'tag_id' => $tagId,
                'tag_text' => $tagText,
                'ok' => true,
                'overview' => $newOverview,
                'references' => $generated['references'] ?? ($who['references'] ?? []),
                'who_fact_sheet_url' => $who['fact_sheet_url'] ?? null,
                'who_health_topic_url' => $who['health_topic_url'] ?? null,
                'existing_overview' => $existing,
                'existing_length' => mb_strlen(trim(strip_tags($existing))),
                'new_length' => mb_strlen(trim(strip_tags($newOverview))),
            ];
        }

        if ($items === []) {
            return ['ok' => false, 'error' => 'No tags could be processed.'];
        }

        $anyOk = false;
        foreach ($items as $item) {
            if ($item['ok'] ?? false) {
                $anyOk = true;
                break;
            }
        }

        if (! $anyOk) {
            return [
                'ok' => false,
                'error' => $errors !== [] ? implode(' ', array_unique($errors)) : 'All generations failed.',
                'items' => $items,
            ];
        }

        return ['ok' => true, 'items' => $items];
    }

    private function healthTopicOverviewHtmlGuide(): string
    {
        return 'overview must be rich HTML inside a single <div> (no html/head/body). '
            .'Use <h3 style="color:#119A48;"> for section headings (never h1/h2). '
            .'Include sections such as Overview, Key facts, Signs and symptoms (when relevant), Prevention and control, and Africa relevance. '
            .'Use multiple <p> paragraphs and <ul>/<li> lists where helpful. '
            .'End with <h3 style="color:#119A48;">References</h3><ul><li><a href="URL" target="_blank" rel="noopener noreferrer">Source label</a></li></ul>. '
            .'Target 8–14 sentences of substantive content (roughly 1500–4500 characters). '
            .'Cite WHO and other provided URLs in References; do not invent sources.';
    }

    private function parseHealthTopicOverviewJson(string $raw): string
    {
        $text = function_exists('clean_unicode') ? clean_unicode($raw) : $raw;
        $text = preg_replace('/```json\s*/i', '', (string) $text);
        $text = preg_replace('/```\s*/', '', $text);
        $text = trim($text);

        $decoded = json_decode($text, true, 512, JSON_INVALID_UTF8_SUBSTITUTE);
        if (! is_array($decoded) || json_last_error() !== JSON_ERROR_NONE) {
            $start = strpos($text, '{');
            $end = strrpos($text, '}');
            if ($start !== false && $end !== false && $end > $start) {
                $decoded = json_decode(substr($text, $start, $end - $start + 1), true, 512, JSON_INVALID_UTF8_SUBSTITUTE);
            }
        }

        if (! is_array($decoded)) {
            return '';
        }

        $overview = trim((string) ($decoded['overview'] ?? ''));

        return $overview;
    }

    /**
     * @param  list<array{label: string, url: string}>  $references
     */
    private function ensureReferencesSection(string $overview, array $references): string
    {
        if ($references === []) {
            return $overview;
        }

        if (stripos($overview, 'references') !== false && stripos($overview, '<a ') !== false) {
            return $overview;
        }

        $lis = '';
        foreach ($references as $ref) {
            $url = trim((string) ($ref['url'] ?? ''));
            $label = trim((string) ($ref['label'] ?? 'Reference'));
            if ($url === '') {
                continue;
            }
            $lis .= '<li><a href="'.htmlspecialchars($url, ENT_QUOTES, 'UTF-8').'" target="_blank" rel="noopener noreferrer">'
                .htmlspecialchars($label, ENT_QUOTES, 'UTF-8').'</a></li>';
        }

        if ($lis === '') {
            return $overview;
        }

        return rtrim($overview)
            .'<h3 style="color:#119A48;">References</h3><ul>'.$lis.'</ul>';
    }

    /**
     * @param  list<string>  $existingKeys lower-case tag keys already used
     * @param  list<string>  $referenceTopics
     * @param  list<string>  $blockedKeys lower-case keys to avoid (existing + generated)
     * @return array{ok: true, topics: list<array{tag_text: string, overview: string}>}|array{ok: false, error: string}
     */
    private function generateHealthTopicsBatch(int $count, array $existingKeys, array $referenceTopics, array $blockedKeys): array
    {
        $count = max(1, min(8, $count));
        $model = config('ai.openai_model', 'gpt-3.5-turbo');

        $instruction = 'You are a clinical terminology editor for the Africa CDC Knowledge Hub. '
            .'Create NEW health topic tags (diseases, conditions) for a public health portal focused on Africa. '
            .'Use WHO, CDC, MedlinePlus, and university health topic lists only as naming inspiration. '
            .'Each tag must be unique (case-insensitive) vs blocked names. '
            .'Return ONLY valid JSON: {"topics":[{"tag_text":"Name","overview":"<div>...</div>"}]}. '
            .'tag_text max 255 chars. '.$this->healthTopicOverviewHtmlGuide();

        $user = 'Generate exactly '.$count.' topics. '
            .'Blocked names (do not reuse): '.json_encode(array_slice($blockedKeys, 0, 120)).'. '
            .'Existing tags sample: '.json_encode(array_slice($existingKeys, 0, 60)).'. '
            .'Reference inspiration: '.json_encode(array_slice($referenceTopics, 0, 40)).'.';

        $messages = [
            ['role' => 'system', 'content' => $instruction],
            ['role' => 'user', 'content' => $user],
        ];

        $result = $this->chatCompletionJson($messages, 3096, $model, 'insights');
        if (! ($result['ok'] ?? false)) {
            return ['ok' => false, 'error' => $result['error'] ?? 'AI request failed.'];
        }

        $topics = $this->parseHealthTopicsJson($result['content']);
        if ($topics === []) {
            return ['ok' => false, 'error' => 'Could not parse health topics JSON from OpenAI.'];
        }

        $out = [];
        foreach ($topics as $row) {
            $tagText = trim((string) ($row['tag_text'] ?? ''));
            $overview = trim((string) ($row['overview'] ?? ''));
            if ($tagText !== '' && $overview !== '') {
                $out[] = ['tag_text' => $tagText, 'overview' => $overview];
            }
        }

        if ($out === []) {
            return ['ok' => false, 'error' => 'OpenAI returned topics without usable tag_text/overview fields.'];
        }

        return ['ok' => true, 'topics' => $out];
    }

    /**
     * Forum summarisation uses {@see prompt()} with the same endpoint, model config, and moderate max_tokens.
     *
     * @param  array<int, array{role: string, content: string}>  $messages
     * @return array{ok: true, content: string}|array{ok: false, error: string}
     */
    private function chatCompletionJson(array $messages, int $maxTokens, ?string $model = null, string $feature = 'insights'): array
    {
        $result = app(AiCompletionService::class)->completeForFeature(
            $feature,
            $messages,
            max(512, min(4096, $maxTokens)),
            $model,
            true
        );

        if ($result['ok'] ?? false) {
            return ['ok' => true, 'content' => $result['content']];
        }

        return ['ok' => false, 'error' => $result['error'] ?? 'AI request failed.'];
    }

    /**
     * @return array{ok: true, content: string}|array{ok: false, error: string}
     */
    private function openAiResponseContentOrError($response): array
    {
        if (! is_object($response)) {
            return ['ok' => false, 'error' => 'No response from OpenAI. Check OPEN_API_KEY, OPENAI_MODEL, and server outbound HTTPS.'];
        }

        if (isset($response->error)) {
            $msg = is_object($response->error)
                ? (string) ($response->error->message ?? 'API error')
                : (string) $response->error;

            return ['ok' => false, 'error' => 'OpenAI API error: '.$msg];
        }

        $choice = $response->choices[0] ?? null;
        if (! is_object($choice) || ! isset($choice->message->content)) {
            return ['ok' => false, 'error' => 'Unexpected OpenAI response format.'];
        }

        $content = trim((string) $choice->message->content);
        if ($content === '') {
            $reason = (string) ($choice->finish_reason ?? 'unknown');

            return ['ok' => false, 'error' => 'Empty response from OpenAI (finish_reason: '.$reason.'). Try fewer topics or a different OPENAI_MODEL.'];
        }

        return ['ok' => true, 'content' => $content];
    }

    /**
     * @return list<array{tag_text?: string, overview?: string}>
     */
    private function parseHealthTopicsJson(string $raw): array
    {
        $text = function_exists('clean_unicode') ? clean_unicode($raw) : $raw;
        $text = preg_replace('/```json\s*/i', '', (string) $text);
        $text = preg_replace('/```\s*/', '', $text);
        $text = trim($text);

        $decoded = json_decode($text, true, 512, JSON_INVALID_UTF8_SUBSTITUTE);
        if (! is_array($decoded) || json_last_error() !== JSON_ERROR_NONE) {
            $start = strpos($text, '{');
            $end = strrpos($text, '}');
            if ($start !== false && $end !== false && $end > $start) {
                $decoded = json_decode(substr($text, $start, $end - $start + 1), true, 512, JSON_INVALID_UTF8_SUBSTITUTE);
            }
        }

        if (! is_array($decoded)) {
            return [];
        }

        $list = $decoded['topics'] ?? null;
        if (! is_array($list)) {
            return [];
        }

        $out = [];
        foreach ($list as $item) {
            if (! is_array($item)) {
                continue;
            }
            $out[] = [
                'tag_text' => $item['tag_text'] ?? '',
                'overview' => $item['overview'] ?? '',
            ];
        }

        return $out;
    }

    /**
     * @return list<array{title?: string, summary?: string, description?: string}>
     */
    private function parseAfricaHealthFactsJson(string $raw): array
    {
        $text = function_exists('clean_unicode') ? clean_unicode($raw) : $raw;
        $text = preg_replace('/```json\s*/i', '', (string) $text);
        $text = preg_replace('/```\s*/', '', $text);
        $text = trim($text);

        $decoded = json_decode($text, true, 512, JSON_INVALID_UTF8_SUBSTITUTE);
        if (! is_array($decoded) || json_last_error() !== JSON_ERROR_NONE) {
            $start = strpos($text, '{');
            $end = strrpos($text, '}');
            if ($start !== false && $end !== false && $end > $start) {
                $decoded = json_decode(substr($text, $start, $end - $start + 1), true, 512, JSON_INVALID_UTF8_SUBSTITUTE);
            }
        }

        if (! is_array($decoded)) {
            return [];
        }

        $list = $decoded['facts'] ?? null;
        if (! is_array($list)) {
            return [];
        }

        $out = [];
        foreach ($list as $item) {
            if (! is_array($item)) {
                continue;
            }
            $out[] = [
                'title' => $item['title'] ?? '',
                'summary' => $item['summary'] ?? '',
                'description' => $item['description'] ?? '',
            ];
        }

        return $out;
    }

}