<?php

namespace App\Http\Controllers;

use App\Services\LlmsTxtService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class LlmsTxtController extends Controller
{
    public function show(LlmsTxtService $llmsTxt): Response
    {
        $body = Cache::remember('llms.txt:v1', now()->addHours(6), fn () => $llmsTxt->render());

        return response($body, 200, [
            'Content-Type' => 'text/markdown; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
