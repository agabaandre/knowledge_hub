 <div id="google_translate_element" style="display:none;"></div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/js/bootstrap-select.min.js" type="text/javascript"></script>
    @php
$lang = !empty(current_user()->language) ? current_user()->language : 'en';
dd(current_user()->langauge);
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
     $(document).ready(function () {
        doGTranslate('{{$lang}}'); // Translate the page in the user langauge
      });
    function GTranslateGetCurrentLang() { var keyValue = document['cookie'].match('(^|;) ?googtrans=([^;]*)(;|$)'); return keyValue ? keyValue[2].split('/')[2] : null; }
    function GTranslateFireEvent(element, event) { try { if (document.createEventObject) { var evt = document.createEventObject(); element.fireEvent('on' + event, evt) } else { var evt = document.createEvent('HTMLEvents'); evt.initEvent(event, true, true); element.dispatchEvent(evt) } } catch (e) { } }

    function doGTranslate(lang_code) {
   
    
            var lang = lang_code || 'en'; // transalte to provided langauge
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
    }
    $(function () {
      $('.selectpicker').selectpicker();
    });


   

    </script>