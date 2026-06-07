<div id="khub-site-branding" class="notranslate">
    <h3 style="color:black !important; font-weight:bold; margin-bottom: 7px;" data-khub-i18n="ui_body.site_title">
        {{ \App\Support\UiLocaleLabels::siteTitle() }}
    </h3>
    @if(\App\Support\UiLocaleLabels::siteTagline() !== '')
        <h6 class="slogan fw-bold" style="font-size: 14px; margin-bottom: 7px; margin-left: 0;" data-khub-i18n="ui_body.site_tagline">
            {{ \App\Support\UiLocaleLabels::siteTagline() }}
        </h6>
    @endif
</div>
