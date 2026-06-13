@extends('layouts.app')

@php
    $site = settings()->site_name ?? 'Africa Health Knowledge Hub';
    $pageTitle = 'Knowledge Hub Courses — '.$site;
    $pageDescription = 'Browse online courses on public health, leadership, and technical topics on '.$site.'. Compare providers, ratings, and enrol in self-paced or facilitated learning.';
@endphp

@section('styles')
<style>
    .course-card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        transition: transform 0.2s ease-in-out;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .course-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .course-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .course-body {
        padding: 1rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .course-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--theme-color-primary);
    }

    .course-meta {
        font-size: 0.875rem;
        color: #6c757d;
    }

    .course-summary {
        font-size: 0.95rem;
        color: #555;
        margin: 0.5rem 0;
    }

    .course-button {
        text-align: right;
        margin-top: auto;
    }

    .course-button .btn {
        padding: 6px 16px;
    }

    .course-rating {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 0.75rem 0;
        font-size: 0.9rem;
    }

    .course-rating-stars {
        display: flex;
        gap: 0.2rem;
        color: #ffc107;
    }

    .course-rating-stars .fa-star {
        font-size: 1rem;
    }

    .course-rating-stars .fa-star-o {
        color: #ddd;
    }

    .course-rating-value {
        font-weight: 600;
        color: #2d3748;
    }

    .course-rating-count {
        color: #718096;
        font-size: 0.85rem;
    }
    .courses-infinite-sentinel { height: 1px; width: 100%; }
    .courses-infinite-loader { color: #64748b; font-size: 0.875rem; padding: 0.5rem 0; }
</style>
@endsection

@section('content')
<!-- ======================= Banner ======================== -->
<div class="bg-light rounded py-5" style="background-image: url({{ asset('frontend/img/dots.png') }}); background-repeat:repeat-x; background-size:contain;">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4 class="mb-0 ft-medium fs-lg">
                        Knowledge Hub Courses
                    </h4>
                    @if(!empty($canFetchCourses))
                        <button type="button" class="btn btn-sm theme-primary" id="courseFetchBtn">
                            <i class="fa fa-refresh me-1"></i> Fetch courses
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ======================= Course Cards ======================== -->
<div class="container middle">
    @include('courses.partials.sync_progress')
    <div id="courses-list-wrap"
         @if(($coursesInfiniteScroll ?? false) && $courses instanceof \Illuminate\Pagination\AbstractPaginator)
         data-infinite-scroll="1"
         data-current-page="{{ $courses->currentPage() }}"
         data-last-page="{{ $courses->lastPage() }}"
         data-total="{{ $courses->total() }}"
         data-loaded="{{ (($courses->currentPage() - 1) * $courses->perPage()) + $courses->count() }}"
         @endif>
    <div class="row justify-content-center mt-3" id="coursesGrid">
        @if ($courses->count() > 0)
            @include('courses.partials.course_list_items', ['courses' => $courses])
        @endif
    </div>

    @if(($coursesInfiniteScroll ?? false) && $courses instanceof \Illuminate\Pagination\AbstractPaginator && $courses->total() > 0)
        @php $loadedCourseCount = (($courses->currentPage() - 1) * $courses->perPage()) + $courses->count(); @endphp
        <div class="courses-infinite-footer py-3 text-center" id="courses-infinite-footer">
            <p class="text-muted small mb-2" id="courses-infinite-status">
                Showing {{ number_format($loadedCourseCount) }} of {{ number_format($courses->total()) }} courses
            </p>
            @if($courses->hasMorePages())
                <div id="courses-infinite-sentinel" class="courses-infinite-sentinel" aria-hidden="true"></div>
                <div id="courses-infinite-loader" class="courses-infinite-loader d-none" aria-live="polite">
                    <i class="fa fa-spinner fa-spin me-1"></i>Loading more courses…
                </div>
            @else
                <p class="text-muted small mb-0" id="courses-infinite-complete">All courses loaded</p>
            @endif
        </div>
    @elseif($courses instanceof \Illuminate\Pagination\AbstractPaginator && $courses->hasPages())
    <!-- Pagination -->
    <div class="row justify-content-center mt-3">
        {{ $courses->links() }}
    </div>
    @endif
    </div>
</div>
@endsection

@section('scripts')
    <script>
        window.coursesInfiniteScrollConfig = {
            enabled: @json((bool) ($coursesInfiniteScroll ?? false)),
            pageUrl: @json(route('courses.page'))
        };
        window.COURSES_INFINITE_STATUS_COMPLETE = 'All courses loaded';
        window.COURSES_INFINITE_STATUS_ERROR = 'Could not load more courses. Tap to retry.';
    </script>
    <script src="{{ asset('js/courses-index-infinite.js') }}?v={{ @filemtime(public_path('js/courses-index-infinite.js')) }}"></script>
@endsection
