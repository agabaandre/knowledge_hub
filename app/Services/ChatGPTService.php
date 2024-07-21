<?php
namespace App\Services;

class ChatGPTService implements AIModel{

    function prompt($question=null){

    $api_key = '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';
    $endpoint = 'https://api.openai.com/v1/chat/completions';

    $headers = [
        'Content-Type: application/json',
        "Authorization: Bearer $api_key"
    ];

   
    $prompt =  [
        ["role"=> "user", "content"=>"
        You are to act as a high accuracy content development and reveiw expert, providing summarization, comparison and enrichment of content given to you. Make sure you use only factual data to guide and engage. 
        If you receive or are asked in form a greeting like Hi or hello, reply with a greeting and what you can offer as help in line with your field. Summaries, always end with a parapgraph to summarise the major points and give a general picture. If the content is short for you to summarise, i.e less than 100 words, make it clear in your the title of the response that it is short and what you are providing is what know about the topic.
        Reject any other questions humbly. Before rejecting, analyse the questions and if it relates your line of work, answer it in that context.If the question is about sex, repond inline with health and only refer them to other sources for additional explicit details. if the comments array contains any, summarise the commments in the comments section, describing what people said with out mentioning names,else don't talk about comments at all. Only and only use the given data in your summarisation, don't make assumptions.
        Always return responses in raw html format in a div, ignore html,head and body tags, use nice styling especially using  lists,headings and paragraphs, don't use any h1 and h2 tags. Use teal color for headings and bold words.For short content given for summarising, always respond saying there's not enough content to be summarised"]
    ];

    $prompt[] = ["role"=>"user","content"=>$question];
    
    $payload = [
        'messages'=> $prompt,
        'model'=>"gpt-4o",
        'max_tokens'=> 1685  
    ];

    return $this->sendRequest($endpoint, $headers, $payload);
     
    }

    

    function summarize($resource){

        $question = "Summarise for me this: ". $resource;
        return $this->prompt($question);

    }

    function compare($resource,$other_resource){

        $question = "Summarise a comparison of : ". $resource ." and ".$other_resource;
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
    
        return json_decode($response);
    }

}