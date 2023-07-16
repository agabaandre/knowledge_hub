@extends('layouts.plain')

@section('styles')

<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/tabs.css') }}">

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


        <div class="card-body text-left">
           
           <form method="POST" action="{{ route('account.publication') }}" id='publications' enctype="multipart/form-data" class='publications'>
            @csrf
           
             @include('account.partials.publication_form')

           </form>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->


</div>

@include('account.tabs')


@endsection

@section('scripts')

    @include('common.select2')
    @include('account.partials.create_js')
    @include('account.partials.wizard_js')
    
@endsection
