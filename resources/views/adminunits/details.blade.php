
@extends('layouts.plain')

@section('styles')
<style>
@include('partials.publications.publication_feed_card_styles')
@include('publications.partials.preview_modal_styles')
</style>
@endsection
@section('content')      	
<!-- ======================= Countries ======================== -->
<section class="space gray">
    
    <div class="container">

    @if(count($publications) == 0 && count($child_units) == 0)
    @php
        $iconClass = trim((string) ($unit->icon ?: 'fa-building'));
        if ($iconClass !== '' && ! str_starts_with($iconClass, 'fa')) {
            $iconClass = 'fa '.$iconClass;
        } elseif (! str_contains($iconClass, ' ')) {
            $iconClass = 'fa '.$iconClass;
        }
    @endphp
    @include('adminunits.admin_units', ['adminunits' => collect(), 'hideEmpty' => true])
    <div class="row justify-content-center">
        <div class="col-xl-5 col-lg-6 col-md-8" data-aos="fade-up">
            <div class="adminunit-card adminunit-card--static text-center">
                <span class="adminunit-card__media mx-auto">
                    <span class="adminunit-card__icon" aria-hidden="true">
                        <i class="{{ $iconClass }}"></i>
                    </span>
                    @if(! empty($unit->logo))
                        <img class="adminunit-card__logo" src="{{ storage_link('uploads/adminunits/'.$unit->logo) }}" alt="">
                    @endif
                </span>
                <h3 class="adminunit-card__name">{{ $unit->name }}</h3>
                @if(! empty($unit->description))
                    <p class="adminunit-card__desc">{{ strip_tags((string) $unit->description) }}</p>
                @endif
                <p class="text-muted mb-0">No publications from this unit yet.</p>
            </div>
        </div>
    </div>

    @else
       
            @if(count($child_units)>0)

                <div class="row justify-content-center" data-aos="slide-down">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                        <div class="sec_title position-relative text-center mb-5 mt-3">
                            <h2 class="ft-bold">Units Under {{$unit->name}}</h2>
                        </div>
                    </div>
                </div>

                @include('adminunits.admin_units',['adminunits'=>$child_units])

            @endif

            
            @if(count($publications)>0)
            
                <div class="row justify-content-center" data-aos="slide-down">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                        <div class="sec_title position-relative text-center mb-5 mt-3">
                            <h2 class="ft-bold">Published Resources</h2>
                        </div>
                    </div>
                </div>
            
                @include('publications.partials.publications')

            @else
                <div class="row justify-content-center mb-2">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-2">
                        <div class="sec_title position-relative text-center mb-5 mt-5">
                            <h4 class="ft-bold ft-md text-warning">No resources have been published by  {{$unit->name}} yet</h4>
                        </div>
                    </div>
                </div>
            @endif
    @endif


        
    </div>
</section>
@auth
    @include('common.pdf-chat-modal')
@endauth
<!-- ======================= Countries ======================== -->
@endsection
@section('scripts')
@auth
    @include('common.pdf-chat-js')
@endauth
@include('common.attachment_js')
@include('publications.partials.preview_modal')
@include('partials.publications.publication_feed_card_scripts')
@endsection
