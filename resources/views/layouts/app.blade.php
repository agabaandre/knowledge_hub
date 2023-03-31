
@include('layouts.partials.styles')

<div id="main-wrapper">

@include('layouts.partials.header')

@if(!@$is_home)
    @include('home.partials.page_search')
@endif

@yield('styles')

@if(Session::has('alert'))

<div class="alert alert-{{ ($alert_class)?$alert_class:'success' }}" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-hidden="true">×</button>
        {!! Session::get('alert') !!}</a>
</div>
@endif


@yield('content')
@yield('scripts')

@include('layouts.partials.footer')


	