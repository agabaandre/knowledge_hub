@php
    // Use same logic as footer - check user preference first, then cookie, then default to 'en'
    $currentLang = 'en';
    if (auth()->check() && isset(current_user()->langauge) && !empty(current_user()->langauge)) {
        $currentLang = current_user()->langauge;
    } elseif (isset($_COOKIE['googtrans']) && !empty($_COOKIE['googtrans'])) {
        $cookieLang = explode('/', $_COOKIE['googtrans']);
        if (isset($cookieLang[2]) && !empty($cookieLang[2])) {
            $currentLang = $cookieLang[2];
        }
    }
    $languages = [
        'en' => ['name' => 'English', 'flag' => '🇺🇸', 'code' => 'en'],
        'fr' => ['name' => 'Français', 'flag' => '🇫🇷', 'code' => 'fr'],
        'ar' => ['name' => 'العربية', 'flag' => '🇸🇦', 'code' => 'ar'],
        'es' => ['name' => 'Español', 'flag' => '🇪🇸', 'code' => 'es'],
        'pt' => ['name' => 'Português', 'flag' => '🇵🇹', 'code' => 'pt'],
        'sw' => ['name' => 'Kiswahili', 'flag' => '🇰🇪', 'code' => 'sw'],
    ];
    $currentLanguage = $languages[$currentLang] ?? $languages['en'];
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
        background: var(--theme-color-primary, #119A48) !important;
        border: 1px solid var(--theme-color-primary, #119A48) !important;
        border-radius: 6px;
        color: #fff !important;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s ease;
        white-space: nowrap;
    }

    .language-selector-btn:hover {
        background: color-mix(in srgb, var(--theme-color-primary, #119A48) 85%, black) !important;
        border-color: color-mix(in srgb, var(--theme-color-primary, #119A48) 85%, black) !important;
        color: #fff !important;
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
            z-index: 9999999 !important;
            transform: translateX(0);
            max-width: calc(100vw - 20px) !important;
            min-width: 180px !important;
        }
        
        .language-selector-wrapper.active .language-dropdown {
            max-height: 400px !important;
            opacity: 1 !important;
            visibility: visible !important;
            display: block !important;
            z-index: 9999999 !important;
        }
        
        .language-selector-wrapper {
            position: relative;
            z-index: 9999998 !important;
        }
        
        .menu-language-menu-container {
            position: relative;
            z-index: 9999998 !important;
        }
        
        .language-selector-btn {
            position: relative;
            z-index: 9999998 !important;
        }
    }
</style>

<div class="menu-language-menu-container">
    <div class="language-selector-wrapper" id="languageSelector">
        <button type="button" class="language-selector-btn notranslate" id="languageSelectorBtn">
            <span class="flag-icon">{{ $currentLanguage['flag'] }}</span>
            <span class="lang-code">{{ strtoupper($currentLanguage['code']) }}</span>
            <i class="fa fa-chevron-down chevron"></i>
        </button>
        <div class="language-dropdown" id="languageDropdown">
            <ul class="language-dropdown-list">
                @foreach($languages as $code => $lang)
                    <li class="language-dropdown-item">
                        <a href="#" class="language-dropdown-link notranslate {{ $code === $currentLang ? 'active' : '' }}" 
                           data-lang="{{ $code }}" 
                           onclick="changeLanguage('{{ $code }}'); return false;">
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
                        var topPosition;
                        if (spaceBelow >= dropdownHeight || spaceBelow > spaceAbove) {
                            // Place below button
                            topPosition = rect.bottom + 8;
                        } else {
                            // Place above button (if more space above)
                            topPosition = rect.top - dropdownHeight - 8;
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
                        languageDropdown.style.zIndex = '9999999';
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
    
    function getCurrentLang() {
        // First priority: Use server-provided language (from user's saved preference or cookie)
        var serverLang = '{{ $currentLang }}';
        
        // Second priority: Check cookie
        var keyValue = document.cookie.match('(^|;) ?googtrans=([^;]*)(;|$)');
        var cookieLang = null;
        if (keyValue) {
            cookieLang = keyValue[2].split('/')[2];
        }
        
        @auth
        // For logged-in users, always use server-provided language first (their saved preference)
        // The server-side logic in langselect.blade.php already checks user preference first
        // So serverLang should reflect the user's saved preference if they have one
        if (serverLang) {
            return serverLang;
        }
        // Fallback to cookie if serverLang is somehow empty
        return cookieLang || 'en';
        @else
        // Guest users: use cookie if available, otherwise default to 'en'
        return cookieLang || serverLang || 'en';
        @endauth
    }

    function updateLanguageUI(langCode) {
        var langMap = {
            'en': {flag: '🇺🇸', code: 'EN'},
            'fr': {flag: '🇫🇷', code: 'FR'},
            'ar': {flag: '🇸🇦', code: 'AR'},
            'es': {flag: '🇪🇸', code: 'ES'},
            'pt': {flag: '🇵🇹', code: 'PT'},
            'sw': {flag: '🇰🇪', code: 'SW'}
        };

        var current = langMap[langCode] || langMap['en'];
        
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
        console.log('Initializing language UI with:', currentLang);
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

    // Global changeLanguage function
    window.changeLanguage = function(langCode) {
        updateLanguageUI(langCode);
        
        // Close all dropdowns
        var selectors = document.querySelectorAll('#languageSelector');
        selectors.forEach(function(selector) {
            selector.classList.remove('active');
            var dropdown = selector.querySelector('#languageDropdown');
            if (dropdown) {
                dropdown.style.cssText = '';
            }
        });
        
        // Save language preference (non-blocking, handles errors gracefully)
        @auth
        if (typeof window.jQuery !== 'undefined' && window.jQuery && window.jQuery.ajax) {
            // Get current user preferences as JSON array
            @php
                $userPreferences = [];
                if (auth()->check() && current_user()) {
                    $userPreferences = current_user()->preferences()->pluck('subtheme_id')->toArray();
                }
            @endphp
            
            window.jQuery.ajax({
                url: '{{ route("account.update") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    langauge: langCode,
                    id: {{ current_user()->id ?? 0 }},
                    first_name: '{{ current_user()->first_name ?? "" }}',
                    last_name: '{{ current_user()->last_name ?? "" }}',
                    email: '{{ current_user()->email ?? "" }}',
                    preferences: {{ json_encode($userPreferences) }}
                },
                success: function() {
                    console.log('Language preference saved');
                },
                error: function(xhr, status, error) {
                    // Fail silently - language change via Google Translate still works
                    console.log('Language preference save failed (non-critical):', error);
                }
            });
        }
        @endauth
        
        // Special handling for English - remove translation without reload
        if (langCode === 'en') {
            // Clear the translation cookie
            var date = new Date();
            date.setTime(date.getTime() - 1); // Expire immediately
            document.cookie = "googtrans=; expires=" + date.toUTCString() + "; path=/";
            
            // Remove translation classes and styles using vanilla JS (no jQuery dependency)
            document.body.classList.remove('translated-rtl');
            document.documentElement.classList.remove('translated-rtl');
            
            // Remove Google Translate stylesheet links
            var translateLinks = document.querySelectorAll('head link[href*="translate.googleapis.com"]');
            translateLinks.forEach(function(link) {
                link.remove();
            });
            
            // Remove inline direction styles
            var elementsWithDirection = document.querySelectorAll('[style*="direction"]');
            elementsWithDirection.forEach(function(el) {
                if (el.style.direction) {
                    el.style.direction = '';
                }
            });
            
            // Try to revert translation using Google Translate widget (if available)
            var teCombo = document.querySelector('select.goog-te-combo:not(.menu-language-menu-container select)');
            if (teCombo) {
                // Find English option
                var enIndex = Array.from(teCombo.options).findIndex(function(option) {
                    return option.value === 'en' || option.value === '';
                });
                if (enIndex !== -1) {
                    teCombo.selectedIndex = enIndex;
                    // Fire change event
                    try {
                        if (typeof GTranslateFireEvent === 'function') {
                            GTranslateFireEvent(teCombo, 'change');
                        } else {
                            // Fallback: create and dispatch event
                            var event = new Event('change', { bubbles: true });
                            teCombo.dispatchEvent(event);
                        }
                    } catch (e) {
                        console.log('Error firing translate event:', e);
                    }
                }
            }
            
            // Save English preference cookie
            date = new Date();
            date.setTime(date.getTime() + (365 * 24 * 60 * 60 * 1000)); // 1 year
            document.cookie = "googtrans=; expires=" + date.toUTCString() + "; path=/";
            
            console.log('Translation removed (English selected)');
            return false;
        }
        
        // For non-English languages, use doGTranslate function
        var translationAttempts = 0;
        var maxAttempts = 50; // Wait up to 5 seconds (50 * 100ms)
        
        function triggerTranslation() {
            translationAttempts++;
            
            // Check if doGTranslate function is available
            if (typeof doGTranslate === 'function') {
                // Also check if jQuery is available (doGTranslate uses it)
                if (typeof window.jQuery === 'undefined' && typeof $ === 'undefined') {
                    if (translationAttempts < maxAttempts) {
                        setTimeout(triggerTranslation, 100);
                        return;
                    }
                }
                
                try {
                    console.log('Calling doGTranslate with language:', langCode);
                    doGTranslate(langCode);
                } catch (e) {
                    console.error('Translation error:', e);
                    // Fallback: set cookie and reload page (only for non-English)
                    var date = new Date();
                    date.setTime(date.getTime() + (365 * 24 * 60 * 60 * 1000));
                    document.cookie = "googtrans=/auto/" + langCode + "; expires=" + date.toUTCString() + "; path=/";
                    window.location.reload();
                }
            } else {
                // Function not loaded yet, wait a bit and try again
                if (translationAttempts < maxAttempts) {
                    setTimeout(triggerTranslation, 100);
                } else {
                    // Still not available after max attempts, use cookie fallback
                    console.log('doGTranslate not available after ' + maxAttempts + ' attempts, using cookie fallback');
                    var date = new Date();
                    date.setTime(date.getTime() + (365 * 24 * 60 * 60 * 1000));
                    document.cookie = "googtrans=/auto/" + langCode + "; expires=" + date.toUTCString() + "; path=/";
                    window.location.reload();
                }
            }
        }
        
        // Start translation attempt
        triggerTranslation();
        
        return false;
    };
})();
</script>
