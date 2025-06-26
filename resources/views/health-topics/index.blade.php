@php $primary = settings()->primary_color ?? '#222'; @endphp
@extends('layouts.app')

@section('title', 'Health Topics')

@section('content')
<div class="container-fluid py-5" style="max-width: 960px;">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row">
            <div class="col-12">
                <h2 class="fw-bold text-primary" style="font-size:1.75rem;">Health Topics</h2>
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-secondary">Home</a></li>
                    <li class="breadcrumb-item active">Health Topics</li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="row mb-4">
        <div class="col-12">
            <input type="text" id="topic-search" class="form-control form-control-lg shadow-sm border-0" placeholder="🔍 Search health topics..." style="border-radius: 0.8rem; font-size: 1.05rem;">
        </div>
    </div>

    <!-- Health Topic Cards -->
    <div class="row" id="topics-list">
        @forelse($groupedTags->flatten() as $tag)
            <div class="col-12 col-md-6 mb-3 topic-card-col">
                <a href="{{ route('health-topics.show', $tag->id) }}" class="topic-card-link">
                    <div class="topic-card p-3 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between h-100 border">
                        <div class="topic-info pe-md-3">
                            <div class="topic-title text-dark fw-semibold mb-1" style="font-size: 1.05rem;">
                                {{ $tag->tag_text }}
                            </div>
                            @if($tag->overview)
                                <div class="topic-desc text-muted" style="font-size: 0.875rem; line-height: 1.4;">
                                    {{ Str::limit(strip_tags($tag->overview), 80) }}
                                </div>
                            @endif
                        </div>
                        <div class="ms-md-auto mt-3 mt-md-0">
                            <span class="btn theme-primary btn-xs shadow-sm px-3 py-1">
                                View <i class="fa fa-arrow-right ms-1"></i>
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="empty-state">
                    <i class="fa fa-info-circle fa-2x text-muted mb-2"></i>
                    <h5 class="fw-bold" style="font-size: 1rem;">No Health Topics Available</h5>
                    <p class="text-muted" style="font-size: 0.9rem;">There are currently no health emergency topics configured in the system.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Styles -->
<style>
    .topic-card-link {
        text-decoration: none;
        display: block;
        height: 100%;
    }

    .topic-card {
        background: #fff;
        border-radius: 0.75rem;
        border: 1px solid #e0e0e0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        transition: all 0.15s ease-in-out;
    }

    .topic-card:hover {
        box-shadow: 0 6px 18px rgba(0,0,0,0.08);
        border-color: {{ $primary }};
        transform: translateY(-2px);
    }

    .btn-xs {
        font-size: 0.8rem;
        border-radius: 1.2rem;
    }

    .empty-state {
        padding: 2rem;
    }
</style>

<!-- Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('topic-search');
        const cardCols = Array.from(document.querySelectorAll('.topic-card-col'));

        searchInput.addEventListener('input', function () {
            const val = this.value.trim().toLowerCase();
            cardCols.forEach(col => {
                const text = col.innerText.toLowerCase();
                col.style.display = text.includes(val) ? '' : 'none';
            });
        });
    });
</script>
@endsection
