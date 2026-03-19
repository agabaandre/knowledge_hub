 <div id="google_translate_element" style="display:none;"></div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/js/bootstrap-select.min.js" type="text/javascript"></script>
    @php
    $langauge = isset(current_user()->langauge) ? current_user()->langauge : 'en';

    //dd($langauge);

    @endphp
    <script type="text/javascript">
    function googleTranslateElementInit() {
      if (window.__khubGoogleWidgetInitialized) return;
      if (!window.google || !google.translate || !google.translate.TranslateElement) return;
      new google.translate.TranslateElement({
        pageLanguage: 'en',
        autoDisplay: false,
        disableAutoHover: true,
        showBanner: false
      }, 'google_translate_element');
      window.__khubGoogleWidgetInitialized = true;
    }
    window.googleTranslateElementInit = googleTranslateElementInit;

    // If callback fired before this template script loaded, initialize now.
    if (window.__khubGoogleTranslateCallbackHit) {
      googleTranslateElementInit();
    }

    // Fallback for localhost timing/network hiccups: request script if widget is still unavailable.
    function ensureGoogleTranslateReady() {
      if (window.__khubGoogleWidgetInitialized) return;
      if (window.google && google.translate && google.translate.TranslateElement) {
        googleTranslateElementInit();
        return;
      }
      if (window.__khubGoogleScriptRequested) return;
      window.__khubGoogleScriptRequested = true;
      var s = document.createElement('script');
      s.type = 'text/javascript';
      s.src = 'https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
      s.async = true;
      s.onerror = function() { window.__khubGoogleScriptRequested = false; };
      (document.head || document.documentElement).appendChild(s);
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', ensureGoogleTranslateReady);
    } else {
      ensureGoogleTranslateReady();
    }
    setTimeout(ensureGoogleTranslateReady, 1200);
  </script>


  
  <script type="text/javascript">
     // Define helper function first
     function GTranslateGetCurrentLang() { 
       var keyValue = document['cookie'].match('(^|;) ?googtrans=([^;]*)(;|$)'); 
       return keyValue ? keyValue[2].split('/')[2] : null; 
     }
     
     $(document).ready(function () {
        var cookieLang = GTranslateGetCurrentLang();
        var userLang = '{{$langauge}}';
        
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
      var lang = lang_code || 'en'; // translate to provided language
      
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