@php
    $hide_search = true;
@endphp

@extends('layouts.plain')

@section('styles')

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/tabs.css') }}">
    @include('account.partials.wizard_res')
    
    <!-- Lobibox Notifications CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lobibox@1.2.7/dist/css/lobibox.min.css" />

@endsection

@section('content')
{{-- Custom Header Section (replaces search bar) --}}
<div class="pt-5 pt-0 custom-bg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div style="text-align: center; padding: 2rem 0;">
                    <h1 style="font-size: 2rem; font-weight: 700; margin: 0 0 0.5rem 0; color: white;">
                        <i class="fa fa-plus-circle me-2"></i>Publish a Resource
                    </h1>
                    <p style="margin: 0; color: rgba(255, 255, 255, 0.95); font-size: 1rem;">
                        Share your knowledge and contribute to the Africa CDC Knowledge Hub
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Secondary Navigation Below Banner --}}
@include('partials.secondary_navigation', ['forceShow' => true])

    <div class="row">


        <div class="card col-lg-12">
            <div class="card-header text-left" style="display: none;">
                <h4 class="card-title float-left">Publish a resource</h4>
            </div>

            <div class="row mt-2">
                <div class="col-lg-12">
                    @include('layouts.partials.alerts')
                </div>
            </div>


            <div class="card-body text-left  py-5">


                <div class="container">

                    <form method="POST" action="{{ route('account.publication') }}" id="publication_form"
                          enctype="multipart/form-data" class='publications'
                          data-parsley-validate="">
                        @csrf


                        @include('account.wizard')

                    </form>

                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->


        </div>


    </div>

@endsection

@section('scripts')

    @include('common.select2')
    @include('partials.title_case_js')
    @include('account.partials.create_js')
    @include('account.partials.wizard_js')
    
    <!-- Lobibox Notifications JS -->
    <script src="https://cdn.jsdelivr.net/npm/lobibox@1.2.7/dist/js/lobibox.min.js"></script>

@endsection
