<?php

namespace App\Http\Controllers;

use App\Services\SitemapService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index(SitemapService $sitemap): Response
    {
        $xml = Cache::remember('sitemap:index:v1', now()->addHours(6), fn () => $sitemap->renderIndex());

        return $this->xmlResponse($xml);
    }

    public function section(string $name, SitemapService $sitemap): Response
    {
        $cacheKey = 'sitemap:section:v1:'.$name;
        $xml = Cache::remember($cacheKey, now()->addHours(6), fn () => $sitemap->renderSection($name));

        return $this->xmlResponse($xml);
    }

    private function xmlResponse(string $xml): Response
    {
        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
