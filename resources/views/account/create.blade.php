@extends('layouts.plain')

@section('styles')

@endsection

@section('content')


<div class="row">

 
    @include('layouts.partials.alerts')

    <div class="card col-lg-12">
        <div class="card-header text-left">
            <h4 class="card-title float-left">Publish a resource</h4>
        </div>

        <div class="card-body text-left">
           
           <form method="POST" action="{{ route('account.publication') }}" id='publications' class='publications'>
            @csrf
           
             @include('account.partials.publication_form')

           </form>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

@endsection

@section('scripts')
    @include('common.select2')
    @include('account.partials.create_js')
@endsection
