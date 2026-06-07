 <div id="google_translate_element" style="display:none;"></div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/js/bootstrap-select.min.js" type="text/javascript"></script>
    @php
    use App\Models\SiteLanguage;
    $langauge = app()->getLocale();
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
    function googleTranslateElementInit() {
      new google.translate.TranslateElement({
        pageLanguage: 'en',
        autoDisplay: false,
        disableAutoHover: true,
        showBanner: false
      },
        'google_translate_element');

    }
  </script>


  
  <script type="text/javascript">
     // Define helper function first
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

     $(document).ready(function () {
        var cookieLang = GTranslateGetCurrentLang();
        var userLang = '{{ $langauge }}';
        var googleLang = khubGoogleCodeForLocale(userLang);

        if (userLang && userLang !== 'en') {
          if (cookieLang !== googleLang) {
            var date = new Date();
            date.setTime(date.getTime() + (365 * 24 * 60 * 60 * 1000));
            document.cookie = "googtrans=/auto/" + googleLang + "; expires=" + date.toUTCString() + "; path={{ $localeCookiePath }}";
          }
          doGTranslate(googleLang);
        } else if (!cookieLang || cookieLang === 'en') {
          var clearDate = new Date();
          clearDate.setTime(clearDate.getTime() - 1);
          document.cookie = "googtrans=; expires=" + clearDate.toUTCString() + "; path={{ $localeCookiePath }}";
        } else if (cookieLang && cookieLang !== 'en') {
          doGTranslate(cookieLang);
        }
      });
    function GTranslateFireEvent(element, event) { try { if (document.createEventObject) { var evt = document.createEventObject(); element.fireEvent('on' + event, evt) } else { var evt = document.createEvent('HTMLEvents'); evt.initEvent(event, true, true); element.dispatchEvent(evt) } } catch (e) { } }

    function doGTranslate(lang_code) {
      var lang = lang_code || 'en'; // translate to provided language
      
      // Special handling for English - remove translation and reload
      if (lang === 'en') {
        // Clear the translation cookie
        var date = new Date();
        date.setTime(date.getTime() - 1); // Expire immediately
        document.cookie = "googtrans=; expires=" + date.toUTCString() + "; path={{ $localeCookiePath }}";
        
        // Remove all Google Translate classes and restore original content
        $('body').removeClass('translated-rtl');
        $('html').removeClass('translated-rtl');
        $('head').find('link[href*="translate.googleapis.com"]').remove();
        
        // Remove inline styles added by Google Translate
        $('[style*="direction"]').css('direction', '');
        
        // Try to restore original page content by triggering revert
        var teCombo = document.querySelector('select.goog-te-combo:not(.menu-language-menu-container select)');
        if (teCombo) {
          // Find the original language option (English should be the first or default)
          var enIndex = Array.from(teCombo.options).findIndex(option => {
            return option.value === 'en' || option.value === '';
          });
          if (enIndex !== -1) {
            teCombo.selectedIndex = enIndex;
            khubFireComboChange(teCombo);
          }
          
          // Force page reload to ensure clean state
          setTimeout(function() {
            window.location.reload();
          }, 300);
        } else {
          // If widget not ready, just reload
          window.location.reload();
        }
        
        // Save English preference
        date = new Date();
        date.setTime(date.getTime() + (365 * 24 * 60 * 60 * 1000)); // 1 year
        document.cookie = "googtrans=; expires=" + date.toUTCString() + "; path={{ $localeCookiePath }}";
        return;
      }

      var teCombo = document.querySelector('select.goog-te-combo:not(.menu-language-menu-container select)');

      if (!teCombo || !teCombo.innerHTML) {
        setTimeout(function () { doGTranslate(lang_code) }, 500);
        return;
      }

      var langIndex = Array.from(teCombo.options).findIndex(option => option.value === lang);

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

      // Save language preference to cookie with longer expiration
      var date = new Date();
      date.setTime(date.getTime() + (365 * 24 * 60 * 60 * 1000)); // 1 year
      document.cookie = "googtrans=/auto/" + lang + "; expires=" + date.toUTCString() + "; path={{ $localeCookiePath }}";
    }
    $(function () {
      $('.selectpicker').selectpicker();
    });


   

    </script>