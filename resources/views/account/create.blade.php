@extends('layouts.plain')

@section('styles')

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/tabs.css') }}">
    @include('account.partials.wizard_res')
    
    <!-- Lobibox Notifications CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lobibox@1.2.7/dist/css/lobibox.min.css" />

@endsection

@section('content')

    <div class="row">


        <div class="card col-lg-12">
            <div class="card-header text-left">
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
    @include('account.partials.create_js')
    @include('account.partials.wizard_js')
    
    <!-- Lobibox Notifications JS -->
    <script src="https://cdn.jsdelivr.net/npm/lobibox@1.2.7/dist/js/lobibox.min.js"></script>

@endsection
