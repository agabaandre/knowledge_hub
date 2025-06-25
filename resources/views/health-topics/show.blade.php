@php $primary = settings()->primary_color ?? '#222'; @endphp
@extends('layouts.app')

@section('title', $tag->tag_text . ' - Health Topics')

@section('content')
<div class="container-fluid pt-5" style="max-width:900px;">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-2" style="font-size:2rem;">{{ $tag->tag_text }}</h2>
            @if($tag->overview)
                <div class="mb-4 text-secondary" style="font-size:1.08rem; line-height:1.7;">
                    {!! $tag->overview !!}
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <h5 class="mb-3" style="font-weight:600;">Related Publications</h5>
            @if($publications->count() > 0)
                <div class="publication-card-list">
                    @foreach($publications as $publication)
                        <div class="pub-card mb-3 p-3 shadow-sm bg-white rounded-4 d-flex flex-row align-items-center border-secondary">
                            <div class="pub-image-wrapper me-3 flex-shrink-0">
                                <img src="{{ $publication->image_url ?? $publication->cover ?? asset('assets/img/default-publication.png') }}" alt="{{ $publication->title }}" class="pub-image">
                            </div>
                            <div class="flex-grow-1 d-flex flex-column flex-md-row align-items-md-center justify-content-between w-100">
                                <div class="flex-grow-1">
                                    <a href="{{ url('records/resource?id=' . $publication->id) }}" class="pub-title-link text-dark">
                                        <span class="fw-semibold pub-title">{{ $publication->title }}</span>
                                    </a>
                                    <div class="pub-desc text-secondary mt-1" style="font-size:0.97rem;">
                                        {{ Str::limit(strip_tags($publication->description), 120) }}
                                    </div>
                                </div>
                                <div class="pub-meta text-end text-md-start mt-2 mt-md-0 ms-md-3" style="min-width:160px;">
                                    @if($publication->author)
                                        <div class="text-secondary" style="font-size:0.93rem;"><i class="fa fa-user me-1"></i> {{ $publication->author->name }}</div>
                                    @endif
                                    @if($publication->created_at)
                                        <div class="text-secondary" style="font-size:0.93rem;"><i class="fa fa-calendar me-1"></i> {{ $publication->created_at->format('M Y') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($publications->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $publications->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <div class="empty-state">
                        <i class="fa fa-file-alt fa-2x text-muted mb-2"></i>
                        <h5>No Publications Found</h5>
                        <p class="text-secondary">There are currently no publications tagged with "{{ $tag->tag_text }}".</p>
                        <a href="{{ url('records') }}" class="btn browse-btn-custom mt-2" style="background: {{ $primary }}; color: #fff; border: none;">Browse All Publications</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.publication-card-list {
    display: flex;
    flex-direction: column;
    gap: 1.1rem;
}
.pub-card {
    background: #fff;
    border-radius: 1.1rem;
    box-shadow: 0 2px 12px 0 rgba(30,34,90,0.07);
    border: 1px solid #343a40;
    transition: box-shadow 0.18s, border 0.18s, transform 0.18s;
    cursor: pointer;
    min-height: 90px;
}
.pub-card:hover {
    box-shadow: 0 6px 24px 0 rgba(30,34,90,0.13);
    border: 1.5px solid {{ $primary }};
    transform: translateY(-2px) scale(1.01);
}
.pub-image-wrapper {
    width: 70px;
    height: 70px;
    border-radius: 0.7rem;
    overflow: hidden;
    background: #f4f6fa;
    display: flex;
    align-items: center;
    justify-content: center;
}
.pub-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 0.7rem;
    display: block;
}
.pub-title-link {
    color: #212529;
    font-size: 1.08rem;
    text-decoration: none;
    transition: color 0.15s;
}
.pub-title-link:hover {
    color: {{ $primary }};
    text-decoration: underline;
}
.browse-btn-custom:hover, .browse-btn-custom:focus {
    filter: brightness(0.92);
    color: #fff;
}
.pub-title {
    font-size: 1.08rem;
    font-weight: 600;
}
.pub-desc {
    font-size: 0.97rem;
}
.pub-meta {
    font-size: 0.93rem;
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
        height: 160px;
        margin-bottom: 0.7rem;
    }
    .pub-image {
        width: 100%;
        height: 100%;
        border-radius: 0.7rem;
    }
}
</style>
@endsection 