<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;

class ChatGPTService implements AIModel{

    function prompt($question=null){

    $api_key  = config("ai.open_api_key");
    $endpoint = 'https://api.openai.com/v1/chat/completions';

    $headers = [
        'Content-Type: application/json',
        "Authorization: Bearer $api_key"
    ];

    $prompt =  [
        ["role"=> "user", "content"=>"
        You are to act as a high accuracy content development and reveiw expert,  providing accurate comprehensive summarization and or comparison without being ridiculously brief and not mentioning specific sections in the document but you can still use bullets and headings , comparison and enrichment of content given to you.If Attached content ('attached_content:<content here>') contains data, work on that first but ignoring table of contents and unreadble characters,for atatched content remember to mention that the section u are summarising is from the attachment. Make sure you use only factual data to guide and engage. 
        If you receive or are asked in form a greeting like Hi or hello, reply with a greeting and what you can offer as help in line with your field. Summaries, always end with a parapgraph to summarise the major points and give a general picture. If the content is short for you to summarise, i.e less than 100 words, make it clear in your the title of the response that it is short and what you are providing is what know about the topic.
        Reject any other questions humbly. Before rejecting, analyse the questions and if it relates your line of work, answer it in that context.If the question is about sex, repond inline with health and only refer them to other sources for additional explicit details. if the comments array contains any, summarise the commments in the comments section, describing what people said with out mentioning names,else don't talk about comments at all. Only and only use the given data in your summarisation, don't make assumptions.
        Always return responses in raw html format in a div, ignore html,head and body tags, use nice styling especially using  lists,headings and paragraphs, don't use any h1 and h2 tags. Use teal color for headings and bold words.For short content given for summarising, always respond saying there's not enough content to be summarised,remember to make your summaries rich enough, to atleast aquarter of what you are given but not less. and avoid using background colors. Translate the summary to the summary ;anguage if provided. Only do comparison if it is a comparison question."]
    ];

    $prompt[] = ["role"=>"user","content"=>$question];

    $payload = [
        'messages'=> $prompt,
        'model'=>"gpt-3.5-turbo",
        'max_tokens'=> 3096 //1685  
    ];

    return $this->sendRequest($endpoint, $headers, $payload);
     
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
    public function promptStream(string $question, callable $onChunk): void
    {
        $api_key  = config("ai.open_api_key");
        $endpoint = 'https://api.openai.com/v1/chat/completions';
        $headers = [
            'Content-Type: application/json',
            "Authorization: Bearer $api_key"
        ];
        $systemContent = "You are to act as a high accuracy content development and review expert, providing accurate comprehensive summarization and or comparison without being ridiculously brief and not mentioning specific sections in the document but you can still use bullets and headings , comparison and enrichment of content given to you.If Attached content ('attached_content:<content here>') contains data, work on that first but ignoring table of contents and unreadable characters,for attached content remember to mention that the section u are summarising is from the attachment. Make sure you use only factual data to guide and engage. If the content is short for you to summarise, i.e less than 100 words, make it clear in the title of the response. Always return responses in raw html format in a div, ignore html,head and body tags, use nice styling especially using lists,headings and paragraphs, don't use h1 and h2 tags. Use teal color for headings and bold words. Avoid using background colors. Translate the summary to the summary language if provided.";
        $payload = [
            'model' => config('ai.openai_model', 'gpt-3.5-turbo'),
            'messages' => [
                ['role' => 'system', 'content' => $systemContent],
                ['role' => 'user', 'content' => $question],
            ],
            'max_tokens' => 3096,
            'stream' => true,
        ];
        $jsonData = json_encode($payload);
        $headers[] = 'Content-Length: ' . strlen($jsonData);

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $data) use ($onChunk) {
            $len = strlen($data);
            if ($len > 0) {
                $this->parseSSELine($data, $onChunk);
            }
            return $len;
        });

        curl_exec($ch);
        if (curl_errno($ch)) {
            Log::error('OpenAI stream error: ' . curl_error($ch));
            $onChunk('<div class="alert alert-danger">Stream error. Please try again.</div>');
        }
        curl_close($ch);
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

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
    
        // Execute cURL request and get the response
        $response = curl_exec($ch);
    
        // Check for cURL errors
        if ($response === false) {
            Log::error('OpenAI cURL error: '.curl_error($ch));
            $response = null;
        }

        // Close cURL session
        curl_close($ch);

        Log::info('====RESPONSE::==== '.$response);

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

        $api_key = config('ai.open_api_key');
        $endpoint = 'https://api.openai.com/v1/chat/completions';
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer '.$api_key,
        ];

