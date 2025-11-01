@extends('admin.layouts.main')

@section('content')
<div class="page-header">
    <h1 class="page-title">Event Preview</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.events.index') }}">Events</a></li>
            <li class="breadcrumb-item active">Preview</li>
        </ol>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h3>{{ $event->title }}</h3>
        <p class="text-muted">{{ $event->venue }} @if($event->is_online) • Online @endif</p>
        <div class="event-description">
            {!! $event->description !!}
        </div>
        <div class="mt-3">
            <strong>Start Date:</strong> {{ $event->startdate ? \Carbon\Carbon::parse($event->startdate)->format('M d, Y') : '-' }}<br>
            <strong>End Date:</strong> {{ $event->enddate ? \Carbon\Carbon::parse($event->enddate)->format('M d, Y') : '-' }}<br>
            <strong>Organized By:</strong> {{ $event->organized_by ?? '-' }}<br>
            <strong>Contact Person:</strong> {{ $event->contact_person ?? '-' }}<br>
            <strong>Status:</strong> <span class="badge {{ $event->status==='active' ? 'badge-success' : 'badge-secondary' }}">{{ ucfirst($event->status ?? 'pending') }}</span>
        </div>
        <div class="mt-4">
            <a href="{{ route('admin.events.edit', $event->id) }}" class="btn btn-primary">Edit</a>
            <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .event-description {
        line-height: 1.6;
        color: #333;
    }
    .event-description p {
        margin-bottom: 1rem;
    }
    .event-description ul, .event-description ol {
        margin-bottom: 1rem;
        padding-left: 2rem;
    }
    .event-description img {
        max-width: 100%;
        height: auto;
    }
</style>
@endsection


