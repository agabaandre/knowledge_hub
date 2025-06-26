@php $primary = settings()->primary_color ?? '#222'; @endphp
@extends('layouts.app')

@section('title', $tag->tag_text . ' - Health Topics')

@section('styles')
<style>
    .publication-card {
        border: 1px solid #e0e0e0;
        border-radius: 0.75rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        overflow: hidden;
        transition: all 0.2s ease-in-out;
    }

    .publication-card:hover {
        box-shadow: 0 6px 20px rgba(0,0,0,0.12);
        border-color: {{ $primary }};
        transform: translateY(-2px);
    }

    .publication-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .ratio-4x3 {
        aspect-ratio: 4 / 3;
        background-color: #f8f9fa;
    }

    .pub-title {
        font-size: 1.05rem;
        font-weight: 600;
        line-height: 1.4;
    }

    .pub-desc {
        font-size: 0.92rem;
        color: #6c757d;
    }

    .pub-meta {
        font-size: 0.85rem;
        color: #888;
    }

    .pub-title-link:hover {
        color: {{ $primary }};
        text-decoration: underline;
    }

    .empty-state {
        padding: 2rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-5" style="max-width:1020px;">

    <!-- Topic Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold text-primary" style="font-size:2rem;">{{ $tag->tag_text }}</h2>
            @if($tag->overview)
                <div class="mb-4 text-secondary" style="font-size:1.08rem; line-height:1.7;">
                    {!! $tag->overview !!}
                </div>
            @endif
        </div>
    </div>

    <!-- Related Publications -->
    <div class="row">
        <div class="col-12">
            <h5 class="mb-3 fw-semibold text-dark">Related Publications</h5>

            @if($publications->count() > 0)
                <div class="row g-4">
                    @foreach($publications as $publication)
                        <div class="col-md-6">
                            <div class="card publication-card h-100 d-flex flex-column">
                                <!-- Cover Image -->
                                <div class="ratio ratio-4x3">
                                    <img src="{{ $publication->image_url ?? $publication->cover ?? asset('assets/img/default-publication.png') }}"
                                         alt="{{ $publication->title }}">
                                </div>

                                <!-- Card Body -->
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div>
                                        <a href="{{ url('records/resource?id=' . $publication->id) }}"
                                           class="text-decoration-none pub-title-link text-dark">
                                            <h6 class="pub-title mb-2">
                                                {{ Str::limit(strip_tags($publication->title), 90) }}
                                            </h6>
                                        </a>
                                        <p class="pub-desc mb-3">
                                            {{ Str::limit(strip_tags($publication->description), 120) }}
                                        </p>
                                    </div>
                                    <div class="pub-meta">
                                        @if($publication->author)
                                            <div><i class="fa fa-user me-1"></i>{{ $publication->author->name }}</div>
                                        @endif
                                        @if($publication->created_at)
                                            <div><i class="fa fa-calendar me-1"></i>{{ $publication->created_at->format('M Y') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($publications->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $publications->links() }}
                    </div>
                @endif
            @else
                <!-- No Publications Message -->
                <div class="text-center py-5">
                    <div class="empty-state">
                        <i class="fa fa-file-alt fa-2x text-muted mb-2"></i>
                        <h5>No Publications Found</h5>
                        <p class="text-secondary">There are currently no publications tagged with <strong>"{{ $tag->tag_text }}"</strong>.</p>
                        <a href="{{ url('records') }}" class="btn theme-primary text-white mt-2" style="border: none;">Browse All Publications</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
