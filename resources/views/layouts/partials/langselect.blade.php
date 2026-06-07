@php
    use App\Models\SiteLanguage;
    $translateFilled = (bool)(settings()->translate_button_filled ?? true);
    $translateTextColor = settings()->translate_button_text_color ?? '#ffffff';
    $languages = SiteLanguage::selectorMap();
    $googCookie = $_COOKIE['googtrans'] ?? null;
    $khubCookie = request()->cookie(config('supported_locales.locale_cookie')) ?? null;
    $userLocalePref = auth()->check() ? (current_user()->langauge ?? null) : null;
    $currentLang = SiteLanguage::resolveActiveLocale($userLocalePref, $googCookie, $khubCookie);
    if (! isset($languages[$currentLang])) {
        $currentLang = array_key_first($languages) ?: 'en';
    }
    $currentLanguage = $languages[$currentLang] ?? null;
    if ($currentLanguage === null) {
        $first = reset($languages);
        $currentLanguage = is_array($first) ? $first : ['name' => 'English', 'flag' => '', 'code' => 'en', 'google_code' => 'en'];
    }
@endphp

<style>
    .menu-language-menu-container {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        width: 100%;
    }

    .language-selector-wrapper {
        position: relative;
        display: inline-block;
        margin-right: 0;
        margin-left: auto;
    }

    .language-selector-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s ease;
        white-space: nowrap;
        @if($translateFilled)
        border: 1px solid var(--theme-color-primary, #119A48);
        background: var(--theme-color-primary, #119A48) !important;
        color: {{ $translateTextColor }} !important;
        @else
        border: none !important;
        background: transparent !important;
        color: var(--theme-color-primary, #119A48) !important;
        @endif
    }

    .language-selector-btn:hover {
        @if($translateFilled)
        background: color-mix(in srgb, var(--theme-color-primary, #119A48) 85%, black) !important;
        border-color: color-mix(in srgb, var(--theme-color-primary, #119A48) 85%, black) !important;
        color: {{ $translateTextColor }} !important;
        @else
        background: rgba(0,0,0,0.06) !important;
        border: none !important;
        color: color-mix(in srgb, var(--theme-color-primary, #119A48) 85%, black) !important;
        @endif
    }

    .language-selector-btn .flag-icon {
        font-size: 18px;
        line-height: 1;
    }

    .language-selector-btn .chevron {
        font-size: 12px;
        transition: transform 0.3s ease;
    }

    .language-selector-wrapper.active .language-selector-btn .chevron {
        transform: rotate(180deg);
    }

    .language-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        min-width: 200px;
        max-height: 0;
        overflow: hidden;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 9999999;
        display: block;
    }

    .language-selector-wrapper.active .language-dropdown {
        max-height: 400px !important;
        opacity: 1 !important;
        visibility: visible !important;
        display: block !important;
    }

    .language-dropdown-list {
        list-style: none;
        padding: 8px 0;
        margin: 0;
    }

    .language-dropdown-item {
        padding: 0;
        margin: 0;
    }

    .language-dropdown-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 20px;
        color: #333;
        text-decoration: none;
        transition: background 0.2s ease;
        cursor: pointer;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        font-size: 14px;
    }

    .language-dropdown-link:hover {
        background: #f5f5f5;
        color: #000;
    }

    .language-dropdown-link .flag {
        font-size: 20px;
        line-height: 1;
        flex-shrink: 0;
    }

    .language-dropdown-link .lang-name {
        flex: 1;
    }

    .language-dropdown-link.active {
        background: #e8f5e9;
        color: #2e7d32;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .language-selector-btn {
            padding: 6px 12px;
            font-size: 13px;
        }
        
        .language-dropdown {
            right: 0;
            left: auto;
        }
    }
    
    /* Mobile phone specific styles (max-width: 767px, not tablets) */
    @media (max-width: 767px) {
        .language-selector-btn {
            padding: 6px 10px;
            font-size: 12px;
            border-radius: 4px;
            -webkit-tap-highlight-color: transparent;
            touch-action: manipulation;
        }
        
        .language-selector-btn .lang-code {
            display: none; /* Hide language code on very small screens, show only flag */
        }
        
        .language-dropdown {
            right: 10px !important;
            left: auto !important;
            position: fixed !important;
            z-index: 99999999 !important;
            transform: translateX(0);
            max-width: calc(100vw - 20px) !important;
            min-width: 180px !important;
            isolation: isolate; /* Create new stacking context */
        }
        
        .language-selector-wrapper.active .language-dropdown {
            max-height: 400px !important;
            opacity: 1 !important;
            visibility: visible !important;
            display: block !important;
            z-index: 99999999 !important;
        }
        
        .language-selector-wrapper {
            position: relative;
            z-index: 99999998 !important;
            isolation: isolate; /* Create new stacking context */
        }
        
        .menu-language-menu-container {
            position: relative;
            z-index: 99999998 !important;
            isolation: isolate; /* Create new stacking context */
        }
        
        .language-selector-btn {
            position: relative;
            z-index: 99999998 !important;
        }
        
        /* Ensure language dropdown is above navigation menu on mobile */
        #navigation ~ * .language-dropdown,
        .language-dropdown {
            z-index: 99999999 !important;
        }
    }
</style>

<div class="menu-language-menu-container">
    <div class="language-selector-wrapper" id="languageSelector">
        <button type="button" class="language-selector-btn notranslate" id="languageSelectorBtn">
            <span class="flag-icon">{{ $currentLanguage['flag'] }}</span>
            <span class="lang-code">{{ strtoupper($currentLanguage['code'] ?? $currentLang) }}</span>
            <i class="fa fa-chevron-down chevron"></i>
        </button>
        <div class="language-dropdown" id="languageDropdown">
            <ul class="language-dropdown-list">
                @foreach($languages as $code => $lang)
                    <li class="language-dropdown-item">
                        <a href="#" class="language-dropdown-link notranslate {{ $code === $currentLang ? 'active' : '' }}" 
                           data-lang="{{ $code }}" 
                           data-google="{{ $lang['google_code'] ?? $code }}"
                           onclick="changeLanguage({{ json_encode($code) }}, {{ json_encode($lang['google_code'] ?? $code) }}); return false;">
                            <span class="flag">{{ $lang['flag'] }}</span>
                            <span class="lang-name">{{ $lang['name'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

<script>
// Language Selector - Pure Vanilla JS, No jQuery Required
(function() {
    'use strict';
    
    var initialized = false;
    var handlersAttached = false;
    var touchHandled = false;
    
    // Document-level handlers (attach once globally)
    function handleDocumentClick(e) {
        var selectors = document.querySelectorAll('#languageSelector');
        selectors.forEach(function(languageSelector) {
            if (!languageSelector.contains(e.target)) {
                if (languageSelector.classList.contains('active')) {
                    var languageDropdown = languageSelector.querySelector('#languageDropdown');
                    languageSelector.classList.remove('active');
                    if (languageDropdown && window.innerWidth <= 767) {
                        languageDropdown.style.cssText = '';
                    }
                }
            }
        });
    }
    
    function handleDocumentTouch(e) {
        var selectors = document.querySelectorAll('#languageSelector');
        selectors.forEach(function(languageSelector) {
            if (!languageSelector.contains(e.target)) {
                if (languageSelector.classList.contains('active')) {
                    var languageDropdown = languageSelector.querySelector('#languageDropdown');
                    languageSelector.classList.remove('active');
                    if (languageDropdown && window.innerWidth <= 767) {
                        languageDropdown.style.cssText = '';
                    }
                }
            }
        });
    }
    
    function initLanguageSelector() {
        // Attach document handlers once (if DOM is ready)
        if (!handlersAttached && document.body) {
            document.addEventListener('click', handleDocumentClick);
            document.addEventListener('touchstart', handleDocumentTouch);
            handlersAttached = true;
        }
        // Get all language selector instances (can be multiple on page)
        var selectors = document.querySelectorAll('#languageSelector');
        
        if (selectors.length === 0) {
            // Elements not ready, retry
            if (!initialized) {
                setTimeout(initLanguageSelector, 150);
            }
            return;
        }
        
        // Check if mobile
        function isMobile() {
            return window.innerWidth <= 767;
        }
        
        // Initialize each selector instance (only if not already initialized)
        selectors.forEach(function(languageSelector) {
            // Skip if already has event listeners
            if (languageSelector.dataset.initialized === 'true') {
                return;
            }
            
            var languageSelectorBtn = languageSelector.querySelector('#languageSelectorBtn');
            var languageDropdown = languageSelector.querySelector('#languageDropdown');
            
            if (!languageSelectorBtn || !languageDropdown) {
                return; // Skip this instance if elements missing
            }
            
            // Toggle dropdown
            function toggleDropdown() {
                var isActive = languageSelector.classList.contains('active');
                
                if (isActive) {
                    // Close
                    languageSelector.classList.remove('active');
                    if (isMobile()) {
                        languageDropdown.style.cssText = '';
                    }
                } else {
                    // Open
                    languageSelector.classList.add('active');
                    
                    if (isMobile()) {
                        // Mobile: Use fixed positioning relative to viewport
                        var rect = languageSelectorBtn.getBoundingClientRect();
                        var viewportHeight = window.innerHeight;
                        var dropdownHeight = 400; // max height
                        var spaceBelow = viewportHeight - rect.bottom;
                        var spaceAbove = rect.top;
                        
                        // Calculate position - prefer below button, but flip above if not enough space
                        // On mobile, move up by 100px from button position
                        var topPosition;
                        if (spaceBelow >= dropdownHeight || spaceBelow > spaceAbove) {
                            // Place below button, but move up by 100px on mobile
                            topPosition = rect.bottom + 8 - 140;
                            // Ensure it doesn't go above viewport
                            if (topPosition < 8) {
                                topPosition = 8;
                            }
                        } else {
                            // Place above button (if more space above)
                            topPosition = rect.top - dropdownHeight - 8 - 140;
                            if (topPosition < 8) {
                                topPosition = 8; // Ensure minimum margin from top
                            }
                        }
                        
                        // Calculate right position to align with button
                        var rightPosition = Math.max(10, window.innerWidth - rect.right);
                        
                        // For fixed positioning, use viewport coordinates directly (no scrollY)
                        languageDropdown.style.position = 'fixed';
                        languageDropdown.style.top = topPosition + 'px';
                        languageDropdown.style.right = rightPosition + 'px';
                        languageDropdown.style.left = 'auto';
                        languageDropdown.style.maxWidth = Math.min(280, window.innerWidth - 20) + 'px';
                        languageDropdown.style.minWidth = '180px';
                        languageDropdown.style.zIndex = '99999999';
                        languageDropdown.style.isolation = 'isolate';
                        languageDropdown.style.display = 'block';
                        languageDropdown.style.visibility = 'visible';
                        languageDropdown.style.opacity = '1';
                        languageDropdown.style.maxHeight = '400px';
                    } else {
                        // Desktop: Let CSS handle it, clear any mobile styles
                        languageDropdown.style.cssText = '';
                    }
                }
            }
            
            // Button click handler
            function handleButtonClick(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Block if touch was just processed (mobile)
                if (touchHandled) {
                    touchHandled = false;
                    return;
                }
                
                toggleDropdown();
            }
            
            // Touch handler for mobile
            function handleButtonTouch(e) {
                if (isMobile()) {
                    e.preventDefault();
                    e.stopPropagation();
                    touchHandled = true;
                    toggleDropdown();
                    // Reset touch flag
                    setTimeout(function() {
                        touchHandled = false;
                    }, 300);
                }
            }
            
            // Attach button event listeners
            languageSelectorBtn.addEventListener('click', handleButtonClick);
            languageSelectorBtn.addEventListener('touchstart', handleButtonTouch, { passive: false });
            
            // Prevent dropdown clicks from closing
            languageDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
            
            languageDropdown.addEventListener('touchstart', function(e) {
                e.stopPropagation();
            });
            
            // Mark this selector as initialized
            languageSelector.dataset.initialized = 'true';
        });
        
        // Mark as initialized if we found and processed selectors
        if (selectors.length > 0) {
            initialized = true;
        }
    }
    
    // Initialize when ready
    function startInit() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initLanguageSelector);
        } else {
            initLanguageSelector();
        }
    }
    
    startInit();
    
    // Re-check for new selectors if elements are added dynamically
    var observer = new MutationObserver(function(mutations) {
        var uninitialized = document.querySelectorAll('#languageSelector');
        var hasUninitialized = false;
        uninitialized.forEach(function(sel) {
            if (sel.dataset.initialized !== 'true') {
                hasUninitialized = true;
            }
        });
        if (!initialized || hasUninitialized) {
            initLanguageSelector();
        }
    });
    
    if (document.body) {
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
})();

// Language management functions - No jQuery required
(function() {
    'use strict';

    window.khubLangMeta = @json($languages);
    var khubLocaleCookieName = @json(config('supported_locales.locale_cookie', 'khub_locale'));
    @php
        $runtimeCookiePath = config('supported_locales.cookie_path', '/');
        $basePath = request()->getBasePath();
        if (is_string($basePath) && $basePath !== '' && $basePath !== '/') {
            $runtimeCookiePath = rtrim($basePath, '/').'/';
        }
    @endphp
    var khubLocaleCookiePath = @json($runtimeCookiePath);
    var khubLocaleMaxAgeSec = {{ (int) config('supported_locales.locale_cookie_minutes', 525600) * 60 }};
    var khubLocaleApplyUrl = @json(route('locale.apply'));
    var khubLocaleSwitchTemplate = @json(route('locale.switch', ['locale' => '__LOCALE__']));

    function writeAppCookie(name, value, maxAgeSec) {
        var parts = [
            name + '=' + encodeURIComponent(value),
            'path=' + khubLocaleCookiePath,
            'SameSite=Lax'
        ];
        if (typeof maxAgeSec === 'number') {
            parts.push('max-age=' + maxAgeSec);
        }
        document.cookie = parts.join(';');
    }

    function clearAppCookie(name) {
        writeAppCookie(name, '', 0);
        // Legacy cookies set with path=/ before subdir fix
        document.cookie = name + '=;path=/;max-age=0;SameSite=Lax';
    }

    function readCookie(name) {
        var m = document.cookie.match('(^|;) ?' + name + '=([^;]*)(;|$)');
        return m ? decodeURIComponent(m[2]) : null;
    }

    function resolveLocaleFromGoogleCookie() {
        var raw = readCookie('googtrans');
        if (!raw) return null;
        var g = raw.split('/')[2] || '';
        if (!g) return null;
        var meta = window.khubLangMeta || {};
        for (var loc in meta) {
            if (!Object.prototype.hasOwnProperty.call(meta, loc)) continue;
            var gc = (meta[loc].google_code || loc);
            if (gc === g || loc === g) return loc;
        }
        return g;
    }

    function getCurrentLang() {
        var serverLang = '{{ $currentLang }}';
        var khub = readCookie(khubLocaleCookieName);
        if (khub && window.khubLangMeta && window.khubLangMeta[khub]) return khub;
        if (serverLang && window.khubLangMeta && window.khubLangMeta[serverLang]) return serverLang;
        return resolveLocaleFromGoogleCookie() || serverLang || 'en';
    }

    function updateLanguageUI(langCode) {
        var meta = (window.khubLangMeta && window.khubLangMeta[langCode]) || {};
        var current = {
            flag: meta.flag || '',
            code: (langCode || 'en').toString().substring(0, 2).toUpperCase()
        };
        if (langCode && langCode.length > 2) {
            current.code = langCode.toUpperCase().replace(/[^A-Z0-9]/g, '').substring(0, 6) || 'EN';
        }
        
        // Update button (all instances)
        var buttons = document.querySelectorAll('#languageSelectorBtn');
        buttons.forEach(function(btn) {
            var flagIcon = btn.querySelector('.flag-icon');
            var langCodeSpan = btn.querySelector('.lang-code');
            if (flagIcon) flagIcon.textContent = current.flag;
            if (langCodeSpan) langCodeSpan.textContent = current.code;
        });
        
        // Update dropdown links
        var links = document.querySelectorAll('.language-dropdown-link');
        links.forEach(function(link) {
            link.classList.remove('active');
            if (link.getAttribute('data-lang') === langCode) {
                link.classList.add('active');
            }
        });
        
        // jQuery fallback if available
        if (typeof window.jQuery !== 'undefined' && window.jQuery) {
            window.jQuery('.language-dropdown-link').removeClass('active');
            window.jQuery('.language-dropdown-link[data-lang="' + langCode + '"]').addClass('active');
        }
    }

    // Update UI on load - check immediately and also after a short delay to catch any changes
    function initializeLanguageUI() {
        var currentLang = getCurrentLang();
        updateLanguageUI(currentLang);
    }
    
    // Update immediately if DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeLanguageUI);
    } else {
        initializeLanguageUI();
    }
    
    // Also update after a short delay to catch any late changes from cookie/translation
    setTimeout(initializeLanguageUI, 500);

    function closeLanguageDropdowns() {
        document.querySelectorAll('#languageSelector').forEach(function(selector) {
            selector.classList.remove('active');
            var dropdown = selector.querySelector('#languageDropdown');
            if (dropdown) {
                dropdown.style.cssText = '';
            }
        });
    }

    function swapLocaleFragments(fragments) {
        if (!fragments || typeof fragments !== 'object') return;

        Object.keys(fragments).forEach(function(id) {
            var el = document.getElementById(id);
            if (el && fragments[id]) {
                el.outerHTML = fragments[id];
            }
        });
    }

    function khubApplyNativeLabels(labels) {
        if (!labels || typeof labels !== 'object') return;

        Object.keys(labels).forEach(function(fullKey) {
            var value = labels[fullKey];
            if (value == null || value === '') return;

            document.querySelectorAll('[data-khub-i18n="' + fullKey + '"]').forEach(function(el) {
                if (el.tagName === 'INPUT') {
                    if (el.type === 'submit' || el.type === 'button') {
                        el.value = value;
                    } else {
                        el.placeholder = value;
                    }
                    return;
                }

                var textTarget = el.querySelector('.khub-i18n-text');
                if (textTarget) {
                    textTarget.textContent = value;
                    return;
                }

                if (el.tagName === 'OPTION') {
                    el.textContent = value;
                    return;
                }

                el.textContent = value;
            });
        });
    }

    function khubReinitNavigation() {
        if (typeof window.jQuery === 'undefined' || !window.jQuery) return;
        var $nav = window.jQuery('#navigation');
        if (!$nav.length) return;
        $nav.removeData('navigation');
        $nav.find('.nav-menus-wrapper-close-button').remove();
        if (typeof $nav.navigation === 'function') {
            $nav.navigation();
        }
    }

    function khubRebindCookieButtons() {
        if (typeof window.jQuery === 'undefined' || !window.jQuery) return;
        window.jQuery('.allow-button').off('click.khubLocale').on('click.khubLocale', function () {
            var allow = window.jQuery(this).attr('allow');
            if (parseInt(allow, 10) === 1) {
                var date = new Date();
                date.setTime(date.getTime() + (90 * 24 * 60 * 60 * 1000));
                document.cookie = 'is_returning=yes; expires=' + date.toUTCString() + '; path=' + khubLocaleCookiePath;
            }
            window.jQuery('.cookie-consent').hide();
        });
    }

    function khubApplyGoogleTranslateWhenReady(googleCode, attempt) {
        attempt = attempt || 0;
        if (typeof window.khubApplyGoogleTranslate === 'function') {
            window.khubApplyGoogleTranslate(googleCode);
            return;
        }
        if (attempt < 25) {
            setTimeout(function() {
                khubApplyGoogleTranslateWhenReady(googleCode, attempt + 1);
            }, 200);
        }
    }

    function khubAfterLocaleSwap(googleCode, labels) {
        if (typeof window.khubInitMegaMenus === 'function') {
            window.khubInitMegaMenus();
        }
        khubReinitNavigation();
        khubRebindCookieButtons();
        khubApplyNativeLabels(labels);
        if (typeof window.jQuery !== 'undefined' && window.jQuery) {
            window.jQuery('[data-toggle="tooltip"]').tooltip();
        }
        khubApplyGoogleTranslateWhenReady(googleCode);
        document.documentElement.setAttribute('lang', googleCode || 'en');
    }

    function khubFallbackLocaleRedirect(localeCode) {
        var redirectTarget = window.location.pathname + window.location.search + window.location.hash;
        var switchUrl = khubLocaleSwitchTemplate.replace('__LOCALE__', encodeURIComponent(localeCode));
        switchUrl += (switchUrl.indexOf('?') === -1 ? '?' : '&') + 'redirect=' + encodeURIComponent(redirectTarget);
        window.location.href = switchUrl;
    }

    // Global changeLanguage: AJAX swap for native nav/footer + Google Translate for page body.
    window.changeLanguage = function(localeCode, googleCode) {
        googleCode = googleCode || localeCode;

        writeAppCookie(khubLocaleCookieName, localeCode, khubLocaleMaxAgeSec);
        if (localeCode === 'en') {
            clearAppCookie('googtrans');
        } else {
            writeAppCookie('googtrans', '/auto/' + googleCode, khubLocaleMaxAgeSec);
        }

        updateLanguageUI(localeCode);
        closeLanguageDropdowns();

        var csrfToken = document.querySelector('meta[name="csrf-token"]');
        var headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        };
        if (csrfToken && csrfToken.content) {
            headers['X-CSRF-TOKEN'] = csrfToken.content;
        }

        fetch(khubLocaleApplyUrl, {
            method: 'POST',
            headers: headers,
            credentials: 'same-origin',
            body: JSON.stringify({ locale: localeCode })
        })
        .then(function(response) {
            if (!response.ok) {
                throw new Error('Locale apply failed');
            }
            return response.json();
        })
        .then(function(data) {
            swapLocaleFragments(data.fragments);
            khubAfterLocaleSwap(data.google_code || googleCode, data.labels);
        })
        .catch(function() {
            khubFallbackLocaleRedirect(localeCode);
        });

        return false;
    };
})();
</script>
