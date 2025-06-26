@extends('layouts.app')

@section('styles')

@endsection

@section('content')
<div class="gray py-4">
<div class="container">
	
	<div class="row">

     <div class="col-lg-8">
     	<div class="row">
		 <h4>Sub thematic area: {{$subtheme->description}}</h4>
     	</div>

		@include('partials.quiz.quiz')
	    @include('publications.partials.publications')
       
	</div>
    <div class="col-lg-4">
    @include('publications.partials.facts')
    </div>
</div>
</div>
</div>
@endsection