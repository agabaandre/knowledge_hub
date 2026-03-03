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
            // Forums and other non-PDF content: use GPT (chatgpt) for summarization
            $resource = Forum::with([
                'user',
                'tags',
                'comments' => function($query) {
                    $query->whereNull('parent_id') // Only top-level comments
                          ->orderBy('created_at', 'asc');
                },
                'comments.user',
                'comments.likes',
                'comments.replies' => function($query) {
                    $query->orderBy('created_at', 'asc');
                },
                'comments.replies.user',
                'comments.replies.likes'
            ])->find($resourceId);
            
            if (!$resource) {
                return $this->formatResponse((Object) ['message' => 'Forum not found']);
            }

            // Build structured comment data for AI
            $structuredComments = $this->buildStructuredForumComments($resource);

            // Process forum description to extract text content (strip HTML but keep structure)
            $forumDescriptionText = strip_tags($resource->forum_description ?? '');
            $forumDescriptionText = html_entity_decode($forumDescriptionText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            
            $prompt = "Summary language: $language. 
            
Forum Title: {$resource->forum_title}

Forum Content: {$forumDescriptionText}

Comments (total: " . count($structuredComments) . "): " . json_encode($structuredComments, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "

Please summarize this forum discussion, including:
- Main points discussed in the forum post
- Key insights and opinions shared in the comments
- Important attachments or resources mentioned (mention file types and names)
- Overall sentiment and engagement level
- Any questions raised or topics needing clarification";

            $response = $this->aiModel->summarize($prompt, $additional_prompt);
        }

        Log::info("RESPONSE: " . json_encode($response));

        return $this->formatResponse($response);
    }

    /**
     * Stream summary to $onChunk(string $content). Uses GPT streaming when possible; PDF uses ChatPDF (one chunk).
     */
    public function summariseStream($resourceId, $type, $language, $additional_prompt, callable $onChunk): void
    {
        $type = intval($type);
        if ($type === 1) {
            // Forum: use GPT streaming
            $resource = Forum::with([
                'user', 'tags',
                'comments' => fn ($q) => $q->whereNull('parent_id')->orderBy('created_at'),
                'comments.user', 'comments.likes',
                'comments.replies' => fn ($q) => $q->orderBy('created_at'),
                'comments.replies.user', 'comments.replies.likes'
            ])->find($resourceId);
            if (!$resource) {
                $onChunk('<div class="alert alert-danger">Forum not found.</div>');
                return;
            }
            $structuredComments = $this->buildStructuredForumComments($resource);
            $forumDescriptionText = strip_tags($resource->forum_description ?? '');
            $forumDescriptionText = html_entity_decode($forumDescriptionText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $prompt = "Summary language: $language.\n\nForum Title: {$resource->forum_title}\n\nForum Content: {$forumDescriptionText}\n\nComments (total: " . count($structuredComments) . "): " . json_encode($structuredComments, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\nPlease summarize this forum discussion, including: Main points discussed, key insights and opinions in the comments, important attachments or resources mentioned, overall sentiment and engagement, any questions raised. Return raw HTML in a div, use teal for headings, no h1/h2, no background colors. Translate to $language if provided.";
            if ($additional_prompt) {
                $prompt .= " Pay attention to: " . $additional_prompt;
            }
            $chatGpt = app('chatgpt');
            $chatGpt->promptStream($prompt, $onChunk);
            return;
        }

        // Publication
        $resource = Publication::find($resourceId);
        if (!$resource) {
            $onChunk('<div class="alert alert-danger">Publication not found.</div>');
            return;
        }

        if (strpos($resource->publication ?? '', '.pdf') > -1) {
            // PDF: ChatPDF does not support streaming for summarize; get full response and send as one chunk
            $aiModel = app('chatpdf');
            $response = $aiModel->summarize($resource, $language, $additional_prompt);
            $formatted = $this->formatResponse($response);
            $onChunk($formatted['content'] ?? '');
            return;
        }

        // Non-PDF publication: GPT streaming
        $prompt = "Use summary language: $language, title: {$resource->title}, body: " . ($resource->description ?? '') . ", attached_content: " . truncate(pdfToText($resource->publication ?? ''), 100000) . ", comments: " . json_encode($resource->comments->toArray());
        $prompt .= " Summarise for me this. Don't forget to translate to $language if provided.";
        if ($additional_prompt) {
            $prompt .= " Pay attention to this: " . $additional_prompt;
        }
        $chatGpt = app('chatgpt');
        $chatGpt->promptStream($prompt, $onChunk);
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

    /**
     * Build structured comment data for AI summarization
     * Includes user info, attachments, likes, and reply structure
     */
    private function buildStructuredForumComments($forum)
    {
        $structuredComments = [];
        
        foreach ($forum->comments as $comment) {
            // Skip replies (they'll be included in their parent comment)
            if ($comment->parent_id) {
                continue;
            }
            
            // Get comment text (strip HTML but keep plain text)
            $commentText = strip_tags($comment->comment ?? '');
            $commentText = html_entity_decode($commentText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $commentText = trim($commentText);
            
            // Get attachments information
            $attachmentsInfo = [];
            try {
                $attachments = $comment->attachments ?? collect();
                if (!$attachments || (is_object($attachments) && method_exists($attachments, 'count') && $attachments->count() === 0)) {
                    // Try direct query
                    $attachments = \App\Models\CustomAttachment::where('model', 'forum_comments')
                        ->where('record_id', $comment->id ?? 0)
                        ->get();
                }
                
                foreach ($attachments as $attachment) {
                    $fileName = $attachment->name ?? basename($attachment->path);
                    $extension = strtolower(pathinfo($attachment->path ?? $fileName, PATHINFO_EXTENSION));
                    
                    $fileType = 'file';
                    if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'])) {
                        $fileType = 'image';
                    } elseif (in_array($extension, ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'm4v', 'ogg', 'ogv'])) {
                        $fileType = 'video';
                    } elseif ($extension === 'pdf') {
                        $fileType = 'pdf';
                    } elseif (in_array($extension, ['doc', 'docx'])) {
                        $fileType = 'word';
                    } elseif (in_array($extension, ['xls', 'xlsx'])) {
                        $fileType = 'excel';
                    } elseif (in_array($extension, ['ppt', 'pptx'])) {
                        $fileType = 'powerpoint';
                    }
                    
                    $attachmentsInfo[] = [
                        'name' => $fileName,
                        'type' => $fileType
                    ];
                }
            } catch (\Exception $e) {
                \Log::error('Error loading attachments for comment: ' . $e->getMessage());
            }
            
            // Build reply structure
            $replies = [];
            if ($comment->replies && $comment->replies->count() > 0) {
                foreach ($comment->replies as $reply) {
                    $replyText = strip_tags($reply->comment ?? '');
                    $replyText = html_entity_decode($replyText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $replyText = trim($replyText);
                    
                    // Get reply attachments
                    $replyAttachmentsInfo = [];
                    try {
                        $replyAttachments = $reply->attachments ?? collect();
                        if (!$replyAttachments || (is_object($replyAttachments) && method_exists($replyAttachments, 'count') && $replyAttachments->count() === 0)) {
                            $replyAttachments = \App\Models\CustomAttachment::where('model', 'forum_comments')
                                ->where('record_id', $reply->id ?? 0)
                                ->get();
                        }
                        
                        foreach ($replyAttachments as $attachment) {
                            $fileName = $attachment->name ?? basename($attachment->path);
                            $extension = strtolower(pathinfo($attachment->path ?? $fileName, PATHINFO_EXTENSION));
                            $fileType = 'file';
                            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) $fileType = 'image';
                            elseif (in_array($extension, ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'])) $fileType = 'video';
                            elseif ($extension === 'pdf') $fileType = 'pdf';
                            
                            $replyAttachmentsInfo[] = [
                                'name' => $fileName,
                                'type' => $fileType
                            ];
                        }
                    } catch (\Exception $e) {
                        // Ignore errors for reply attachments
                    }
                    
                    $replies[] = [
                        'user' => $reply->user->name ?? 'Unknown',
                        'text' => $replyText,
                        'likes' => $reply->likes ? $reply->likes->count() : 0,
                        'attachments' => $replyAttachmentsInfo,
                        'time' => $reply->created_at ? $reply->created_at->diffForHumans() : ''
                    ];
                }
            }
            
            $structuredComments[] = [
                'user' => $comment->user->name ?? 'Unknown',
                'text' => $commentText,
                'likes' => $comment->likes ? $comment->likes->count() : 0,
                'attachments' => $attachmentsInfo,
                'replies_count' => $comment->replies ? $comment->replies->count() : 0,
                'replies' => $replies,
                'time' => $comment->created_at ? $comment->created_at->diffForHumans() : ''
            ];
        }
        
        return $structuredComments;
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
