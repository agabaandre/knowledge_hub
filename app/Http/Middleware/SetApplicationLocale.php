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

        if ($request->user()) {
            $userLang = $request->user()->langauge ?? null;
            if (is_string($userLang) && in_array($userLang, $supported, true)) {
                $locale = $userLang;
            }
        }

        $cookieName = config('supported_locales.locale_cookie');
        if ($cookieName && ($cookieLocale = $request->cookie($cookieName))) {
            if (is_string($cookieLocale) && in_array($cookieLocale, $supported, true)) {
                // Guest or preference before login: cookie applies when user has no DB preference
                if (! $request->user() || empty($request->user()->langauge)) {
                    $locale = $cookieLocale;
                }
            }
        }

        App::setLocale($locale);

        return $next($request);
    }
}
