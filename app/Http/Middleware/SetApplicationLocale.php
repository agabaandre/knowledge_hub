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
        $defaultCfg = config('supported_locales.default', config('app.locale', 'en'));
        $locale = in_array($defaultCfg, $supported, true) ? $defaultCfg : (in_array('en', $supported, true) ? 'en' : ($supported[0] ?? 'en'));

        $cookieName = config('supported_locales.locale_cookie');
        if ($cookieName && ($cookieLocale = $request->cookie($cookieName))) {
            if (is_string($cookieLocale) && in_array($cookieLocale, $supported, true)) {
                // Locale selector writes this cookie on every language change; honor it first.
                $locale = $cookieLocale;
            }
        } elseif ($request->user()) {
            // Fallback for logged-in users when no locale cookie exists.
            $userLang = $request->user()->langauge ?? null;
            if (is_string($userLang) && in_array($userLang, $supported, true)) {
                $locale = $userLang;
            }
        }

        App::setLocale($locale);

        return $next($request);
    }
}
