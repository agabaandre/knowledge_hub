@php $primary = settings()->primary_color ?? '#222'; @endphp
@extends('layouts.app')

@section('title', $tag->tag_text . ' - Health Topics')

@section('content')
<div class="container-fluid py-5">

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
                <div class="publication-card-list">
                    @foreach($publications as $publication)
                        <div class="pub-card d-flex flex-column flex-md-row align-items-start align-items-md-center p-3 bg-white border shadow-sm rounded-4">
                            <!-- Image -->
                            <div class="pub-image-wrapper me-md-3 mb-3 mb-md-0">
                                <img src="{{ $publication->image_url ?? $publication->cover ?? asset('assets/img/default-publication.png') }}" alt="{{ $publication->title }}" class="pub-image">
                            </div>

                            <!-- Content -->
                            <div class="flex-grow-1 d-flex flex-column flex-md-row align-items-md-center justify-content-between w-100">
                                <div class="flex-grow-1">
                                    <a href="{{ url('records/resource?id=' . $publication->id) }}" class="pub-title-link text-dark">
                                        <span class="fw-semibold pub-title">{{ $publication->title }}</span>
                                    </a>
                                    <div class="pub-desc text-secondary mt-1">
                                        {{ Str::limit(strip_tags($publication->description), 120) }}
                                    </div>
                                </div>

                                <!-- Meta -->
                                <div class="pub-meta text-md-end mt-3 mt-md-0 ms-md-4 text-secondary">
                                    @if($publication->author)
                                        <div><i class="fa fa-user me-1"></i> {{ $publication->author->name }}</div>
                                    @endif
                                    @if($publication->created_at)
                                        <div><i class="fa fa-calendar me-1"></i> {{ $publication->created_at->format('M Y') }}</div>
                                    @endif
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
.publication-card-list {
    display: flex;
    flex-direction: column;
    gap: 1.2rem;
}
.pub-card {
    transition: all 0.2s ease;
    border: 1px solid #dee2e6;
}
.pub-card:hover {
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
    border-color: {{ $primary }};
    transform: translateY(-3px);
}
.pub-image-wrapper {
    width: 90px;
    height: 90px;
    background: #f0f2f5;
    border-radius: 0.8rem;
    overflow: hidden;
    flex-shrink: 0;
}
.pub-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 0.8rem;
}
.pub-title-link {
    text-decoration: none;
    font-size: 1.05rem;
    color: #212529;
}
.pub-title-link:hover {
    color: {{ $primary }};
    text-decoration: underline;
}
.pub-title {
    font-size: 1.08rem;
    font-weight: 600;
}
.pub-desc {
    font-size: 0.96rem;
    color: #6c757d;
}
.pub-meta {
    font-size: 0.9rem;
}
.empty-state {
    padding: 1rem;
}
@media (max-width: 600px) {
    .pub-card {
        flex-direction: column !important;
        align-items: flex-start !important;
    }
    .pub-image-wrapper {
        width: 100%;
        height: 180px;
        margin-bottom: 1rem;
    }
}
</style>
@endsection
