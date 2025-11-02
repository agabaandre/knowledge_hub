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
        z-index: 10000;
    }

    .language-selector-wrapper.active .language-dropdown {
        max-height: 400px;
        opacity: 1;
        visibility: visible;
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
        }
        
        .language-selector-btn .lang-code {
            display: none; /* Hide language code on very small screens, show only flag */
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
$(document).ready(function() {
    // Toggle dropdown
    $('#languageSelectorBtn').on('click', function(e) {
        e.stopPropagation();
        $('#languageSelector').toggleClass('active');
    });

    // Close dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#languageSelector').length) {
            $('#languageSelector').removeClass('active');
        }
    });

    // Get current language from cookie
    function getCurrentLang() {
        var keyValue = document.cookie.match('(^|;) ?googtrans=([^;]*)(;|$)');
        if (keyValue) {
            var langCode = keyValue[2].split('/')[2];
            return langCode || 'en';
        }
        return '{{ $currentLang }}';
    }

    // Update UI to show current language
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
        $('#languageSelectorBtn .flag-icon').text(current.flag);
        $('#languageSelectorBtn .lang-code').text(current.code);
        
        // Update active state in dropdown
        $('.language-dropdown-link').removeClass('active');
        $('.language-dropdown-link[data-lang="' + langCode + '"]').addClass('active');
    }

    // Check current language on load
    setTimeout(function() {
        var currentLang = getCurrentLang();
        updateLanguageUI(currentLang);
    }, 1000);

    // Make changeLanguage available globally
    window.changeLanguage = function(langCode) {
        // Update UI first
        updateLanguageUI(langCode);
        $('#languageSelector').removeClass('active');
        
        // Save to user profile if logged in (async, don't wait for it)
        @auth
        $.ajax({
            url: '{{ route("account.update") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                langauge: langCode,
                id: {{ current_user()->id ?? 0 }},
                first_name: '{{ current_user()->first_name ?? "" }}',
                last_name: '{{ current_user()->last_name ?? "" }}',
                email: '{{ current_user()->email ?? "" }}',
                preferences: ''
            },
            success: function() {
                console.log('Language preference saved');
            }
        });
        @endauth
        
        // Call translation function - it will handle page reload for English
        doGTranslate(langCode);
        
        return false;
    };
});
</script>
