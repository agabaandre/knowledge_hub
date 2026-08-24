@include('layouts.partials.footer_partners')
<footer class="footer pt-5 pb-4 bg-body-secondary">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 mb-4 mb-lg-0">
                <h5 class="fw-bold mb-3">{{ settings()->site_name ?? 'Knowledge Hub' }}</h5>
                @php $footerLogoPx = (int)(settings()->logo_scale ?? 80); $footerLogoPx = in_array($footerLogoPx, [40,50,60,70,80,100,120]) ? $footerLogoPx : 80; @endphp
                @if(settings()->logo ?? null)
                    <img src="{{ settings()->logo }}" class="mb-2 {{ (settings()->footer_logo_inverse ?? false) ? 'logo-inverse' : '' }}" alt="Logo" style="max-height: {{ $footerLogoPx }}px;">
                @endif
                @if(settings()->address ?? null)
                    <p class="text-muted small mb-1">
                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode(settings()->address) }}" target="_blank" class="text-reset">{!! settings()->address !!}</a>
                    </p>
                @endif
                @if(settings()->phone ?? null)
                    <p class="text-muted small mb-0"><a href="tel:{{ settings()->phone }}" class="text-reset">{{ settings()->phone }}</a></p>
                @endif
                @if(settings()->email ?? null)
                    <p class="text-muted small"><a href="mailto:{{ settings()->email }}" class="text-reset">{{ settings()->email }}</a></p>
                @endif
            </div>
            <div class="col-lg-2 col-md-4">
                <h6 class="h6 fw-bold mb-3">Navigate</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="{{ url('/') }}" class="link-secondary text-decoration-none">Home</a></li>
                    @guest
                        <li class="mb-2"><a href="{{ url('login') }}" class="link-secondary text-decoration-none">Login</a></li>
                    @else
                        <li class="mb-2"><a href="{{ url('account/publish') }}" class="link-secondary text-decoration-none">Publish resource</a></li>
                    @endguest
                    <li class="mb-2"><a href="{{ url('forums') }}" class="link-secondary text-decoration-none">Forums</a></li>
                    <li class="mb-2"><a href="{{ url('courses') }}" class="link-secondary text-decoration-none">Courses</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-4">
                <h6 class="h6 fw-bold mb-3">About</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="{{ url('faqs') }}" class="link-secondary text-decoration-none">FAQs</a></li>
                    <li class="mb-2"><a href="https://africacdc.org" target="_blank" class="link-secondary text-decoration-none">Africa CDC</a></li>
                    <li class="mb-2"><a href="{{ url('privacy') }}" class="link-secondary text-decoration-none">Privacy Policy</a></li>
                    <li class="mb-2"><a href="{{ url('user_manual') }}" class="link-secondary text-decoration-none">User Guide</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-4">
                <h6 class="h6 fw-bold mb-3">Find us</h6>
                <div class="d-flex gap-2">
                    @if(settings()->facebook ?? null)<a href="{{ settings()->facebook }}" target="_blank" class="btn btn-icon btn-sm btn-light" title="Facebook"><i class="fa-brands fa-facebook"></i></a>@endif
                    @if(settings()->twitter ?? null)<a href="{{ settings()->twitter }}" target="_blank" class="btn btn-icon btn-sm btn-light" title="Twitter"><i class="fa-brands fa-x-twitter"></i></a>@endif
                    @if(settings()->linkedin ?? null)<a href="{{ settings()->linkedin }}" target="_blank" class="btn btn-icon btn-sm btn-light" title="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>@endif
                </div>
                @php $footerPopularTags = $popular_tags ?? collect(); @endphp
                @if((settings()->show_tags ?? false) && $footerPopularTags->count() > 0)
                    <div class="mt-3">
                        <h6 class="h6 fw-bold mb-2">Tags</h6>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($footerPopularTags->take(8) as $tag)
                                <a href="{{ tag_records_url($tag) }}" class="badge bg-primary text-decoration-none">{{ truncate($tag->tag_text, 12) }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <hr class="my-4">
        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                <p class="small text-muted mb-0">© {{ date('Y') }} {{ settings()->site_name ?? 'Knowledge Hub' }}. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <a href="{{ url('privacy') }}" class="small link-secondary text-decoration-none">Privacy</a>
                <span class="mx-1">·</span>
                <a href="{{ url('user_manual') }}" class="small link-secondary text-decoration-none">User Guide</a>
            </div>
        </div>
    </div>
</footer>

<button id="scrollToTop" class="scroll-btn" tabindex="0" type="button" aria-label="Scroll to top"></button>

@if (!get_cookie('is_returning'))
    <div class="cookie-consent position-fixed bottom-0 start-0 end-0 p-3 bg-dark text-white small text-center" style="z-index: 1050;">
        This site uses cookies. <a href="{{ url('privacy_policy/read') }}" class="text-white text-decoration-underline">Privacy policy</a>
        <div class="mt-2">
            <button class="btn btn-sm btn-light me-1 allow-button" allow="1">Allow</button>
            <button class="btn btn-sm btn-outline-light allow-button" allow="0">Decline</button>
        </div>
    </div>
@endif

<script src="{{ asset('theme1/assets/vendors/popperjs/popper.min.js') }}"></script>
<script src="{{ asset('theme1/assets/vendors/bootstrap/bootstrap.min.js') }}"></script>
<script src="{{ asset('theme1/assets/js/nifty.js') }}"></script>
<script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
<script>
(function() {
    function initSelect2() {
        if (typeof $ === 'undefined' || !$.fn.select2) return;
        $('.select2').not('.select2-hidden-accessible').select2({ width: '100%', dir: 'ltr', minimumResultsForSearch: 0, placeholder: function() { return $(this).data('placeholder') || 'Select an option'; } });
        $('.select2Modal').not('.select2-hidden-accessible').select2({ width: '100%', dir: 'ltr', dropdownParent: $('.modalselect'), minimumResultsForSearch: 0 });
        $('select.js-example-basic-single').each(function() { if (!$(this).hasClass('select2-hidden-accessible')) { $(this).addClass('select2').select2({ width: '100%', dir: 'ltr', minimumResultsForSearch: 0, placeholder: function() { return $(this).data('placeholder') || 'Select an option'; } }); } });
        $('select.form-control').each(function() { if (!$(this).hasClass('select2-hidden-accessible') && !$(this).hasClass('no-select2')) { $(this).addClass('select2').select2({ width: '100%', dir: 'ltr', minimumResultsForSearch: 0, placeholder: function() { return $(this).data('placeholder') || 'Select an option'; } }); } });
        $('.services').not('.select2-hidden-accessible').select2({ width: '100%', dropdownParent: $('.modalselect'), minimumInputLength: 0, minimumResultsForSearch: 0 });
        $('.trainings').not('.select2-hidden-accessible').select2({ width: '100%', dropdownParent: $('.modalselect'), minimumResultsForSearch: 0 });
        $('.districts').not('.select2-hidden-accessible').select2({ width: '100%', dropdownParent: $('.modalselect'), minimumResultsForSearch: 0 });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initSelect2); else initSelect2();
})();
</script>
{{-- Bootstrap 5 compatibility: data-toggle/data-target (BS4) and jQuery .modal() for theme1 front --}}
<script>
(function() {
    function init() {
        var body = document.body;
        if (!body) return;
        body.addEventListener('click', function(e) {
            var el = e.target.closest('[data-toggle="modal"]');
            if (!el) return;
            e.preventDefault();
            var id = el.getAttribute('data-target') || (el.getAttribute('href') || '').replace(/^[^#]*#/, '#');
            if (!id || id === '#') return;
            var modalEl = document.querySelector(id);
            if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            }
        }, true);
        document.querySelectorAll('[data-toggle="collapse"]').forEach(function(el) {
            if (!el.hasAttribute('data-bs-toggle')) el.setAttribute('data-bs-toggle', 'collapse');
            var t = el.getAttribute('data-target');
            if (t && !el.hasAttribute('data-bs-target')) el.setAttribute('data-bs-target', t);
        });
        document.querySelectorAll('[data-dismiss="modal"]').forEach(function(btn) {
            btn.setAttribute('data-bs-dismiss', 'modal');
            btn.setAttribute('aria-label', 'Close');
        });
        if (typeof $ !== 'undefined' && typeof bootstrap !== 'undefined' && bootstrap.Modal && !$.fn._modalBs5) {
            $.fn._modalBs5 = true;
            var _ = $.fn.modal;
            $.fn.modal = function(action) {
                return this.each(function() {
                    if (!this.classList || !this.classList.contains('modal')) return;
                    var m = bootstrap.Modal.getOrCreateInstance(this);
                    if (action === 'show') m.show();
                    else if (action === 'hide') m.hide();
                    else if (action === 'toggle') m.toggle();
                });
            };
        }
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();
</script>
@include('layouts.partials.language')
<script>
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) { new bootstrap.Tooltip(el); });
    document.querySelectorAll('.allow-button').forEach(function(btn) {
        btn.addEventListener('click', function() {
            if (this.getAttribute('allow') === '1') {
                var d = new Date(); d.setTime(d.getTime() + 90*24*60*60*1000);
                document.cookie = 'is_returning=yes; expires=' + d.toUTCString() + '; path=/';
            }
            this.closest('.cookie-consent')?.remove();
        });
    });
    if (typeof $ !== 'undefined' && $.fn.autocomplete) {
        $('.autocomplete').autocomplete({
            source: "{{ url('records/autocomplete') }}",
            minLength: 5,
            select: function(e, ui) { $('.term').val(ui.item.label); $('.search-form').submit(); }
        });
    }
    setTimeout(function() { document.querySelectorAll('.alert').forEach(function(a) { a.style.display = 'none'; }); }, 10000);
</script>
<script src="{{ asset('js/khub-content-preloader.js') }}?v={{ @filemtime(public_path('js/khub-content-preloader.js')) }}"></script>
</div>
</body>
</html>
