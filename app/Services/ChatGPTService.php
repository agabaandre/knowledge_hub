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
            $error = curl_error($ch);
            echo 'cURL Error: ' . $error;
            $response = null;
        }
    
        // Close cURL session
        curl_close($ch);

        Log::info("====RESPONSE::====/n ".$response);
    
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
        $chunks = array_chunk($englishKeyed, 30, true);

        foreach ($chunks as $index => $chunk) {
            $part = $this->translateUiStringChunk($targetLanguageLabel, $chunk);
            if ($part === null) {
                return [
                    'ok' => false,
                    'error' => 'OpenAI did not return valid JSON for batch '.((int) $index + 1).'. Check the API key, model, and application logs.',
                ];
            }

            foreach (array_keys($chunk) as $key) {
                if (! array_key_exists($key, $part)) {
                    return [
                        'ok' => false,
                        'error' => 'AI response missing key: '.$key,
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
     * @param  array<string, string>  $chunk
     * @return array<string, string>|null
     */
    private function translateUiStringChunk(string $targetLanguageLabel, array $chunk): ?array
    {
        $system = 'You are a professional UI translator for a public health knowledge hub web application. '
            .'You MUST respond with a single JSON object only. No markdown code fences, no commentary, no text before or after the JSON. '
            .'The JSON object keys must be exactly the same as in the input (same spelling). '
            .'Each value is the translation of the English UI string into this language: '.$targetLanguageLabel.'. '
            .'Preserve placeholders and tokens exactly (examples: :name, :attribute, :year, %s, {0}). Do not translate brand names if they appear as proper nouns. '
            .'Keep menu labels and buttons concise and natural in the target language.';

        $user = "Translate the string values from English to {$targetLanguageLabel}. Keep keys identical.\n\n"
            .json_encode($chunk, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        $data = $this->postChatCompletion([
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' => $user],
        ], 8192);

        if ($data === null) {
            return null;
        }

        $content = $data['choices'][0]['message']['content'] ?? null;
        if (! is_string($content) || $content === '') {
            return null;
        }

        $obj = $this->decodeJsonObjectFromAssistant($content);
        if (! is_array($obj)) {
            Log::warning('translateUiStringChunk: could not parse JSON', [
                'snippet' => mb_substr($content, 0, 800),
            ]);

            return null;
        }

        /** @var array<string, mixed> $obj */
        $out = [];
        foreach ($obj as $k => $v) {
            if (! is_string($k)) {
                continue;
            }
            $out[$k] = is_string($v) ? $v : (is_scalar($v) ? (string) $v : json_encode($v));
        }

        return $out;
    }

    /**
     * @param  list<array{role: string, content: string}>  $messages
     * @return array<string, mixed>|null
     */
    private function postChatCompletion(array $messages, int $maxTokens = 4096): ?array
    {
        $api_key = config('ai.open_api_key');
        if ($api_key === '' || $api_key === null) {
            return null;
        }

        $endpoint = 'https://api.openai.com/v1/chat/completions';
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer '.$api_key,
        ];

        $body = [
            'model' => config('ai.openai_model', 'gpt-3.5-turbo'),
            'messages' => $messages,
            'max_tokens' => $maxTokens,
            'temperature' => 0.2,
        ];

        $ch = curl_init($endpoint);
        $jsonData = json_encode($body);
        $headers[] = 'Content-Length: '.strlen($jsonData);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);

        $response = curl_exec($ch);

        if ($response === false) {
            Log::error('OpenAI translate: cURL error: '.curl_error($ch));
            curl_close($ch);

            return null;
        }

        $http = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $decoded = json_decode($response, true);
        if (! is_array($decoded)) {
            Log::error('OpenAI translate: invalid response JSON');

            return null;
        }

        if ($http >= 400) {
            $msg = $decoded['error']['message'] ?? ('HTTP '.$http);
            Log::error('OpenAI translate API error: '.$msg);

            return null;
        }

        return $decoded;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function decodeJsonObjectFromAssistant(string $text): ?array
    {
        $text = trim($text);
        if (preg_match('/^```(?:json)?\s*\R(.*)\R```$/su', $text, $m)) {
            $text = trim($m[1]);
        }

        $decoded = json_decode($text, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        $start = strpos($text, '{');
        $end = strrpos($text, '}');
        if ($start !== false && $end !== false && $end > $start) {
            $slice = substr($text, $start, $end - $start + 1);
            $decoded = json_decode($slice, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }

}