@php
    $hide_search = true;
@endphp

@extends('layouts.app')

@section('title', 'User Guide')

@section('styles')
    @include('user_manual.partials.styles')
@endsection

@section('content')
<div class="pt-5 pt-0 custom-bg">
    <div class="container">
        <div class="guide-hero">
            <h1><i class="fa fa-book me-2"></i>User Guide</h1>
            <p>How to search, publish, discuss, and review content on the Knowledge Hub</p>
        </div>
    </div>
</div>

@include('partials.secondary_navigation', ['forceShow' => true])

<div class="container">
    <div class="guide-wrap">
        <div class="guide-switch">
            <a class="btn btn-sm btn-outline-secondary" href="{{ url('faqs') }}">FAQs</a>
            <a class="btn btn-sm btn-outline-secondary" href="{{ url('administrator-guide') }}">Administrator guide</a>
        </div>
        <div class="guide-card">
            {!! $bodyHtml !!}
        </div>
    </div>
</div>
@endsection
