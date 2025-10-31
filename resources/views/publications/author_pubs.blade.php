@extends('layouts.app')

@section('styles')

@endsection

@section('content')
<div class="gray py-4">
<div class="container">
	<div class="row">

     <div class="col-lg-8">
     	<div class="row">
		 <h4>Source: 
		 @if(!empty($author->orcid))
		     <a href="https://orcid.org/{{ $author->orcid }}" target="_blank" rel="noopener noreferrer" title="View {{ $author->name }}'s ORCID profile">
		         {{ $author->name }}
		         <i class="fa fa-external-link-alt" style="font-size: 0.8em; margin-left: 5px;"></i>
		     </a>
		 @else
		     {{ $author->name }}
		 @endif
		 </h4>
     	</div>

	    @include('publications.partials.publications')
       
	</div>
	<div class="col-lg-4">
    @include('publications.partials.facts')
    </div>
</div>
</div>
</div>
@endsection