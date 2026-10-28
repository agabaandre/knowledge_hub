<div id="google_translate_element" style="display:none;"></div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/js/bootstrap-select.min.js"
    integrity="sha384-gYEWPc4PqVsUcmUA/F1H9PPNjnNvS8CHniaAOtoRluGxpLVqO2dwtGnA/gK3NVAg"
    crossorigin="anonymous" referrerpolicy="no-referrer" type="text/javascript"></script>
@php
    use App\Models\SiteLanguage;
    $uiLocale = active_ui_locale();
    $systemDefaultLocale = SiteLanguage::systemDefaultLocale();
    $contentLocale = SiteLanguage::contentLocale();
    $localeCookiePath = config('supported_locales.cookie_path', '/');
    $basePath = request()->getBasePath();
    if (is_string($basePath) && $basePath !== '' && $basePath !== '/') {
        $localeCookiePath = rtrim($basePath, '/').'/';
    }
    $googleCodesByLocale = collect(SiteLanguage::selectorMap())
        ->mapWithKeys(fn ($row, $code) => [$code => $row['google_code'] ?? $code])
        ->all();
    // Google pageLanguage = authored HTML language (usually en), NOT the visitor default.
    $pageLanguageGoogle = $googleCodesByLocale[$contentLocale] ?? $contentLocale;
