@php
    $hide_search = true;
@endphp

@extends('layouts.app')

@section('title', 'Administrator Guide')

@section('styles')
    @include('user_manual.partials.styles')
@endsection

@section('content')
<div class="pt-5 pt-0 custom-bg">
    <div class="container">
        <div class="guide-hero">
            <h1><i class="fa fa-cogs me-2"></i>Administrator Guide</h1>
            <p>Approvals, dashboards, slugs, federation, and operations</p>
        </div>
    </div>
</div>

@include('partials.secondary_navigation', ['forceShow' => true])

<div class="container">
    <div class="guide-wrap">
        <div class="guide-switch">
            <a class="btn btn-sm btn-outline-secondary" href="{{ url('user_manual') }}">User guide</a>
            <a class="btn btn-sm btn-outline-secondary" href="{{ url('faqs') }}">FAQs</a>
        </div>
        <div class="guide-card">
            {!! $bodyHtml !!}
        </div>
    </div>
</div>
@endsection
