<?php

namespace App\Services;

use App\Models\Forum;
use App\Models\Publication;
use Illuminate\Support\Facades\Log;

class AIService
{
    private $aiModel;

    public function __construct()
    {
        $this->aiModel = app('chatgpt');
    }

    public function summarise($resourceId, $type, $language,$additional_prompt=null)
    {
        if (intval($type) !== 1) { //1 is for forums
            $resource = Publication::find($resourceId);
            
            if (!$resource) {
                return $this->formatResponse((Object)['message' => 'Publication not found']);
            }

            if (strpos($resource->publication, '.pdf') > -1) {
                $this->aiModel = app('chatpdf');
                $response = $this->aiModel->summarize($resource, $language, $additional_prompt);
            } else {
                $prompt = "Use summary language: $language, title: $resource->title,  
                body: $resource->description,
                attached_content: " . truncate(pdfToText($resource->publication), 100000) . ", 
                comments: " . json_encode($resource->comments->toArray());

                $response = $this->aiModel->summarize($prompt,$additional_prompt);
            }
        } else {
            $resource = Forum::find($resourceId);
            
            if (!$resource) {
                return $this->formatResponse((Object) ['message' => 'Forum not found']);
            }

            $prompt = "summary language: $language, forum title: $resource->forum_title,
             forum content: $resource->forum_description,  
             forum comments: " . json_encode($resource->comments->toArray());
            $response = $this->aiModel->summarize($prompt,$additional_prompt);
        }

        Log::info("RESPONSE: " . json_encode($response));

        return $this->formatResponse($response);
    }

    public function compare($resourceId, $otherResourceId,$additional_prompt=null)
    {
        $resource = Publication::find($resourceId);
        $resource2 = Publication::find($otherResourceId);

          
        if (!$resource || !$resource2)
        return $this->formatResponse((Object)['message' => 'Publication not found']);


        if (strpos($resource->publication, '.pdf') > -1 && strpos($resource2->publication, '.pdf') > -1) {
            $this->aiModel = app('chatpdf');
            $response = $this->aiModel->compare($resource, $resource2,$additional_prompt);
        } else {
          
            $prompt = " title: $resource->title,  
            body: $resource->description,
            attached_content: " . truncate(pdfToText($resource->publication), 100000) . ", 
            comments: " . json_encode($resource->comments->toArray());

            $prompt2 = "title: $resource2->title,  
            body: $resource2->description,
            attached_content: " . truncate(pdfToText($resource2->publication), 100000) . ", 
            comments: " . json_encode($resource2->comments->toArray());

            $response = $this->aiModel->compare($prompt, $prompt2,$additional_prompt);
        }

        Log::info("RESPONSE: " . json_encode($response));

        return $this->formatResponse($response);
    }

    /**
     * Summarize content from an uploaded file (before saving as publication)
     */
    public function summariseFile($file_path, $language = 'en', $additional_prompt = null)
    {
        try {
            // Check if file is PDF
            if (strtolower(pathinfo($file_path, PATHINFO_EXTENSION)) === 'pdf') {
                $this->aiModel = app('chatpdf');
                $response = $this->aiModel->summarizeFile($file_path, $language, $additional_prompt);
            } else {
                // For non-PDF files, extract text and use ChatGPT
                $file_content = '';
                if (file_exists($file_path)) {
                    $file_content = file_get_contents($file_path);
                    // Limit content size
                    $file_content = substr($file_content, 0, 100000);
                }
                
                $prompt = "Summarize this document content: " . $file_content;
                if ($additional_prompt) {
                    $prompt .= ". Pay attention to: " . $additional_prompt;
                }
                if ($language && $language !== 'en') {
                    $prompt .= ". Translate summary to: $language";
                }
                
                $response = $this->aiModel->summarize($prompt, $additional_prompt);
            }

            Log::info("FILE SUMMARY RESPONSE: " . json_encode($response));

            return $this->formatResponse($response);
        } catch (\Exception $e) {
            Log::error("Error summarizing file: " . $e->getMessage());
            return ['content' => "<div class='alert alert-danger'><p>Error: " . $e->getMessage() . '</p></div>'];
        }
    }

    private function formatResponse($response)
    {
        if (isset($response->content)) {
            return ['content' => $response->content];
        }

        if (isset($response->message)) {
            return ['content' => $response->message];
        }

        if (isset($response->choices)) {
            return ['content' => $response->choices[0]->message->content];
        }

        $error = (is_object($response->error))?$response->error->message : $response->error;

        return ['content' => "<div class='alert alert-danger'><p>Failure: " .$error  ?? ' Error' . '</p></div>'];
    }
}
