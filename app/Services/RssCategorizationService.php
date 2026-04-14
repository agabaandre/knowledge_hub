<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Uses OpenAI to categorize RSS content into publication metadata.
 * Fallback: returns empty/safe defaults if API fails.
 */
class RssCategorizationService
{
    public function categorize(string $title, string $description, string $link = ''): array
    {
        $apiKey = config('ai.open_api_key');
        if (empty($apiKey)) {
            Log::warning('RSS categorization: OPEN_API_KEY not set');
            return $this->fallbackMetadata($title, $description);
        }

        $prompt = $this->buildPrompt($title, $description, $link);
        $payload = [
            'model' => config('ai.openai_model', 'gpt-3.5-turbo'),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a metadata extraction assistant. Reply only with valid JSON, no markdown or extra text. Use null for unknown. For thematic_area and sub_theme use short names that can be matched to database (e.g. "Health Systems", "Surveillance"). For tags use comma-separated health topic names from: Allergies, Arthritis, Asthma, Cancer, Cholera, COVID-19, Ebola, Epidemics, Health Governance, Health Systems, HIV, HIV/AIDS, Hypertension, Influenza, Laboratory, Marburg, Mental Health, Mortality Rate, Mpox, NCDs, Nutrition, Osteoporosis, Pharmacy, Population, Pregnancy, Surveillance, Workforce. For member_states use country names. Keep abstract/description at least 150 words if you expand it.',
                ],
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => 1500,
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', $payload);

            if (!$response->successful()) {
                Log::warning('RSS categorization API error', ['status' => $response->status(), 'body' => $response->body()]);
                return $this->fallbackMetadata($title, $description);
            }

            $body = $response->json();
            $content = $body['choices'][0]['message']['content'] ?? '';
            $content = preg_replace('/^```\w*\n?|\n?```$/', '', trim($content));
            $decoded = json_decode($content, true);
            if (!is_array($decoded)) {
                return $this->fallbackMetadata($title, $description);
            }
            return array_merge($this->fallbackMetadata($title, $description), $decoded);
        } catch (\Throwable $e) {
            Log::warning('RSS categorization exception: ' . $e->getMessage());
            return $this->fallbackMetadata($title, $description);
        }
    }

    private function buildPrompt(string $title, string $description, string $link): string
    {
        $text = "Title: {$title}\n\nDescription/Abstract: " . substr(strip_tags($description), 0, 3000);
        if ($link) {
            $text .= "\n\nLink: {$link}";
        }
        $text .= "\n\nExtract and return a JSON object with these keys (use null if unknown): "
            . "thematic_area, sub_theme, category, sub_category, member_states (array of country names), "
            . "journal_name, journal_volume, journal_issue, journal_pages, "
            . "abstract (or description, min 150 words), associated_authors, author_affiliation, "
            . "tags (array of health topics from the list above), doi, issn, isbn, publisher, license, funder, copyright_info.";
        return $text;
    }

    private function fallbackMetadata(string $title, string $description): array
    {
        $desc = trim(strip_tags($description));
        if (str_word_count($desc) < 150 && strlen($desc) < 750) {
            $desc = $desc ?: $title;
            $desc .= "\n\n[Description extracted from RSS; please expand to at least 150 words before publishing.]";
        }
        return [
            'title' => $title,
            'description' => $desc,
            'abstract' => $desc,
            'thematic_area' => null,
            'sub_theme' => null,
            'category' => null,
            'sub_category' => null,
            'member_states' => [],
            'journal_name' => null,
            'journal_volume' => null,
            'journal_issue' => null,
            'journal_pages' => null,
            'associated_authors' => null,
            'author_affiliation' => null,
            'tags' => [],
            'doi' => null,
            'issn' => null,
            'isbn' => null,
            'publisher' => null,
            'license' => null,
            'funder' => null,
            'copyright_info' => null,
        ];
    }
}
