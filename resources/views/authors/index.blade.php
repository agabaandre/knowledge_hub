@extends('layouts.plain')

@section('styles')

@endsection

@section('content')

<div class="py-3 gray">
<div class="container">
<div class="row justify-content-center py-3">
	<h3>Contributors</h3>
</div>
<div class="row align-items-center">
					
@foreach($authors as $author)

	<div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-6" data-aos="flip-left">
		<div class="cats-wrap text-center">
			<a href="{{ url('authors/publications')}}?author={{$author->id}}" class="cats-box d-block rounded bg-white px-2 py-4">
				<div class="text-center mb-2 mx-auto position-relative d-inline-flex align-items-center justify-content-center p-3 theme-bg-light circle">
					<i class="{{ $author->icon ?? 'fa fa-user' }} fs-lg theme-cl"></i>
				</div>
				<div class="cats-box-caption">
					<h4 class="fs-md mb-0 ft-medium m-catrio">
						@if(!empty($author->orcid))
						    <a href="https://orcid.org/{{ $author->orcid }}" target="_blank" rel="noopener noreferrer" title="View {{ truncate($author->name,30) }}'s ORCID profile" style="color: inherit; text-decoration: none;">
						        {{ truncate($author->name,30) }}
						        <i class="fa fa-external-link-alt" style="font-size: 0.7em; margin-left: 3px;"></i>
						    </a>
						@else
						    {{ truncate($author->name,30) }}
						@endif
					</h4>
					<span class="text-muted">{{count($author->publications)}} Resources</span>
				</div>
			</a>
		</div>
	</div>

@endforeach
</div>
</div>
</div>

@endsection