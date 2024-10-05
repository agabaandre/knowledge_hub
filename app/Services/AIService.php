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

    public function summarise($resourceId, $type, $language)
    {
        if (intval($type) !== 1) {
            $resource = Publication::find($resourceId);
            
            if (!$resource) {
                return $this->formatResponse((Object)['message' => 'Publication not found']);
            }

            if (strpos($resource->publication, '.pdf') > -1) {
                $this->aiModel = app('chatpdf');
                $response = $this->aiModel->summarize($resource, $language);
            } else {
                $prompt = "Use summary language: $language, title: $resource->title,  
                body: $resource->description,
                attached_content: " . truncate(pdfToText($resource->publication), 100000) . ", 
                comments: " . json_encode($resource->comments->toArray());

                $response = $this->aiModel->summarize($prompt);
            }
        } else {
            $resource = Forum::find($resourceId);
            
            if (!$resource) {
                return $this->formatResponse((Object) ['message' => 'Forum not found']);
            }

            $prompt = "summary language: $language, forum title: $resource->forum_title,
             forum content: $resource->forum_description,  
             forum comments: " . json_encode($resource->comments->toArray());
            $response = $this->aiModel->summarize($prompt);
        }

        Log::info("RESPONSE: " . json_encode($response));

        return $this->formatResponse($response);
    }

    public function compare($resourceId, $otherResourceId)
    {
        $resource = Publication::find($resourceId);
        $resource2 = Publication::find($otherResourceId);

          
        if (!$resource || !$resource2)
        return $this->formatResponse((Object)['message' => 'Publication not found']);


        if (strpos($resource->publication, '.pdf') > -1 && strpos($resource2->publication, '.pdf') > -1) {
            $this->aiModel = app('chatpdf');
            $response = $this->aiModel->compare($resource, $resource2);
        } else {
          
            $prompt = " title: $resource->title,  
            body: $resource->description,
            attached_content: " . truncate(pdfToText($resource->publication), 100000) . ", 
            comments: " . json_encode($resource->comments->toArray());

            $prompt2 = "title: $resource2->title,  
            body: $resource2->description,
            attached_content: " . truncate(pdfToText($resource2->publication), 100000) . ", 
            comments: " . json_encode($resource2->comments->toArray());

            $response = $this->aiModel->compare($prompt, $prompt2);
        }

        Log::info("RESPONSE: " . json_encode($response));

        return $this->formatResponse($response);
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

        return ['content' => "<div class='alert alert-danger'><p>Failure: " . $response->error->message ?? 'Error' . '</p></div>'];
    }
}
