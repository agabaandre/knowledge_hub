<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;

class ChatPDFService implements AIModel{

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
            "content"=>"Provide an accurate, thorough and comprehensive summarization (or comparison if requested) without being very brief and not mentioning specific sections in the document but you can still use bullets and headings as contained in the document.It is good to start with what the document covers as an overview, then give a detailed summary and add on whatever you find important, not forgetting any key definitions and case studes/ survey results/data/stats/ conclucions or numbers if avaialble.Be sure to touch all major sections, basing main headings. Always return responses in raw html format in a div,
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