        $guide = 'You translate short UI strings for a public health knowledge hub (navigation labels, buttons, footer links). '
            .'Target language: '.$targetLanguageLabel.'. '
            .'Preserve placeholders exactly: :name, :attribute, :year, %s, {0}, etc. Keep labels concise. '
            .'Reply using ONE of these formats only (prefer the first): '
            .'(1) A single JSON object whose keys are exactly the input keys and values are translations. '
            .'(2) If JSON is awkward, one line per key: KEY<TAB>translated text (real tab character). '
            .'No markdown code fences, no explanation, no HTML.';

        if ($retryPass) {
            $guide .= ' This is a second pass: include every listed key exactly once.';
        }

        $userTask = ($retryPass ? 'Translate only these remaining keys.' : 'Translate all string values from English.')
            ."\n\n".json_encode($chunk, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        // Match forum summarization: stacked user messages + model from config (see promptStream)
        $payload = [
            'messages' => [
                ['role' => 'user', 'content' => $guide],
                ['role' => 'user', 'content' => $userTask],
            ],
            'model' => config('ai.openai_model', 'gpt-3.5-turbo'),
            'max_tokens' => 4096,
            'temperature' => 0.2,
        ];

        $response = $this->sendRequest($endpoint, $headers, $payload);
        $content = $this->extractOpenAiMessageContent($response);
        if ($content === null || $content === '') {
            Log::warning('translateUiStringChunk: empty OpenAI content', [
                'retry' => $retryPass,
                'keys' => array_keys($chunk),
            ]);

            return [];
        }

        $parsed = $this->parseTranslationMap($content, array_keys($chunk));
        if ($parsed === []) {
            Log::warning('translateUiStringChunk: could not parse response', [
                'retry' => $retryPass,
                'snippet' => mb_substr($content, 0, 1200),
            ]);
        }

        return $parsed;
    }

    /**
     * Extract assistant text the same way as {@see AIService::formatResponse()}.
     */
    private function extractOpenAiMessageContent(mixed $response): ?string
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

        // Full JSON
        $decoded = json_decode($text, true, 512, JSON_INVALID_UTF8_SUBSTITUTE);
        if (is_array($decoded) && json_last_error() === JSON_ERROR_NONE) {
            if (isset($decoded['translations']) && is_array($decoded['translations'])) {
                $decoded = $decoded['translations'];
            }
            $out = $this->pickExpectedTranslations($decoded, $expected);
            if ($out !== []) {
                return $out;
            }
        }

        // Shallow JSON object match (same idea as AIService::parseMetadata)
        if (preg_match('/\{[^{}]*(?:\{[^{}]*\}[^{}]*)*\}/s', $text, $matches)) {
            $decoded = json_decode($matches[0], true, 512, JSON_INVALID_UTF8_SUBSTITUTE);
            if (is_array($decoded) && json_last_error() === JSON_ERROR_NONE) {
                if (isset($decoded['translations']) && is_array($decoded['translations'])) {
                    $decoded = $decoded['translations'];
                }
                $out = $this->pickExpectedTranslations($decoded, $expected);
                if ($out !== []) {
                    return $out;
                }
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
                $out = $this->pickExpectedTranslations($decoded, $expected);
                if ($out !== []) {
                    return $out;
                }
            }
        }

        // TAB lines: KEY<TAB>value
        $out = [];
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
            if ($k !== '' && isset($expected[$k])) {
                $out[$k] = trim($v);
            }
        }

        return $out;
    }

    /**
     * @param  array<string, mixed>  $decoded
     * @param  array<string, true>  $expected
     * @return array<string, string>
     */
    private function pickExpectedTranslations(array $decoded, array $expected): array
    {
        $out = [];
        foreach ($decoded as $k => $v) {
            if (! is_string($k) || ! isset($expected[$k])) {
                continue;
            }
            $out[$k] = is_string($v) ? $v : (is_scalar($v) ? (string) $v : json_encode($v));
        }

        return $out;
    }

}