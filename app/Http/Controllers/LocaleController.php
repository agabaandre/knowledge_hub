<?php

namespace App\Http\Controllers;

use App\Models\SiteLanguage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class LocaleController extends Controller
{
    /**
     * Set Laravel UI locale + Google Translate cookies, then redirect back.
     * More reliable than client-only cookies on subdirectory installs.
     */
    public function switch(Request $request, string $locale)
    {
        $supported = SiteLanguage::activeLocaleCodes();
        if (! in_array($locale, $supported, true)) {
            abort(404);
        }

        $map = SiteLanguage::selectorMap();
        $googleCode = $map[$locale]['google_code'] ?? $locale;

        $cookieName = (string) config('supported_locales.locale_cookie', 'khub_locale');
        $minutes = (int) config('supported_locales.locale_cookie_minutes', 525600);
        $path = $this->localeCookiePath();

        $redirect = (string) $request->query('redirect', '');
        if ($redirect === '' || ! $this->isSafeRedirect($redirect)) {
            $redirect = url()->previous() ?: url('/');
        }

        if (Auth::check() && ($user = current_user())) {
            $user->langauge = $locale;
            $user->save();
        }

        $response = redirect()->to($redirect);

        $response->withCookie(cookie(
            $cookieName,
            $locale,
            $minutes,
            $path,
            null,
            $request->isSecure(),
            false,
            false,
            'Lax'
        ));

        if ($locale === 'en') {
            $response->withCookie(Cookie::forget('googtrans', $path));
        } else {
            $response->withCookie(cookie(
                'googtrans',
                '/auto/'.$googleCode,
                $minutes,
                $path,
                null,
                $request->isSecure(),
                false,
                false,
                'Lax'
            ));
        }

        return $response;
    }

    private function localeCookiePath(): string
    {
        $base = request()->getBasePath();
        if (is_string($base) && $base !== '' && $base !== '/') {
            return rtrim($base, '/').'/';
        }

        $configured = config('supported_locales.cookie_path', '/');

        return is_string($configured) && $configured !== '' ? $configured : '/';
    }

    private function isSafeRedirect(string $url): bool
    {
        if (str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
            return true;
        }

        $appUrl = rtrim((string) config('app.url'), '/');
        if ($appUrl !== '' && str_starts_with($url, $appUrl)) {
            return true;
        }

        return false;
    }
}
