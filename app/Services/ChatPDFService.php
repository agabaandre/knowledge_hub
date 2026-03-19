<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;

class ChatPDFService implements AIModel
{
    private function getApiKey()
    {
        return config('ai.chat_pdf_key');
    }

    /**
     * Get ChatPDF sourceId from a publicly accessible PDF URL (add-url API).
     * @see https://www.chatpdf.com/docs/api/backend
     */
    public function getSourceIdFromUrl(string $pdfUrl)
    {
        $endpoint = 'https://api.chatpdf.com/v1/sources/add-url';
        $headers = [
            'Content-Type: application/json',
            'x-api-key: ' . $this->getApiKey()
        ];
        $payload = ['url' => $pdfUrl];
        $response = $this->sendRequest($endpoint, $headers, $payload);
        return $response->sourceId ?? null;
    }

    /**
     * Get ChatPDF sourceId from a local PDF file path (add-file API).
     */
    public function getSourceIdFromFile(string $filePath)
    {
        $source = $this->submitFile($filePath);
        return $source->sourceId ?? null;
    }

    /**
     * Chat with PDF: send messages and get response (stateless – send full history).
     * Messages: [ ['role' => 'user'|'assistant', 'content' => '...'], ... ]
     * Max 6 messages, ~2500 tokens total per API docs.
     *
     * @param string $sourceId ChatPDF source ID
     * @param array $messages Array of { role, content }
     * @param bool $referenceSources Include page references in response
     * @return object { content, references? }
     */
    public function chat(string $sourceId, array $messages, bool $referenceSources = false)
    {
        $endpoint = 'https://api.chatpdf.com/v1/chats/message';
        $headers = [
            'Content-Type: application/json',
            'x-api-key: ' . $this->getApiKey()
        ];
        $payload = [
            'sourceId' => $sourceId,
            'messages' => array_slice($messages, -6), // API limit: up to 6 messages
        ];
        if ($referenceSources) {
            $payload['referenceSources'] = true;
        }
        Log::info('ChatPDF chat request', ['sourceId' => $sourceId, 'messagesCount' => count($payload['messages'])]);
        return $this->sendRequest($endpoint, $headers, $payload);
    }

