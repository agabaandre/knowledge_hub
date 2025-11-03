<div id="google_translate_element" style="display: none;"></div>
    <select class="form-control select2" onchange="translateLanguage(this.value);" style="border:#FFF;" name="langauge">
        <option value="en" {{ ('en' == $user->langauge) ? 'selected' : '' }}>English</option>
        <option value="ar" {{ ('ar' == $user->langauge) ? 'selected' : '' }}>Arabic</option>
        <option value="fr" {{ ('fr' == $user->langauge) ? 'selected' : '' }}>French</option>
        <option  value="pt" {{ ('pt' == $user->langauge) ? 'selected' : '' }}>Portuguese
        </option>
        <option value="es" {{ ('es' == $user->langauge) ? 'selected' : '' }}>Spanish</option>
        <option value="sw" {{ ('sw' == $user->langauge) ? 'selected' : '' }}>Swahili</option>
</select>

<script>
// translateLanguage function for account profile page language selector
window.translateLanguage = function(langCode) {
    // Use the global changeLanguage function if available (from langselect.blade.php)
    if (typeof window.changeLanguage === 'function') {
        window.changeLanguage(langCode);
        return;
    }
    
    // Fallback: Save language preference directly
    @auth
    if (typeof jQuery !== 'undefined' && jQuery && jQuery.ajax) {
        // Get current user preferences
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
                langauge: langCode,
                id: {{ current_user()->id ?? 0 }},
                first_name: '{{ addslashes(current_user()->first_name ?? "") }}',
                last_name: '{{ addslashes(current_user()->last_name ?? "") }}',
                email: '{{ addslashes(current_user()->email ?? "") }}',
                preferences: {{ json_encode($userPreferences) }}
            },
            success: function(response) {
                console.log('Language preference saved successfully');
                
                // Update the select dropdown to reflect the saved language
                var selectElement = document.querySelector('select[name="langauge"]');
                if (selectElement) {
                    selectElement.value = langCode;
                    // Trigger change event to update Select2 if it's being used
                    if (typeof jQuery !== 'undefined' && jQuery && selectElement.classList.contains('select2')) {
                        jQuery(selectElement).val(langCode).trigger('change');
                    }
                }
                
                // Reload page after a short delay to ensure database is updated and UI reflects the change
                // Only reload for non-English languages (English doesn't need reload per user request)
                if (langCode !== 'en') {
                    setTimeout(function() {
                        window.location.reload();
                    }, 500);
                }
                
                // Also trigger translation if doGTranslate is available
                if (typeof doGTranslate === 'function') {
                    // Handle English separately (no reload)
                    if (langCode === 'en') {
                        // Clear translation cookie
                        var date = new Date();
                        date.setTime(date.getTime() - 1);
                        document.cookie = "googtrans=; expires=" + date.toUTCString() + "; path=/";
                        
                        // Remove translation classes
                        document.body.classList.remove('translated-rtl');
                        document.documentElement.classList.remove('translated-rtl');
                        
                        // Remove Google Translate stylesheets
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
                        
                        // Try to revert translation
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
                        // For non-English, trigger translation
                        doGTranslate(langCode);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Failed to save language preference:', error);
                // Still try to translate even if save fails
                if (typeof doGTranslate === 'function' && langCode !== 'en') {
                    doGTranslate(langCode);
                }
            }
        });
    }
    @endauth
};
</script>