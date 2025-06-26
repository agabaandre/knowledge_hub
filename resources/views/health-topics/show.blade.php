@php $primary = settings()->primary_color ?? '#222'; @endphp
@extends('layouts.app')

@section('title', $tag->tag_text . ' - Health Topics')

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
                            <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden publication-card">
                                <div class="row g-0 h-100">
                                    <div class="col-4">
                                        <img src="{{ $publication->image_url ?? $publication->cover ?? asset('assets/img/default-publication.png') }}"
                                             alt="{{ $publication->title }}"
                                             class="img-fluid h-100 w-100 object-fit-cover rounded-start-4">
                                    </div>
                                    <div class="col-8 d-flex flex-column justify-content-between p-3">
                                        <div>
                                            <a href="{{ url('records/resource?id=' . $publication->id) }}" class="text-dark pub-title-link">
                                                <h6 class="mb-1 fw-semibold pub-title" style="line-height: 1.3;">
                                                    {{ Str::limit(strip_tags($publication->title), 80) }}
                                                </h6>
                                            </a>
                                            <div class="text-muted small pub-desc">
                                                {{ Str::limit(strip_tags($publication->description), 120) }}
                                            </div>
                                        </div>
                                        <div class="mt-2 text-secondary small pub-meta">
                                            @if($publication->author)
                                                <div><i class="fa fa-user me-1"></i> {{ $publication->author->name }}</div>
                                            @endif
                                            @if($publication->created_at)
                                                <div><i class="fa fa-calendar me-1"></i> {{ $publication->created_at->format('M Y') }}</div>
                                            @endif
                                        </div>
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
                <!-- No Publications -->
                <div class="text-center py-5">
                    <div class="empty-state">
                        <i class="fa fa-file-alt fa-2x text-muted mb-2"></i>
                        <h5>No Publications Found</h5>
                        <p class="text-secondary">There are currently no publications tagged with <strong>"{{ $tag->tag_text }}"</strong>.</p>
                        <a href="{{ url('records') }}" class="btn theme-primary mt-2 text-white" style="border: none;">Browse All Publications</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Styles -->
<style>
    .publication-card .pub-title-link:hover {
        color: {{ $primary }};
        text-decoration: underline;
    }
    .publication-card .pub-title {
        font-size: 1rem;
        font-weight: 600;
    }
    .publication-card .pub-desc {
        font-size: 0.9rem;
    }
    .publication-card .pub-meta {
        font-size: 0.85rem;
    }
    .object-fit-cover {
        object-fit: cover;
    }
    .empty-state {
        padding: 2rem;
    }
</style>
@endsection
