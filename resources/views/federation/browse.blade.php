@extends('layouts.app')

@section('title', $pageTitle ?? 'Member States knowledge hubs')

@section('styles')
<style>
.fed-page {
    background: linear-gradient(135deg, #f5f7fa 0%, #eef2f6 100%);
    padding: 2rem 0 3rem;
    min-height: calc(100vh - 120px);
}
.fed-page .fed-shell {
    max-width: 1180px;
    margin: 0 auto;
}
.fed-hero {
    background: #fff;
    border-radius: 16px;
    padding: 1.75rem 2rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 12px rgba(15, 23, 42, 0.06);
    border: 1px solid rgba(15, 23, 42, 0.06);
}
.fed-hero h1 {
    font-size: 1.65rem;
    font-weight: 700;
    color: var(--theme-color-primary, #119A48);
    margin: 0 0 0.35rem;
}
.fed-hero p {
    color: #64748b;
    margin: 0;
    max-width: 720px;
}
.fed-panel {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 6px rgba(15, 23, 42, 0.04);
    margin-bottom: 1.25rem;
}
.fed-panel__body { padding: 1.25rem 1.5rem; }
.fed-tabs {
    border-bottom: 1px solid #e2e8f0;
    padding: 0 1rem;
    gap: 0.25rem;
}
.fed-tabs .nav-link {
    color: #64748b;
    font-weight: 500;
    border: none;
    border-bottom: 2px solid transparent;
    border-radius: 0;
    padding: 0.85rem 1rem;
    font-size: 0.9rem;
}
.fed-tabs .nav-link:hover { color: var(--theme-color-primary, #119A48); }
.fed-tabs .nav-link.active {
    color: var(--theme-color-primary, #119A48);
    background: transparent;
    border-bottom-color: var(--theme-color-primary, #119A48);
    font-weight: 600;
}
.fed-btn-primary {
    background: var(--theme-color-primary, #119A48);
    border-color: var(--theme-color-primary, #119A48);
    color: #fff;
}
.fed-btn-primary:hover {
    filter: brightness(1.06);
    background: var(--theme-color-primary, #119A48);
    border-color: var(--theme-color-primary, #119A48);
    color: #fff;
}
.fed-btn-outline {
    border: 1px solid var(--theme-color-primary, #119A48);
    color: var(--theme-color-primary, #119A48);
    background: transparent;
}
.fed-btn-outline:hover {
    background: color-mix(in srgb, var(--theme-color-primary, #119A48) 8%, white);
    color: var(--theme-color-primary, #119A48);
    border-color: var(--theme-color-primary, #119A48);
}
.fed-hub-card {
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    transition: box-shadow 0.15s ease, border-color 0.15s ease;
    height: 100%;
}
.fed-hub-card:hover {
    border-color: color-mix(in srgb, var(--theme-color-primary, #119A48) 35%, #e2e8f0);
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.07);
}
.fed-hub-card .card-title { font-size: 0.95rem; font-weight: 600; color: #0f172a; }
.fed-empty {
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    color: #64748b;
}
.fed-page .form-label { font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.35rem; }
</style>
@endsection

@section('content')
<div class="fed-page">
    <div class="container fed-shell">
        <div class="fed-hero d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <h1><i class="fa fa-globe-africa me-2"></i>{{ $pageTitle }}</h1>
                <p>{{ $pageDescription }}</p>
            </div>
            <a href="{{ url('records') }}" class="btn btn-sm fed-btn-outline">
                <i class="fa fa-search me-1"></i>Back to records search
            </a>
        </div>

        @if($hubs->isEmpty())
            <div class="fed-empty">
                No member state content is available yet.
            </div>
        @else
            <div class="fed-panel">
                <div class="fed-panel__body">
                    <form method="get" action="{{ route('federation.browse') }}" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Member state</label>
                            <select name="hub" class="form-control select2">
                                <option value="">All member states</option>
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
                            <button type="submit" class="btn fed-btn-primary w-100">Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="fed-panel">
                <ul class="nav fed-tabs">
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
                <div class="fed-panel__body">
                    @if(($type ?? 'publications') === 'publications')
                        @forelse($publications as $row)
                            @include('partials.federation.publication_card', ['row' => $row])
                        @empty
                            <div class="fed-empty mb-0">No approved member state publications match this filter.</div>
                        @endforelse
                        <div class="py-3">{{ $publications->links() }}</div>
                    @else
                        @forelse($forums as $forum)
                            @include('partials.federation.forum_card', ['forum' => $forum])
                        @empty
                            <div class="fed-empty mb-0">No approved member state forums match this filter.</div>
                        @endforelse
                        <div class="py-3">{{ $forums->links() }}</div>
                    @endif
                </div>
            </div>

            <div class="mt-4 mb-2">
                <h2 class="h5 fw-semibold text-body mb-3">Linked member states</h2>
            </div>
            <div class="row">
                @foreach($hubs as $hub)
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card fed-hub-card h-100">
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title mb-1">{{ $hub->name }}</h6>
                                @if($hub->mappedCountry)
                                    <small class="text-muted d-block mb-2">{{ $hub->mappedCountry->name }}</small>
                                @endif
                                <small class="text-muted d-block mb-3">
                                    Last sync: {{ $hub->last_synced_at ? $hub->last_synced_at->diffForHumans() : 'Never' }}
                                </small>
                                <div class="mt-auto d-flex flex-wrap gap-2">
                                    <a href="{{ route('federation.browse', ['hub' => $hub->id]) }}" class="btn btn-sm fed-btn-outline">Browse</a>
                                    <a href="{{ $hub->base_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-link px-0" style="color:var(--theme-color-primary,#119A48);">Visit hub</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
