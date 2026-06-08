<div id="google_translate_element" style="display:none;"></div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/js/bootstrap-select.min.js" type="text/javascript"></script>
@php
    use App\Models\SiteLanguage;
    $uiLocale = active_ui_locale();
    $localeCookiePath = config('supported_locales.cookie_path', '/');
    $basePath = request()->getBasePath();
    if (is_string($basePath) && $basePath !== '' && $basePath !== '/') {
        $localeCookiePath = rtrim($basePath, '/').'/';
    }
    $googleCodesByLocale = collect(SiteLanguage::selectorMap())
        ->mapWithKeys(fn ($row, $code) => [$code => $row['google_code'] ?? $code])
        ->all();
@endphp
<script type="text/javascript">
    window.khubGoogleTranslateReady = false;
    window.khubPendingGoogleTranslate = null;

    function googleTranslateElementInit() {
        if (!document.getElementById('google_translate_element')) {
            return;
        }

        new google.translate.TranslateElement({
            pageLanguage: 'en',
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
        if (!localeCode) return 'en';
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
        var lang = lang_code || 'en';

        if (!window.khubGoogleTranslateReady) {
            window.khubPendingGoogleTranslate = lang;
            return;
        }

        if (lang === 'en') {
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
                window.khubApplyDocumentDirection('en');
            }

            var teComboEn = document.querySelector('select.goog-te-combo:not(.menu-language-menu-container select)');
            if (teComboEn) {
                var enIndex = Array.from(teComboEn.options).findIndex(function(option) {
                    return option.value === 'en' || option.value === '';
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
            var pageLang = GTranslateGetCurrentLang();
            if (pageLang !== lang) {
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
        function bootGoogleTranslateFromCookies() {
            var cookieLang = GTranslateGetCurrentLang();
            var userLang = @json($uiLocale);
            var googleLang = khubGoogleCodeForLocale(userLang);

            if (userLang && userLang !== 'en') {
                if (cookieLang !== googleLang) {
                    var date = new Date();
                    date.setTime(date.getTime() + (365 * 24 * 60 * 60 * 1000));
                    document.cookie = "googtrans=/auto/" + googleLang + "; expires=" + date.toUTCString() + "; path={{ $localeCookiePath }}";
                }
                doGTranslate(googleLang);
            } else if (cookieLang && cookieLang !== 'en') {
                doGTranslate(cookieLang);
            } else {
                var clearDate = new Date();
                clearDate.setTime(clearDate.getTime() - 1);
                document.cookie = "googtrans=; expires=" + clearDate.toUTCString() + "; path={{ $localeCookiePath }}";
                if (typeof window.khubApplyDocumentDirection === 'function') {
                    window.khubApplyDocumentDirection('en');
                }
            }
        }

        if (typeof jQuery !== 'undefined' && jQuery) {
            jQuery(function () {
                khubWhenGoogleTranslateReady(bootGoogleTranslateFromCookies);
                jQuery('.selectpicker').selectpicker();
            });
        } else {
            document.addEventListener('DOMContentLoaded', function () {
                khubWhenGoogleTranslateReady(bootGoogleTranslateFromCookies);
            });
        }
    })();
</script>
<script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
