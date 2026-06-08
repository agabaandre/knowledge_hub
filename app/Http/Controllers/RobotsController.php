<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function show(): Response
    {
        $siteUrl = rtrim((string) config('app.url'), '/');
        $sitemapUrl = $siteUrl.'/sitemap.xml';

        $lines = [
            '# Africa CDC Knowledge Hub',
            'User-agent: *',
            'Allow: /',
            '',
            '# Admin, account, and non-public areas',
            'Disallow: /admin/',
            'Disallow: /account/',
            'Disallow: /api/',
            'Disallow: /tools/',
            'Disallow: /permissions/',
            'Disallow: /docs/',
            'Disallow: /telescope/',
            'Disallow: /install/',
            'Disallow: /storage/',
            'Disallow: /content-request/',
            'Disallow: /mailing_list',
            'Disallow: /tests',
            'Disallow: /auth/',
            'Disallow: /locale/',
            'Disallow: /login',
            'Disallow: /register',
            'Disallow: /logout',
            'Disallow: /password/',
            'Disallow: /verify',
            'Disallow: /records/search/fragment',
            'Disallow: /records/autocomplete',
            '',
            '# Sitemap',
            'Sitemap: '.$sitemapUrl,
            '',
            '# Social preview crawlers',
            'User-agent: facebookexternalhit',
            'Allow: /',
            '',
            'User-agent: Twitterbot',
            'Allow: /',
            '',
            'User-agent: LinkedInBot',
            'Allow: /',
        ];

        return response(implode("\n", $lines)."\n", 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
