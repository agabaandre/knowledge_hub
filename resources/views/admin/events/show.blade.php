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
        <p>{!! nl2br(e($event->description)) !!}</p>
        <a href="{{ route('admin.events.edit', $event->id) }}" class="btn btn-primary">Edit</a>
        <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>
@endsection


