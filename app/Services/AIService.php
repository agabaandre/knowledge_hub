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
     * Also extracts metadata: authors and affiliation
     */
    public function summariseFile($file_path, $language = 'en', $additional_prompt = null)
    {
        try {
            $response = [];
            
            // Check if file is PDF
            if (strtolower(pathinfo($file_path, PATHINFO_EXTENSION)) === 'pdf') {
                $this->aiModel = app('chatpdf');
                
                // Extract description/summary
                $summaryResponse = $this->aiModel->summarizeFile($file_path, $language, $additional_prompt);
                $formattedContent = $this->formatResponse($summaryResponse);
                $response['content'] = clean_unicode($formattedContent['content'] ?? '');
                
                // Extract metadata (authors and affiliation)
                $metadataPrompt = "Extract the following information from this document and return ONLY a valid JSON object with these exact keys: 
                {
                    \"authors\": \"comma-separated list of author names found in the document (e.g., 'John Doe, Jane Smith')\",
                    \"affiliation\": \"the institution, organization, or affiliation of the authors mentioned in the document\"
                }
                If information is not found, use empty strings. Return ONLY the JSON, no additional text.";
                
                $metadataResponse = $this->aiModel->summarizeFile($file_path, $language, $metadataPrompt);
                $metadataContent = $this->formatResponse($metadataResponse)['content'];
                
                // Parse metadata from response
                $metadata = $this->parseMetadata($metadataContent);
                $response['metadata'] = $metadata;
                
            } else {
                // For non-PDF files, extract text and use ChatGPT
                $file_content = '';
                if (file_exists($file_path)) {
                    $file_content = file_get_contents($file_path);
                    // Limit content size
                    $file_content = substr($file_content, 0, 100000);
                }
                
                // Extract description/summary
                $prompt = "Summarize this document content: " . $file_content;
                if ($additional_prompt) {
                    $prompt .= ". Pay attention to: " . $additional_prompt;
                }
                if ($language && $language !== 'en') {
                    $prompt .= ". Translate summary to: $language";
                }
                
                $summaryResponse = $this->aiModel->summarize($prompt, $additional_prompt);
                $formattedContent = $this->formatResponse($summaryResponse);
                $response['content'] = clean_unicode($formattedContent['content'] ?? '');
                
                // Extract metadata
                $metadataPrompt = "From this document content: " . substr($file_content, 0, 50000) . "
                Extract the following information and return ONLY a valid JSON object with these exact keys:
                {
                    \"authors\": \"comma-separated list of author names found in the document (e.g., 'John Doe, Jane Smith')\",
                    \"affiliation\": \"the institution, organization, or affiliation of the authors mentioned in the document\"
                }
                If information is not found, use empty strings. Return ONLY the JSON, no additional text.";
                
                $metadataResponse = $this->aiModel->summarize($metadataPrompt);
                $metadataContent = $this->formatResponse($metadataResponse)['content'];
                
                // Parse metadata from response
                $metadata = $this->parseMetadata($metadataContent);
                $response['metadata'] = $metadata;
            }

            Log::info("FILE SUMMARY RESPONSE: " . json_encode($response));

            return $response;
        } catch (\Exception $e) {
            Log::error("Error summarizing file: " . $e->getMessage());
            return [
                'content' => "<div class='alert alert-danger'><p>Error: " . $e->getMessage() . '</p></div>',
                'metadata' => ['authors' => '', 'affiliation' => '']
            ];
        }
    }

    /**
     * Parse metadata from AI response
     */
    private function parseMetadata($metadataContent)
    {
        $metadata = ['authors' => '', 'affiliation' => ''];
        
        try {
            // First clean Unicode from the raw metadata content
            $metadataContent = clean_unicode($metadataContent);
            
            // Remove markdown code blocks if present
            $metadataContent = preg_replace('/```json\s*/', '', $metadataContent);
            $metadataContent = preg_replace('/```\s*/', '', $metadataContent);
            $metadataContent = trim($metadataContent);
            
            // Try to extract JSON from the response (handle nested objects)
            // Match JSON objects, including nested ones
            if (preg_match('/\{[^{}]*(?:\{[^{}]*\}[^{}]*)*\}/', $metadataContent, $matches)) {
                $jsonContent = $matches[0];
                $parsed = json_decode($jsonContent, true);
                
                if (is_array($parsed) && json_last_error() === JSON_ERROR_NONE) {
                    $metadata['authors'] = $parsed['authors'] ?? '';
                    $metadata['affiliation'] = $parsed['affiliation'] ?? '';
                }
            }
            
            // Fallback: try direct JSON decode if regex didn't work
            if ($metadata['authors'] === '' && $metadata['affiliation'] === '') {
                $parsed = json_decode($metadataContent, true);
                if (is_array($parsed) && json_last_error() === JSON_ERROR_NONE) {
                    $metadata['authors'] = $parsed['authors'] ?? '';
                    $metadata['affiliation'] = $parsed['affiliation'] ?? '';
                }
            }
            
            // Clean up the extracted values - apply Unicode cleaning to individual fields
            $metadata['authors'] = clean_unicode(trim($metadata['authors']));
            $metadata['affiliation'] = clean_unicode(trim($metadata['affiliation']));
            
            // Log parsed metadata for debugging
            Log::info("Parsed metadata: " . json_encode($metadata));
            
        } catch (\Exception $e) {
            Log::error("Error parsing metadata: " . $e->getMessage());
        }
        
        return $metadata;
    }

    private function formatResponse($response)
    {
        $content = '';
        
        if (isset($response->content)) {
            $content = $response->content;
        } elseif (isset($response->message)) {
            $content = $response->message;
        } elseif (isset($response->choices)) {
            $content = $response->choices[0]->message->content;
        } else {
            $error = (is_object($response->error)) ? $response->error->message : ($response->error ?? 'Error');
            $content = "<div class='alert alert-danger'><p>Failure: " . $error . '</p></div>';
        }
        
        // Clean Unicode characters from content before returning
        return ['content' => clean_unicode($content)];
    }
}
