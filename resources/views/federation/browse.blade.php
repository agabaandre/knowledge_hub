@php
    $hide_search = true;
@endphp
@extends('layouts.app')

@section('title', $pageTitle ?? 'Partner country knowledge hubs')

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
.fed-hubs {
    align-items: stretch;
}
.fed-hub-card {
    position: relative;
    overflow: hidden;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    min-height: 118px;
    height: auto;
    background: #fff;
    transition: box-shadow 0.15s ease, border-color 0.15s ease, transform 0.15s ease;
}
.fed-hub-card:hover,
.fed-hub-card:focus-within {
    border-color: color-mix(in srgb, var(--theme-color-primary, #119A48) 40%, #e2e8f0);
    box-shadow: 0 8px 22px rgba(15, 23, 42, 0.1);
    transform: translateY(-2px);
}
.fed-hub-card__bg {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    transform: scale(1.06);
    filter: saturate(1.05);
}
.fed-hub-card__veil {
    position: absolute;
    inset: 0;
    background: linear-gradient(100deg, rgba(255,255,255,0.96) 0%, rgba(255,255,255,0.88) 42%, rgba(255,255,255,0.35) 100%);
}
.fed-hub-card__hit {
    position: absolute;
    inset: 0;
    z-index: 1;
    border-radius: inherit;
}
.fed-hub-card__hit:focus {
    outline: 2px solid var(--theme-color-primary, #119A48);
    outline-offset: 2px;
}
.fed-hub-card__body {
    position: relative;
    z-index: 1;
    padding: 0.9rem 1rem 0.85rem;
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
    min-height: 118px;
}
.fed-hub-card__title {
    font-size: 1rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    line-height: 1.3;
    padding-right: 1.5rem;
}
.fed-hub-card__meta {
    color: #475569;
    font-size: 0.82rem;
}
.fed-hub-card__sync {
    color: #64748b;
    font-size: 0.75rem;
}
.fed-hub-card__actions {
    margin-top: auto;
    padding-top: 0.55rem;
    display: flex;
    align-items: center;
    gap: 0.85rem;
}
.fed-hub-card__browse {
    font-size: 0.78rem;
    font-weight: 600;
    color: var(--theme-color-primary, #119A48);
}
.fed-hub-card__visit {
    position: relative;
    z-index: 2;
    font-size: 0.78rem;
    font-weight: 600;
    color: #334155;
    text-decoration: none;
}
.fed-hub-card__visit:hover {
    color: var(--theme-color-primary, #119A48);
    text-decoration: underline;
}
.fed-empty {
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    color: #64748b;
}
.fed-page .form-label { font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.35rem; }
.fed-page .federation-publication-card .publication-card-row {
    align-items: stretch !important;
}
.fed-page .federation-publication-card .publication-image-col {
    min-height: 372px !important;
    max-height: none !important;
    height: auto !important;
    align-self: stretch;
}
.fed-page .federation-publication-card .publication-image-link {
    min-height: 372px !important;
    height: 100% !important;
    max-height: none !important;
}
.fed-page .federation-publication-card .publication-image {
    min-height: 372px !important;
    height: 100% !important;
    max-height: none !important;
    object-fit: cover !important;
    object-position: center;
}
.fed-hubs-carousel {
    margin: 0 0 1.25rem;
}
.fed-hubs-viewport {
    overflow: hidden;
}
.fed-hubs-track {
    display: flex;
    gap: 1rem;
    width: max-content;
}
.fed-hub-slide {
    flex: 0 0 min(340px, 82vw);
    width: min(340px, 82vw);
}
.fed-hubs-carousel.is-animated .fed-hubs-track {
    animation: fed-rtl-scroll 36s linear infinite;
}
.fed-hubs-carousel.is-animated:hover .fed-hubs-track,
.fed-hubs-carousel.is-animated:focus-within .fed-hubs-track {
    animation-play-state: paused;
}
@keyframes fed-rtl-scroll {
    from { transform: translateX(0); }
    to { transform: translateX(-50%); }
}
@media (prefers-reduced-motion: reduce) {
    .fed-hubs-carousel.is-animated .fed-hubs-track {
        animation: none;
    }
}
</style>
@endsection

@section('content')
<div class="fed-page">
    <div class="container fed-shell">
        <div class="fed-hero d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <h1><i class="fa fa-globe me-2" aria-hidden="true"></i>{{ $pageTitle }}</h1>
                <p>{{ $pageDescription }}</p>
            </div>
            <a href="{{ url('records') }}" class="btn btn-sm fed-btn-outline">
                <i class="fa fa-search me-1"></i>Back to records search
            </a>
        </div>

        @if(($linkedHubs ?? $hubs)->isEmpty())
            <div class="fed-empty">
                No partner country content is available yet.
            </div>
        @else
            @if(($publications->total() + $forums->total()) === 0)
                <div class="fed-empty mb-3">
                    Partner hubs are linked. Approved publications and discussions will appear here after sync and review.
                </div>
            @endif
            @php
                $partnerHubs = ($linkedHubs ?? $hubs);
                $copies = max(2, (int) ceil(4 / max(1, $partnerHubs->count())));
                $hubHalf = collect();
                for ($i = 0; $i < $copies; $i++) {
                    $hubHalf = $hubHalf->concat($partnerHubs);
                }
                $hubCarouselSlides = $hubHalf->concat($hubHalf);
            @endphp
            <div class="mb-2">
                <h2 class="h5 fw-semibold text-body mb-3">Linked partner hubs</h2>
            </div>
            <div class="fed-hubs-carousel is-animated" aria-label="Linked partner hubs">
                <div class="fed-hubs-viewport">
                    <div class="fed-hubs-track">
                        @foreach($hubCarouselSlides as $hub)
                            @php
                                $browseUrl = route('federation.browse', ['hub' => $hub->id]);
                                $flagUrl = $hub->countryFlagUrl();
                                $iso2 = $hub->countryIso2();
                            @endphp
                            <div class="fed-hub-slide">
                                <article class="fed-hub-card">
                                    @if($flagUrl)
                                        <div class="fed-hub-card__bg" style="background-image: url('{{ $flagUrl }}');"></div>
                                    @elseif($iso2)
                                        <span class="flag-icon flag-icon-{{ $iso2 }} fed-hub-card__bg" aria-hidden="true"></span>
                                    @endif
                                    <div class="fed-hub-card__veil"></div>
                                    <a class="fed-hub-card__hit" href="{{ $browseUrl }}" aria-label="Browse {{ $hub->name }}"></a>
                                    <div class="fed-hub-card__body">
                                        <h3 class="fed-hub-card__title">{{ $hub->name }}</h3>
                                        @if($hub->mappedCountry)
                                            <div class="fed-hub-card__meta">{{ $hub->mappedCountry->name }}</div>
                                        @endif
                                        <div class="fed-hub-card__sync">
                                            Last sync: {{ $hub->last_synced_at ? $hub->last_synced_at->diffForHumans() : 'Never' }}
                                        </div>
                                        <div class="fed-hub-card__actions">
                                            <span class="fed-hub-card__browse">Browse</span>
                                            <a href="{{ $hub->base_url }}" target="_blank" rel="noopener noreferrer" class="fed-hub-card__visit">Visit hub</a>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="fed-panel">
                <div class="fed-panel__body">
                    <form method="get" action="{{ route('federation.browse') }}" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Country hub</label>
                            <select name="hub" class="form-control select2">
                                <option value="">All partner hubs</option>
                                @foreach(($linkedHubs ?? $hubs) as $hub)
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
                            @include('partials.federation.publication_card', ['row' => $row, 'excerptWords' => 140])
                        @empty
                            <div class="fed-empty mb-0">No approved partner publications match this filter.</div>
                        @endforelse
                        <div class="py-3">{{ $publications->links() }}</div>
                    @else
                        @forelse($forums as $forum)
                            @include('partials.federation.forum_card', ['forum' => $forum])
                        @empty
                            <div class="fed-empty mb-0">No approved partner forums match this filter.</div>
                        @endforelse
                        <div class="py-3">{{ $forums->links() }}</div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