@endphp
<script type="text/javascript">
    window.khubGoogleTranslateReady = false;
    window.khubPendingGoogleTranslate = null;
    window.khubSystemDefaultLocale = @json($systemDefaultLocale);
    window.khubContentLocale = @json($contentLocale);
    window.khubPageLanguageGoogle = @json($pageLanguageGoogle);
    window.khubActiveUiLocale = @json($uiLocale);

    /** Native Laravel UI covers system default + authored content language — no Google Translate. */
    window.khubShouldUseGoogleTranslate = function (localeCode) {
        var locale = localeCode || window.khubActiveUiLocale || '';
        var content = window.khubContentLocale || 'en';
        var systemDefault = window.khubSystemDefaultLocale || content;
        if (!locale) return false;
        if (locale === content || locale === systemDefault) return false;
        return true;
    };

    function googleTranslateElementInit() {
        if (!document.getElementById('google_translate_element')) {
            return;
        }

        new google.translate.TranslateElement({
            pageLanguage: window.khubPageLanguageGoogle || 'en',
            autoDisplay: false,
            disableAutoHover: true,
            showBanner: false
        }, 'google_translate_element');

        window.khubGoogleTranslateReady = true;

        if (typeof window.khubPendingGoogleTranslate === 'string' && window.khubPendingGoogleTranslate !== '') {
            var pending = window.khubPendingGoogleTranslate;
            window.khubPendingGoogleTranslate = null;
            if (typeof window.khubApplyGoogleTranslate === 'function') {
                window.khubApplyGoogleTranslate(pending);
            }
        }
    }

    function GTranslateGetCurrentLang() {
        var keyValue = document['cookie'].match('(^|;) ?googtrans=([^;]*)(;|$)');
        return keyValue ? keyValue[2].split('/')[2] : null;
    }

    var khubGoogleCodes = @json($googleCodesByLocale);

    function khubGoogleCodeForLocale(localeCode) {
        if (!localeCode) return window.khubPageLanguageGoogle || 'en';
        return (khubGoogleCodes && khubGoogleCodes[localeCode]) ? khubGoogleCodes[localeCode] : localeCode;
    }

    function khubFireComboChange(teCombo) {
        if (!teCombo) return;
        try {
            teCombo.dispatchEvent(new Event('change', { bubbles: true }));
        } catch (e) {}
        GTranslateFireEvent(teCombo, 'change');
        if (typeof jQuery !== 'undefined' && jQuery) {
            jQuery(teCombo).trigger('change');
        }
    }

    function GTranslateFireEvent(element, event) {
        try {
            if (document.createEventObject) {
                var evt = document.createEventObject();
                element.fireEvent('on' + event, evt);
            } else {
                var evt = document.createEvent('HTMLEvents');
                evt.initEvent(event, true, true);
                element.dispatchEvent(evt);
            }
        } catch (e) {}
    }

    function khubWhenGoogleTranslateReady(callback, attempt) {
        attempt = attempt || 0;
        if (window.khubGoogleTranslateReady && typeof google !== 'undefined' && google.translate) {
            callback();
            return;
        }
        if (attempt < 40) {
            setTimeout(function () {
                khubWhenGoogleTranslateReady(callback, attempt + 1);
            }, 250);
        }
    }

    function doGTranslate(lang_code) {
        var pageLang = window.khubPageLanguageGoogle || 'en';
        var contentLocale = window.khubContentLocale || 'en';
        var systemDefault = window.khubSystemDefaultLocale || contentLocale;
        var lang = lang_code || pageLang;

        // Map google code back to app locale when possible.
        var matchedLocale = null;
        if (window.khubLangMeta) {
            for (var loc in window.khubLangMeta) {
                if (!Object.prototype.hasOwnProperty.call(window.khubLangMeta, loc)) continue;
                var gc = window.khubLangMeta[loc].google_code || loc;
                if (gc === lang || loc === lang) {
                    matchedLocale = loc;
                    break;
                }
            }
        }
        var localeForGate = matchedLocale || lang;
        if (typeof window.khubShouldUseGoogleTranslate === 'function'
            && !window.khubShouldUseGoogleTranslate(localeForGate)
            && (lang === pageLang || lang === contentLocale || lang === systemDefault
                || localeForGate === contentLocale || localeForGate === systemDefault)) {
            lang = pageLang;
        }

        if (!window.khubGoogleTranslateReady) {
            if (typeof window.khubShouldUseGoogleTranslate === 'function'
                && !window.khubShouldUseGoogleTranslate(localeForGate)) {
                window.khubPendingGoogleTranslate = null;
                return;
            }
            window.khubPendingGoogleTranslate = lang;
            return;
        }

        // Clear when authored content language OR site default (native UI translations).
        if (lang === pageLang || lang === contentLocale || lang === systemDefault
            || localeForGate === contentLocale || localeForGate === systemDefault
            || (typeof window.khubShouldUseGoogleTranslate === 'function'
                && !window.khubShouldUseGoogleTranslate(localeForGate))) {
            var clearDate = new Date();
            clearDate.setTime(clearDate.getTime() - 1);
            document.cookie = "googtrans=; expires=" + clearDate.toUTCString() + "; path={{ $localeCookiePath }}";

            if (typeof window.khubClearGoogleTranslationState === 'function') {
                window.khubClearGoogleTranslationState();
            } else if (typeof jQuery !== 'undefined' && jQuery) {
                jQuery('body').removeClass('translated-rtl');
                jQuery('html').removeClass('translated-rtl');
                jQuery('head').find('link[href*="translate.googleapis.com"]').remove();
                jQuery('[style*="direction"]').css('direction', '');
            }

            if (typeof window.khubApplyDocumentDirection === 'function') {
                window.khubApplyDocumentDirection(contentLocale);
            }

            var teComboEn = document.querySelector('select.goog-te-combo:not(.menu-language-menu-container select)');
            if (teComboEn) {
                var enIndex = Array.from(teComboEn.options).findIndex(function(option) {
                    return option.value === pageLang || option.value === '' || option.value === contentLocale;
                });
                if (enIndex !== -1) {
                    teComboEn.selectedIndex = enIndex;
                    khubFireComboChange(teComboEn);
                    setTimeout(function () { khubFireComboChange(teComboEn); }, 150);
                }
            }
            return;
        }

        var teCombo = document.querySelector('select.goog-te-combo:not(.menu-language-menu-container select)');

        if (!teCombo || !teCombo.innerHTML) {
            setTimeout(function () { doGTranslate(lang_code); }, 500);
            return;
        }

        var langIndex = Array.from(teCombo.options).findIndex(function(option) {
            return option.value === lang;
        });

        if (langIndex !== -1) {
            teCombo.selectedIndex = langIndex;
            khubFireComboChange(teCombo);
            setTimeout(function () { khubFireComboChange(teCombo); }, 150);
        }

        setTimeout(function() {
            if (langIndex === -1 || !teCombo) return;
            if (teCombo.selectedIndex !== langIndex) {
                teCombo.selectedIndex = langIndex;
            }
            var current = GTranslateGetCurrentLang();
            if (current !== lang) {
                khubFireComboChange(teCombo);
            }
        }, 400);

        var date = new Date();
        date.setTime(date.getTime() + (365 * 24 * 60 * 60 * 1000));
        document.cookie = "googtrans=/auto/" + lang + "; expires=" + date.toUTCString() + "; path={{ $localeCookiePath }}";

        if (typeof window.khubApplyDocumentDirection === 'function' && window.khubLangMeta) {
            var matchedLocale = null;
            for (var loc in window.khubLangMeta) {
                if (!Object.prototype.hasOwnProperty.call(window.khubLangMeta, loc)) continue;
                var gc = window.khubLangMeta[loc].google_code || loc;
                if (gc === lang || loc === lang) {
                    matchedLocale = loc;
                    break;
                }
            }
            if (matchedLocale) {
                window.khubApplyDocumentDirection(matchedLocale);
            }
        }
    }

    window.khubApplyGoogleTranslate = doGTranslate;

    (function () {
        function clearGoogleTranslateCookie() {
            var clearDate = new Date();
            clearDate.setTime(clearDate.getTime() - 1);
            document.cookie = "googtrans=; expires=" + clearDate.toUTCString() + "; path={{ $localeCookiePath }}";
            document.cookie = "googtrans=; expires=" + clearDate.toUTCString() + "; path=/";
        }

        function bootGoogleTranslateFromCookies() {
            var cookieLang = GTranslateGetCurrentLang();
            var userLang = window.khubActiveUiLocale || @json($uiLocale);
            var contentLocale = window.khubContentLocale || 'en';
            var pageLang = window.khubPageLanguageGoogle || 'en';
            var googleLang = khubGoogleCodeForLocale(userLang);

            // Site default (and content language) use packaged Laravel translations only.
            if (!window.khubShouldUseGoogleTranslate(userLang)) {
                clearGoogleTranslateCookie();
                if (typeof window.khubClearGoogleTranslationState === 'function') {
                    window.khubClearGoogleTranslationState();
                }
                if (typeof window.khubApplyDocumentDirection === 'function') {
                    window.khubApplyDocumentDirection(userLang || contentLocale);
                }
                return;
            }

            if (userLang && userLang !== contentLocale) {
                if (cookieLang !== googleLang) {
                    var date = new Date();
                    date.setTime(date.getTime() + (365 * 24 * 60 * 60 * 1000));
                    document.cookie = "googtrans=/auto/" + googleLang + "; expires=" + date.toUTCString() + "; path={{ $localeCookiePath }}";
                }
                doGTranslate(googleLang);
            } else if (cookieLang && cookieLang !== pageLang) {
                doGTranslate(cookieLang);
            } else {
                clearGoogleTranslateCookie();
                if (typeof window.khubApplyDocumentDirection === 'function') {
                    window.khubApplyDocumentDirection(userLang || contentLocale);
                }
            }
        }

        if (typeof jQuery !== 'undefined' && jQuery) {
            jQuery(function () {
                if (window.khubShouldUseGoogleTranslate(window.khubActiveUiLocale)) {
                    khubWhenGoogleTranslateReady(bootGoogleTranslateFromCookies);
                } else {
                    bootGoogleTranslateFromCookies();
                }
                jQuery('.selectpicker').selectpicker();
            });
        } else {
            document.addEventListener('DOMContentLoaded', function () {
                if (window.khubShouldUseGoogleTranslate(window.khubActiveUiLocale)) {
                    khubWhenGoogleTranslateReady(bootGoogleTranslateFromCookies);
                } else {
                    bootGoogleTranslateFromCookies();
                }
            });
        }
    })();
</script>
{{-- Load GT widget only on the public site, and only when UI is not the site default / content language. --}}
@php
    $khubLoadGoogleTranslate = ! \App\Models\SiteLanguage::isBackendRequest()
        && $uiLocale !== $systemDefaultLocale
        && $uiLocale !== $contentLocale;
@endphp
@if($khubLoadGoogleTranslate)
<script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
@endif