    /**
     * Stream chat response from ChatPDF (stream: true).
     * Yields chunks to the callable: $onChunk(string $chunk).
     */
    public function chatStream(string $sourceId, array $messages, callable $onChunk): void
    {
        $endpoint = 'https://api.chatpdf.com/v1/chats/message';
        $payload = [
            'sourceId' => $sourceId,
            'messages' => array_slice($messages, -6),
            'stream' => true,
        ];
        $jsonData = json_encode($payload);
        $headers = [
            'Content-Type: application/json',
            'x-api-key: ' . $this->getApiKey(),
            'Content-Length: ' . strlen($jsonData),
        ];

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $data) use ($onChunk) {
            $len = strlen($data);
            if ($len > 0) {
                $onChunk($data);
            }
            return $len;
        });

        curl_exec($ch);
        if (curl_errno($ch)) {
            Log::error('ChatPDF stream error: ' . curl_error($ch));
            $onChunk(json_encode(['error' => curl_error($ch)]));
        }
        curl_close($ch);
    }

    function prompt($file_url=null,$additional_prompt=null){

        $api_key  = config("ai.chat_pdf_key");
        $chat_endpoint = 'https://api.chatpdf.com/v1/chats/message';

        $headers = [
            'Content-Type: application/json',
            "x-api-key: $api_key"
        ];

        $source = $this->submitDocument($file_url);

        $prompt =  [
            [
            "role"=> "user", 
            "content"=>"Provide an accurate, thorough and comprehensive summarization (or comparison if requested) without being very brief and not mentioning specific sections in the document but you can still use bullets and headings as contained in the document. CRITICAL OPENING: The very first content inside your HTML must be one or more normal <p> paragraphs with full sentences. Do NOT place any <h3>, <h4>, or <h5> before those opening paragraphs. Do NOT start with a <p> that contains only a short bold title (e.g. a single line in <strong> or styled like a heading). Subheadings (h3/h4) and bold section labels are ONLY for content that comes after this first paragraph block. Do NOT use titles \"Overview\", \"Overview of the Document\", or similar at the start. Then give a detailed summary and add on whatever you find important, not forgetting any key definitions and case studes/ survey results/data/stats/ conclucions or numbers if avaialble. Be sure to touch all major sections, basing main headings. Always return responses in raw html format in a div,
             ignore html,head and body tags, use nice styling especially using  lists but followed by paragraph explanations,headings and paragraphs, don't use any h1 and h2 tags. Use teal color for headings and bold words.For short content given for summarising, always respond saying there's not enough content to be summarised,remember to make your summaries rich enough, to atleast enough depending on  what you are given but don't make it too small or too big. and avoid using background colors"
            ]
        ];

        $additional_prompt .= " Make sure you translate to  if requested ";

        $prompt[] = ["role"=>"user","content"=>$additional_prompt];

        $payload = [
            'messages'=> $prompt,
            "sourceId"=> $source->sourceId
        ];

        Log::info("====Request::====/n ".json_encode($payload));

        return $this->sendRequest($chat_endpoint, $headers, $payload);
     
    }

    private function submitDocument($file_url){

        $api_key  = config("ai.chat_pdf_key");
        $chat_endpoint = 'https://api.chatpdf.com/v1/sources/add-url';

        $headers = [
            'Content-Type: application/json',
            "x-api-key: $api_key"];

        $payload = ['url' => $file_url];
        
        return $this->sendRequest($chat_endpoint, $headers, $payload);
    }

    /**
     * Submit a file for processing (for uploaded files)
     */
    private function submitFile($file_path){

        $api_key  = config("ai.chat_pdf_key");
        $chat_endpoint = 'https://api.chatpdf.com/v1/sources/add-file';

        $headers = [
            "x-api-key: $api_key"];

        // Prepare file for multipart upload
        $cfile = new \CURLFile($file_path);
        $cfile->setMimeType('application/pdf');
        $cfile->setPostFilename(basename($file_path));

        $payload = ['file' => $cfile];
        
        // Use cURL for multipart upload
        $ch = curl_init($chat_endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($ch);
        curl_close($ch);

        Log::info("====FILE UPLOAD RESPONSE::====/n ".$response);
    
        return json_decode($response);
    }

    /**
     * Summarize an uploaded file (file path, not URL)
     */
    public function summarizeFile($file_path, $language = null, $additional_prompt = null)
    {
        $source = $this->submitFile($file_path);

        if (!isset($source->sourceId)) {
            return (object)['error' => 'Failed to upload file to ChatPDF'];
        }

        $api_key  = config("ai.chat_pdf_key");
        $chat_endpoint = 'https://api.chatpdf.com/v1/chats/message';

        $headers = [
            'Content-Type: application/json',
            "x-api-key: $api_key"
        ];

        $prompt = [
            [
                "role" => "user",
                "content" => "Provide an accurate, thorough and comprehensive summarization without being very brief. CRITICAL OPENING: The very first content inside your HTML must be one or more normal <p> paragraphs with full sentences. Do NOT place any <h3>, <h4>, or <h5> before those opening paragraphs. Do NOT start with a <p> that contains only a short bold title (e.g. one line in <strong> or styled like a heading). Subheadings (h3/h4) and bold section labels are ONLY for content after this first paragraph block. Do NOT use \"Overview\", \"Overview of the Document\", or similar as the opening. Then give a detailed summary and add on whatever you find important, not forgetting any key definitions and case studies/survey results/data/stats/conclusions or numbers if available. Be sure to touch all major sections, basing main headings. Always return responses in raw html format in a div, ignore html, head and body tags, use nice styling especially using lists but followed by paragraph explanations, headings and paragraphs, don't use any h1 and h2 tags. Use teal color for headings and bold words. For short content given for summarising, always respond saying there's not enough content to be summarised, remember to make your summaries rich enough, to at least enough depending on what you are given but don't make it too small or too big. and avoid using background colors"
            ]
        ];

        $additional_prompt = ($additional_prompt ?? '') . ($language ? " Make sure you translate to $language if requested" : '');
        if ($additional_prompt) {
            $prompt[] = ["role" => "user", "content" => $additional_prompt];
        }

        $payload = [
            'messages' => $prompt,
            "sourceId" => $source->sourceId
        ];

        Log::info("====Request::====/n ".json_encode($payload));

        return $this->sendRequest($chat_endpoint, $headers, $payload);
    }

    
    function summarize($resource,$language=null,$additional_prompt=null){

        $prompt  = ($additional_prompt)?$additional_prompt:"Translate summary to: $language, here the comments users have submitted about it: ".json_encode($resource->comments->toArray());
        $prompt .= " Don't forget to translate to ".$language." if provided ";
        return $this->prompt($resource->publication,$prompt);
    }

    function compare($resource,$other_resource,$additional_prompt=null){

        $question = "Compare the following two for me: ". $resource ." and ".$other_resource;
        return $this->prompt($question);

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

}