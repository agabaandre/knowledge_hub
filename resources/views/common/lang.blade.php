<div id="google_translate_element" style="display: none;"></div>
    <select class="form-control select2" onchange="translateLanguage();" style="border:#FFF;" name="langauge">
        @foreach(\App\Models\SiteLanguage::selectorMap() as $code => $row)
            <option value="{{ $code }}"
                    data-google-code="{{ $row['google_code'] ?? $code }}"
                    {{ ($code == ($user->langauge ?? '')) ? 'selected' : '' }}>{{ $row['name'] }}</option>
        @endforeach
    </select>

<script>
// translateLanguage function for account profile page language selector
window.translateLanguage = function() {
    var sel = document.querySelector('select[name="langauge"]');
    if (!sel) return;
    var localeCode = sel.value;
    var opt = sel.options[sel.selectedIndex];
    var googleCode = (opt && opt.getAttribute('data-google-code')) ? opt.getAttribute('data-google-code') : localeCode;

    if (typeof window.changeLanguage === 'function') {
        window.changeLanguage(localeCode, googleCode);
        return;
    }

    // Fallback: Save language preference directly
    @auth
    if (typeof jQuery !== 'undefined' && jQuery && jQuery.ajax) {
        @php
            $userPreferences = [];
            if (auth()->check() && current_user()) {
                $userPreferences = current_user()->preferences()->pluck('subtheme_id')->toArray();
            }
        @endphp

        jQuery.ajax({
            url: '{{ route("account.update") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                langauge: localeCode,
                id: {{ current_user()->id ?? 0 }},
                first_name: '{{ addslashes(current_user()->first_name ?? "") }}',
                last_name: '{{ addslashes(current_user()->last_name ?? "") }}',
                email: '{{ addslashes(current_user()->email ?? "") }}',
                preferences: {{ json_encode($userPreferences) }}
            },
            success: function(response) {
                console.log('Language preference saved successfully');

                var selectElement = document.querySelector('select[name="langauge"]');
                if (selectElement) {
                    selectElement.value = localeCode;
                    if (typeof jQuery !== 'undefined' && jQuery && selectElement.classList.contains('select2')) {
                        jQuery(selectElement).val(localeCode).trigger('change');
                    }
                }

                if (localeCode !== 'en') {
                    setTimeout(function() {
                        window.location.reload();
                    }, 500);
                }

                if (typeof doGTranslate === 'function') {
                    if (localeCode === 'en') {
                        var date = new Date();
                        date.setTime(date.getTime() - 1);
                        document.cookie = "googtrans=; expires=" + date.toUTCString() + "; path=/";

                        document.body.classList.remove('translated-rtl');
                        document.documentElement.classList.remove('translated-rtl');

                        var translateLinks = document.querySelectorAll('head link[href*="translate.googleapis.com"]');
                        translateLinks.forEach(function(link) {
                            link.remove();
                        });

                        var elementsWithDirection = document.querySelectorAll('[style*="direction"]');
                        elementsWithDirection.forEach(function(el) {
                            if (el.style.direction) {
                                el.style.direction = '';
                            }
                        });

                        var teCombo = document.querySelector('select.goog-te-combo:not(.menu-language-menu-container select)');
                        if (teCombo) {
                            var enIndex = Array.from(teCombo.options).findIndex(function(option) {
                                return option.value === 'en' || option.value === '';
                            });
                            if (enIndex !== -1) {
                                teCombo.selectedIndex = enIndex;
                                if (typeof GTranslateFireEvent === 'function') {
                                    GTranslateFireEvent(teCombo, 'change');
                                } else {
                                    var event = new Event('change', { bubbles: true });
                                    teCombo.dispatchEvent(event);
                                }
                            }
                        }
                    } else {
                        doGTranslate(googleCode);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Failed to save language preference:', error);
                if (typeof doGTranslate === 'function' && localeCode !== 'en') {
                    doGTranslate(googleCode);
                }
            }
        });
    }
    @endauth
};
</script>
