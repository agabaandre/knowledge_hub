@extends('layouts.app')

@section('styles')

@endsection

@section('content')

<div class="gray py-4">
<div class="container">
	<div class="row">

     <div class="col-lg-8">
     	<div class="row">
     		<h4 class="ml-3">Search Results</h4>
     	</div>

		 @include('partials.quiz.quiz')
		
		 <div class="row text-success ml-2 mb-2">
			  {{ (isset($_GET['tag']))?" Tag: ".$_GET['tag']:"" }}
		</div> 

	     @include('publications.partials.publications')

	  </div>

	<div class="col-lg-4">
	@include('home.partials.tags')
    @include('publications.partials.facts')
	<br/>
    </div>
</div>
</div>

@endsection

@section('scripts')

@include('common.select2')

@endsection