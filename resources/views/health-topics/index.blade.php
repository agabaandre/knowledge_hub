@extends('layouts.app')

@section('title', 'Health Topics')

@section('content')
<div class="container-fluid pt-5" style="max-width:900px;">
    <div class="page-header mb-3">
        <div class="row">
            <div class="col-12">
                <h3 class="fw-bold" style="font-size:1.5rem;">Health Topics</h3>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-secondary">Home</a></li>
                    <li class="breadcrumb-item active">Health Topics</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <input type="text" id="topic-search" class="form-control form-control-lg shadow-sm border-0" placeholder="Search health topics..." style="border-radius: 0.7rem; font-size:1.1rem;">
        </div>
    </div>

    <div class="row" id="topics-list">
        @forelse($groupedTags->flatten() as $tag)
            <div class="col-12 col-md-6 mb-2 topic-card-col">
                <a href="{{ route('health-topics.show', $tag->id) }}" class="topic-card-link">
                    <div class="topic-card topic-card-small p-2 d-flex flex-column flex-md-row align-items-md-center justify-content-between h-100 border-secondary">
                        <div class="topic-info">
                            <div class="topic-title fw-bold mb-1" style="font-size:1rem;">{{ $tag->tag_text }}</div>
                            @if($tag->overview)
                                <div class="topic-desc text-secondary" style="font-size:0.85rem; line-height:1.3;">{{ Str::limit(strip_tags($tag->overview), 60) }}</div>
                            @endif
                        </div>
                        <div class="ms-md-3 mt-2 mt-md-0">
                            <span class="btn btn-dark btn-xs px-2 py-1" style="border-radius:1.2rem; font-size:0.8rem;">View <i class="fa fa-arrow-right ms-1" style="font-size:0.8em;"></i></span>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="empty-state">
                    <i class="fa fa-exclamation-triangle fa-2x text-muted mb-2"></i>
                    <h5 style="font-size:1rem;">No Health Topics Available</h5>
                    <p class="text-muted" style="font-size:0.9rem;">There are currently no health emergency topics configured in the system.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

<style>
.topic-card-link {
    text-decoration: none;
    display: block;
    height: 100%;
}
.topic-card-small {
    background: #fff;
    border-radius: 0.7rem;
    box-shadow: 0 1px 6px 0 rgba(30,34,90,0.03);
    border: 1px solid #343a40;
    transition: box-shadow 0.15s, border 0.15s, transform 0.15s;
    cursor: pointer;
    height: 100%;
    min-height: 60px;
    max-height: 110px;
}
.topic-card-small:hover {
    box-shadow: 0 2px 12px 0 rgba(30,34,90,0.18);
    border: 1.5px solid #222;
    transform: translateY(-1px) scale(1.01);
}
.topic-title {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.2rem;
}
.topic-desc {
    font-size: 0.85rem;
    margin-bottom: 0;
}
.btn-xs {
    padding: 0.15rem 0.7rem;
    font-size: 0.8rem;
    line-height: 1.2;
    border-radius: 0.2rem;
}
.empty-state {
    padding: 1rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('topic-search');
    const cardCols = Array.from(document.querySelectorAll('.topic-card-col'));
    searchInput.addEventListener('input', function() {
        const val = this.value.trim().toLowerCase();
        cardCols.forEach(col => {
            const text = col.innerText.toLowerCase();
            col.style.display = text.includes(val) ? '' : 'none';
        });
    });
});
</script>
@endsection 