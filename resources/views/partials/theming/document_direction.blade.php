@php
    $uiLocale = app()->getLocale();
    $htmlLang = str_replace('_', '-', $uiLocale);
    $htmlDir = html_dir_for_locale($uiLocale);
    $isRtlUi = $htmlDir === 'rtl';
@endphp
