@extends('layouts.app')

@section('title', $pageTitle ?? 'Partner country knowledge hubs')

@section('content')
@include('partials.secondary_navigation', ['forceShow' => true])

<div class="container mt-4 mb-5">
    <div class="row mb-4">
        <div class="col-lg-8">
            <h1 class="mb-2" style="font-size:1.6rem;font-weight:700;color:#0f172a;">
                <i class="fa fa-globe-africa me-2" style="color:var(--theme-color-primary,#119A48);"></i>
                {{ $pageTitle }}
            </h1>
            <p class="text-muted mb-0">{{ $pageDescription }}</p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <a href="{{ url('records') }}" class="btn btn-outline-primary btn-sm">
                <i class="fa fa-search me-1"></i>Back to records search
            </a>
        </div>
    </div>

    @if($hubs->isEmpty())
        <div class="alert alert-info">
            No synced country hubs yet. Administrators can register hubs under
            <strong>Settings → Federated Knowledge Hubs</strong> and run <strong>Sync public data</strong>
            or wait for the nightly <code>federation:sync</code> job.
        </div>
    @else
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body">
                <form method="get" action="{{ route('federation.browse') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Country hub</label>
                        <select name="hub" class="form-control select2">
                            <option value="">All partner hubs</option>
                            @foreach($hubs as $hub)
                                <option value="{{ $hub->id }}" {{ (int) request('hub') === (int) $hub->id ? 'selected' : '' }}>
                                    {{ $hub->name }}
                                    @if($hub->mappedCountry) ({{ $hub->mappedCountry->name }}) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <input type="text" name="term" class="form-control" value="{{ $term ?? '' }}" placeholder="Title or description">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Show</label>
                        <select name="type" class="form-control">
                            <option value="publications" {{ ($type ?? 'publications') === 'publications' ? 'selected' : '' }}>Publications</option>
                            <option value="forums" {{ ($type ?? '') === 'forums' ? 'selected' : '' }}>Forums</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <a class="nav-link {{ ($type ?? 'publications') === 'publications' ? 'active' : '' }}"
                   href="{{ route('federation.browse', array_merge(request()->except('type', 'page'), ['type' => 'publications'])) }}">
                    Publications ({{ $publications->total() }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ ($type ?? '') === 'forums' ? 'active' : '' }}"
                   href="{{ route('federation.browse', array_merge(request()->except('type', 'page'), ['type' => 'forums'])) }}">
                    Forums ({{ $forums->total() }})
                </a>
            </li>
        </ul>

        @if(($type ?? 'publications') === 'publications')
            @forelse($publications as $row)
                @include('partials.federation.publication_card', ['row' => $row])
            @empty
                <div class="alert alert-light border">No synced publications found for this filter.</div>
            @endforelse
            <div class="py-3">{{ $publications->links() }}</div>
        @else
            @forelse($forums as $forum)
                @include('partials.federation.forum_card', ['forum' => $forum])
            @empty
                <div class="alert alert-light border">No synced forums found for this filter.</div>
            @endforelse
            <div class="py-3">{{ $forums->links() }}</div>
        @endif

        <div class="row mt-4">
            @foreach($hubs as $hub)
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100 border">
                        <div class="card-body">
                            <h6 class="card-title mb-1">{{ $hub->name }}</h6>
                            @if($hub->mappedCountry)
                                <small class="text-muted d-block mb-2">{{ $hub->mappedCountry->name }}</small>
                            @endif
                            <small class="text-muted d-block mb-2">
                                Last sync: {{ $hub->last_synced_at ? $hub->last_synced_at->diffForHumans() : 'Never' }}
                            </small>
                            <a href="{{ route('federation.browse', ['hub' => $hub->id]) }}" class="btn btn-sm btn-outline-primary">Browse</a>
                            <a href="{{ $hub->base_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-link">Visit hub</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
