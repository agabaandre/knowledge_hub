<div class="row" id="khub-footer-i18n">

    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-4 notranslate">
        <div class="footer_widget">
            <h4 class="widget_title">{{ __('ui_body.footer_address') }}</h4>
            @php
                $footerLogoPx = (int)(settings()->logo_scale ?? 80);
                $footerLogoPx = in_array($footerLogoPx, [40,50,60,70,80,100,120]) ? $footerLogoPx : 80;
            @endphp
            @if(settings()->logo ?? null)
                <img src="{{ settings()->logo }}" class="logo img-footer mb-1 {{ (settings()->footer_logo_inverse ?? false) ? 'logo-inverse' : '' }}" alt="Logo" style="max-height:{{ $footerLogoPx }}px; width:auto;">
            @endif

            <div class="address mt-0">
                <a href="https://www.google.com/maps/search/?api=1&query={!! urlencode(settings()->address) !!}"
                    target="_blank" class="d-block">
                    {!! settings()->address !!}
                </a>
            </div>
            <div class="address mt-0">
                <a href="tel:{!! settings()->phone !!}" class="d-block">
                    {!! settings()->phone !!}
                </a>
                <a href="mailto:{!! settings()->email !!}" class="d-block">
                    {!! settings()->email !!}
                </a>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-4 notranslate">
        <div class="footer_widget">
            <h4 class="widget_title">{{ __('ui_body.footer_navigate') }}</h4>
            <ul class="footer-menu list-unstyled">
                <li><a href="{{ url('/') }}" class="text-decoration-none">{{ __('ui_body.footer_home') }}</a></li>
                @guest
                    <li><a href="{{ url('login') }}" class="text-decoration-none">{{ __('ui_body.footer_login') }}</a></li>
                @else
                    <li><a href="{{ url('account/publish') }}" class="text-decoration-none">{{ __('ui_body.publish_a_resource') }}</a></li>
                @endguest
                <li><a href="{{ url('forums') }}" class="text-decoration-none">{{ __('ui_body.footer_discussion_forums') }}</a></li>
                <li><a href="{{ url('courses') }}" class="text-decoration-none">{{ __('ui_body.footer_courses') }}</a></li>

            </ul>
        </div>
    </div>

    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-4 notranslate">
        <div class="footer_widget">
            <h4 class="widget_title">{{ __('ui_body.footer_about_portal') }}</h4>
            <ul class="footer-menu list-unstyled">
                <li><a href="{{ url('faqs') }}" class="text-decoration-none">{{ __('ui_body.footer_faq') }}
                </a></li>
                <li><a href="https://africacdc.org" target="_blank" class="text-decoration-none">{{ __('ui_body.footer_africa_cdc_website') }}</a></li>
                <li><a href="{{ url('privacy') }}" class="text-decoration-none">{{ __('ui_body.footer_privacy_policy') }}</a></li>
                <li><a href="{{ url('user_manual') }}" class="text-decoration-none">{{ __('ui_body.footer_user_guide') }}</a></li>
            </ul>
        </div>
    </div>

    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-4 notranslate">
        <div class="footer_widget">
            <h4 class="widget_title">{{ __('ui_body.footer_find_us') }}</h4>
            <ul class="footer-menu list-inline mb-3">
                <li class="list-inline-item">
                    <a href="{{ settings()->facebook }}" target="_blank" class="text-decoration-none"
                        data-toggle="tooltip" data-placement="top" title="Facebook">
                        <i class="fa-brands fa-facebook"></i>
                    </a>
                </li>
                <li class="list-inline-item">
                    <a href="{{ settings()->twitter }}" target="_blank" class="text-decoration-none"
                        data-toggle="tooltip" data-placement="top" title="Twitter">
                        <i class="fa-brands fa-x-twitter"></i>
                    </a>
                </li>
                <li class="list-inline-item">
                    <a href="{{ settings()->linkedin }}" target="_blank" class="text-decoration-none"
                        data-toggle="tooltip" data-placement="top" title="LinkedIn">
                        <i class="fa-brands fa-linkedin-in"></i>
                    </a>
                </li>
                <li class="list-inline-item">
                    <a href="{{ settings()->academia }}" target="_blank" class="text-decoration-none"
                        data-toggle="tooltip" data-placement="top" title="Academia">
                        <i class="fa-solid fa-a"></i>
                    </a>
                </li>
                <li class="list-inline-item">
                    <a href="{{ settings()->researchgate }}" target="_blank" class="text-decoration-none"
                        data-toggle="tooltip" data-placement="top" title="ResearchGate">
                        <i class="fa-brands fa-researchgate"></i>
                    </a>
                </li>
                <li class="list-inline-item">
                    <a href="https://play.google.com/store/apps/details?id=com.africacdc.khubmobile&hl=en" target="_blank"
                        class="text-decoration-none" data-toggle="tooltip" data-placement="top" title="Get the Mobile App">
                        <i class="fa-brands fa-google-play"></i>
                    </a>
                </li>
            </ul>
            <p class="text-white small mb-3">{{ __('ui_body.footer_download_app') }}</p>

            @php $footerPopularTags = $popular_tags ?? collect(); @endphp
            @if((settings()->show_tags ?? false) && $footerPopularTags->count() > 0)
            <div class="mt-3">
                <h5 class="widget_title mb-2" style="font-size:0.95rem;">{{ __('ui_body.footer_popular_tags') }}</h5>
                <div class="footer-tags">
                    @php
                        $colors = [settings()->primary_color,settings()->primary_text_color,settings()->icon_font_color];
                    @endphp
                    @foreach($footerPopularTags->take(5) as $tag)
                    <a href="{{ tag_records_url($tag) }}"
                       class="tag-pill"
                       title="{{$tag->tag_text}}">
                        {{ truncate($tag->tag_text,15) }}
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>


</div>
