@php
    $theme = site_theme();
@endphp

@include('layouts.' . $theme . 'partials.styles')

@include('layouts.' . $theme . 'partials.header')

@if (!@$is_home && !@$hide_search)
    @include('home.partials.' . $theme . 'page_search')
@endif

@include('partials.secondary_navigation')

@yield('styles')

@include('layouts.' . $theme . 'partials.alerts')

@if ($theme === 'theme1.')
<div id="content" class="content front-bg khub-gt-content"><div class="content__boxed"><div class="content__wrap">
@endif
@yield('content')
@if ($theme === 'theme1.')
</div></div></div>
@endif
@yield('scripts')

@auth
{{-- Global favourite button (Add favorite / Favorite with heart) for search, publication show, and theme1 --}}
<script>
(function() {
    if (window.handlePubFavourite) return;
    var addUrl = {!! json_encode(url('publications/add_favourite')) !!};
    var removeUrl = {!! json_encode(url('publications/remove_favourite')) !!};
    var csrf = {!! json_encode(csrf_token()) !!};
    window.handlePubFavourite = function(btn) {
        if (!btn || !btn.classList || !btn.classList.contains('js-favourite-pub-btn')) return;
        var id = btn.getAttribute('data-publication-id');
        var isFavourited = btn.getAttribute('data-favourited') === '1';
        var icon = btn.querySelector('i');
        var label = btn.querySelector('.js-fav-label');
        if (!id || !icon) return;
        btn.disabled = true;
        var url = isFavourited ? removeUrl : addUrl;
        var formData = new FormData();
        formData.append('_token', csrf);
        formData.append('id', id);
        fetch(url, { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
            .then(function(r) { return r.json().then(function(j) { return { ok: r.ok, json: j }; }).catch(function() { return { ok: r.ok, json: null }; }); })
            .then(function(result) {
                if (!result.ok && result.json && result.json.error) {
                    alert(result.json.error);
                    return;
                }
                if (result.json && result.json.success) {
                    var favourited = result.json.favourited === true;
                    btn.setAttribute('data-favourited', favourited ? '1' : '0');
                    if (favourited) {
                        icon.classList.remove('fa-heart-o');
                        icon.classList.add('fa-heart');
                        icon.style.color = '#ef4444';
                        if (label) label.textContent = 'Favorite';
                    } else {
                        icon.classList.remove('fa-heart');
                        icon.classList.add('fa-heart-o');
                        icon.style.color = 'inherit';
                        if (label) label.textContent = 'Favorite';
                    }
                }
            })
            .catch(function() { alert('An error occurred. Please try again.'); })
            .finally(function() { btn.disabled = false; });
    };
    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.js-favourite-pub-btn');
        if (btn && window.handlePubFavourite) {
            e.preventDefault();
            e.stopPropagation();
            window.handlePubFavourite(btn);
        }
    }, true);
})();
</script>
@endauth

<x-notify::notify />
@notifyJs

@include('layouts.' . $theme . 'partials.footer')
