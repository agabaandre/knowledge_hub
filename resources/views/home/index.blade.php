@extends('layouts.app')

@section('styles')

<link href="{{ asset('frontend/css/home.css') }}" rel="stylesheet"/>

@endsection

@section('content')

    @include('home.partials.spotlight')
    @include('home.partials.top_categories')
    @include('home.partials.featured')
    @include('home.partials.top_searches')
    @include('home.partials.top_authors')
    @include('home.partials.subscription')

@endsection

@section('scripts')


<script>
var showing = false;

function showComments(elem){

    // Pick the div data as required
			var head = "" + $('.heading'+elem).html();
			var data = "" + $('.pbody'+elem).html();

          
			$('.pop'+elem).popover({
				html: true,
				title: head,
				content: data,
				placement:'bottom',
                manual:'hover'
			});

}

</script>

@endsection