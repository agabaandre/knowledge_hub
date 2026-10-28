<?php

namespace App\Http\Middleware;

use App\Models\SiteLanguage;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetApplicationLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = SiteLanguage::activeLocaleCodes();

        // Admin / permissions: always Configure → Default language (dynamic per hub).
        if (SiteLanguage::isBackendRequest($request)) {
            $locale = SiteLanguage::systemDefaultLocale();
            if (! in_array($locale, $supported, true) && $supported !== []) {
                $locale = in_array('en', $supported, true) ? 'en' : $supported[0];
            }
            App::setLocale($locale);

            return $next($request);
        }

        $queryLocale = $request->query('locale');
        if (is_string($queryLocale) && in_array($queryLocale, $supported, true)) {
            App::setLocale($queryLocale);

            return $next($request);
        }

        // Frontend: profile language, then site default, then guest cookie.
        $cookieName = config('supported_locales.locale_cookie');
        $cookieLocale = $cookieName ? $request->cookie($cookieName) : null;
        $userLang = $request->user()?->langauge ?? null;
        $googtrans = $request->cookie('googtrans');

        $locale = SiteLanguage::resolveActiveLocale(
            is_string($userLang) ? $userLang : null,
            is_string($googtrans) ? $googtrans : null,
            is_string($cookieLocale) ? $cookieLocale : null
        );

        if (! in_array($locale, $supported, true)) {
            $locale = SiteLanguage::systemDefaultLocale();
        }

        App::setLocale($locale);

        return $next($request);
    }
}
