<?php

namespace App\Http\Controllers;

use App\Models\SiteLanguage;
use App\Models\StaticLink;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LocaleSwitchController extends Controller
{
    public function apply(Request $request): JsonResponse
    {
        $locale = (string) $request->input('locale', '');
        $supported = SiteLanguage::activeLocaleCodes();

        if ($locale === '' || ! in_array($locale, $supported, true)) {
            return response()->json(['error' => 'Unsupported locale'], 422);
        }

        App::setLocale($locale);

        $map = SiteLanguage::selectorMap();
        $googleCode = $map[$locale]['google_code'] ?? $locale;

        $user = $request->user();
        if ($user !== null && ($user->langauge ?? null) !== $locale) {
            $user->langauge = $locale;
            $user->save();
        }

        $shared = ['staticLinks' => $this->staticLinks()];

        $fragments = [
            'khub-nav-menus' => view('layouts.partials.nav_menus', $shared)->render(),
            'khub-footer-i18n' => view('layouts.partials.footer_i18n_row', $shared)->render(),
            'khub-footer-bottom-i18n' => view('layouts.partials.footer_i18n_bottom', $shared)->render(),
            'khub-login-i18n' => view('layouts.partials.login_i18n', $shared)->render(),
            'khub-cookie-i18n' => view('layouts.partials.cookie_i18n', $shared)->render(),
        ];

        $cookiePath = $this->cookiePath($request);
        $cookieName = (string) config('supported_locales.locale_cookie', 'khub_locale');
        $cookieMinutes = (int) config('supported_locales.locale_cookie_minutes', 525600);

        $response = response()->json([
            'locale' => $locale,
            'google_code' => $googleCode,
            'fragments' => $fragments,
        ]);

        $response->cookie($cookieName, $locale, $cookieMinutes, $cookiePath, null, false, false);

        if ($locale === 'en') {
            $response->cookie('googtrans', '', -1, $cookiePath, null, false, false);
        } else {
            $response->cookie('googtrans', '/auto/'.$googleCode, $cookieMinutes, $cookiePath, null, false, false);
        }

        return $response;
    }

    public function switch(Request $request, string $locale): RedirectResponse
    {
        $supported = SiteLanguage::activeLocaleCodes();

        if (! in_array($locale, $supported, true)) {
            $locale = in_array('en', $supported, true) ? 'en' : ($supported[0] ?? 'en');
        }

        $map = SiteLanguage::selectorMap();
        $googleCode = $map[$locale]['google_code'] ?? $locale;
        $cookiePath = $this->cookiePath($request);
        $cookieName = (string) config('supported_locales.locale_cookie', 'khub_locale');
        $cookieMinutes = (int) config('supported_locales.locale_cookie_minutes', 525600);

        $redirect = $request->query('redirect', '/');
        if (! is_string($redirect) || $redirect === '' || str_starts_with($redirect, '//')) {
            $redirect = '/';
        }

        $response = redirect($redirect);
        $response->cookie($cookieName, $locale, $cookieMinutes, $cookiePath, null, false, false);

        if ($locale === 'en') {
            $response->cookie('googtrans', '', -1, $cookiePath, null, false, false);
        } else {
            $response->cookie('googtrans', '/auto/'.$googleCode, $cookieMinutes, $cookiePath, null, false, false);
        }

        return $response;
    }

    private function cookiePath(Request $request): string
    {
        $path = (string) config('supported_locales.cookie_path', '/');
        $basePath = $request->getBasePath();

        if (is_string($basePath) && $basePath !== '' && $basePath !== '/') {
            return rtrim($basePath, '/').'/';
        }

        return $path;
    }

    /**
     * @return \Illuminate\Support\Collection<int, StaticLink>
     */
    private function staticLinks()
    {
        $minutes = (int) env('CACHE_EXPIRY_DURATION_MINUTES', 60 * 24);

        return cache()->remember('adminunits', $minutes, static function () {
            return StaticLink::orderBy('order')->get();
        });
    }
}
