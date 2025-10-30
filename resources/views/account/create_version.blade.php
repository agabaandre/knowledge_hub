@extends('layouts.plain')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/tabs.css') }}">
    @include('account.partials.wizard_res')
@endsection

@section('content')
<div class="row">
    <div class="card col-lg-12">
        <div class="card-header text-left">
            <h4 class="card-title float-left">Submit a New Version</h4>
            <div class="text-muted small">Base Resource: <strong>{!! $publication->title !!}</strong> by {{ $publication->author->name }}</div>
        </div>

        <div class="row mt-2">
            <div class="col-lg-12">
                @include('layouts.partials.alerts')
            </div>
        </div>

        <div class="card-body text-left py-4">
            <div class="container">
                <form method="POST" action="{{ route('account.publication') }}" id="publications" enctype="multipart/form-data" class="publications">
                    @csrf
                    <input type="hidden" name="original_id" value="{{ $publication->id }}">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Version Number/Name</label>
                            @php $nextVersion = number_format(($publication->versioning->count() ?: 0) + 1,1); @endphp
                            <input type="text" class="form-control" name="version" value="{{ old('version',$nextVersion) }}" required>
                        </div>
                        <div class="col-md-8 d-flex align-items-end">
                            <div class="text-muted">All other details are pre-filled from the base resource; update only what changed.</div>
                        </div>
                    </div>

                    @php($row = $publication)
                    @include('account.wizard')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @include('common.select2')
    @include('account.partials.create_js')
    @include('account.partials.wizard_js')
@endsection
