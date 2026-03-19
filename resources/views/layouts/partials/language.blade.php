 <div id="google_translate_element" style="display:none;"></div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/js/bootstrap-select.min.js" type="text/javascript"></script>
    @php
    $langauge = isset(current_user()->langauge) ? current_user()->langauge : 'en';

    //dd($langauge);

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
    var khubLangMetaFromServer = @json(\App\Models\SiteLanguage::selectorMap());

    function normalizeSupportedGoogleCode(code, fallbackLocale) {
      var meta = khubLangMetaFromServer || {};
      var fb = fallbackLocale || 'en';
      var fbGoogle = (meta[fb] && meta[fb].google_code) ? meta[fb].google_code : fb;
      if (!code) return fbGoogle;
      var raw = String(code).trim().toLowerCase();
      if (!raw) return fbGoogle;
      for (var loc in meta) {
        if (!Object.prototype.hasOwnProperty.call(meta, loc)) continue;
        var g = String((meta[loc].google_code || loc)).toLowerCase();
        if (g === raw || String(loc).toLowerCase() === raw) {
          return (meta[loc].google_code || loc);
        }
      }
      return fbGoogle;
    }

     // Define helper function first
     function GTranslateGetCurrentLang() { 
       var keyValue = document['cookie'].match('(^|;) ?googtrans=([^;]*)(;|$)'); 
       var raw = keyValue ? keyValue[2].split('/')[2] : null;
       return normalizeSupportedGoogleCode(raw, 'en');
     }
     
     $(document).ready(function () {
        var userLang = normalizeSupportedGoogleCode('{{$langauge}}', 'en');
        var cookieLang = normalizeSupportedGoogleCode(GTranslateGetCurrentLang(), userLang || 'en');
        
        // Priority: User's saved preference > Cookie > Default (en)
        @auth
        // For logged-in users, prioritize saved preference
        if (userLang && userLang !== 'en') {
          // User has a saved non-English preference - use it
          // Sync cookie with user preference if different
          if (cookieLang !== userLang) {
            var date = new Date();
            date.setTime(date.getTime() + (365 * 24 * 60 * 60 * 1000)); // 1 year
            document.cookie = "googtrans=/auto/" + userLang + "; expires=" + date.toUTCString() + "; path=/";
          }
          doGTranslate(userLang);
        } else if (userLang === 'en' || !userLang) {
          // User prefers English or no preference - clear translation
          var date = new Date();
          date.setTime(date.getTime() - 1);
          document.cookie = "googtrans=; expires=" + date.toUTCString() + "; path=/";
          // Don't translate if user prefers English
        } else if (cookieLang && cookieLang !== 'en') {
          // Fallback: use cookie if user has no saved preference
          doGTranslate(cookieLang);
        }
        @else
        // Guest users: use cookie if available
        if (cookieLang === 'en' || !cookieLang) {
          // Clear any existing translation cookie for English
          var date = new Date();
          date.setTime(date.getTime() - 1);
          document.cookie = "googtrans=; expires=" + date.toUTCString() + "; path=/";
        } else if (cookieLang && cookieLang !== 'en') {
          // Use cookie language if available and not English
          doGTranslate(cookieLang);
        }
        @endauth
      });
    function GTranslateFireEvent(element, event) { try { if (document.createEventObject) { var evt = document.createEventObject(); element.fireEvent('on' + event, evt) } else { var evt = document.createEvent('HTMLEvents'); evt.initEvent(event, true, true); element.dispatchEvent(evt) } } catch (e) { } }

    function doGTranslate(lang_code) {
      var lang = normalizeSupportedGoogleCode(lang_code, 'en'); // translate to a supported target only
      
      // Special handling for English - remove translation and reload
      if (lang === 'en') {
        // Clear the translation cookie
        var date = new Date();
        date.setTime(date.getTime() - 1); // Expire immediately
        document.cookie = "googtrans=; expires=" + date.toUTCString() + "; path=/";
        
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
            GTranslateFireEvent(teCombo, 'change');
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
        document.cookie = "googtrans=; expires=" + date.toUTCString() + "; path=/";
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
        GTranslateFireEvent(teCombo, 'change');
        GTranslateFireEvent(teCombo, 'change');
      }

      // Force retranslation to ensure consistency
      setTimeout(function() {
        // Trigger translation again to ensure all elements are translated
        if (teCombo && teCombo.selectedIndex !== langIndex) {
          teCombo.selectedIndex = langIndex;
          GTranslateFireEvent(teCombo, 'change');
        }
        
        // Force retranslate page elements
        var pageLang = GTranslateGetCurrentLang();
        if (pageLang !== lang) {
          teCombo.selectedIndex = langIndex;
          GTranslateFireEvent(teCombo, 'change');
        }
      }, 300);

      // Save language preference to cookie with longer expiration
      var date = new Date();
      date.setTime(date.getTime() + (365 * 24 * 60 * 60 * 1000)); // 1 year
      document.cookie = "googtrans=/auto/" + lang + "; expires=" + date.toUTCString() + "; path=/";
    }
    $(function () {
      $('.selectpicker').selectpicker();
    });


   

    </script>