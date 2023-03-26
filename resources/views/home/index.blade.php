@extends('layouts.app')

@section('styles')

<link href="{{ asset('frontend/css/home.css') }}" rel="stylesheet"/>

@endsection

@section('content')

    @include('home.partials.spotlight')
   {{-- @include('home.partials.banner') --}}
    @include('home.partials.top_categories')
    @include('home.partials.featured')
    @include('home.partials.top_searches')
    @include('home.partials.top_authors')
    @include('home.partials.subscription')

@endsection