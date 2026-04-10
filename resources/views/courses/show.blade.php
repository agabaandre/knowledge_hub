@extends('layouts.app')

@php
    $site = settings()->site_name ?? 'Africa Health Knowledge Hub';
    $summaryPlain = trim(strip_tags($course->summary ?? ''));
    $pageTitle = $course->fullname.' — '.$site;
    $pageDescription = $summaryPlain !== ''
        ? \Illuminate\Support\Str::limit($summaryPlain, 158)
        : 'Learn more about this course on '.$site.': overview, provider, ratings, and how to enrol.';
@endphp

@section('styles')
<style>
    .card-title {
        font-size: 1.8rem;
    }
    .theme-primary {
        background-color: var(--theme-color-primary) !important;
        border: none;
    }
    .theme-secondary {
        background-color: var(--theme-color-secondary) !important;
        border: none;
    }
    .btn:hover, .btn:focus {
        filter: brightness(0.95);
    }
    .course-rating {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin: 1rem 0;
        font-size: 1rem;
    }
    .course-rating-stars {
        display: flex;
        gap: 0.3rem;
        color: #ffc107;
    }
    .course-rating-stars .fa-star {
        font-size: 1.25rem;
    }
    .course-rating-stars .fa-star-half-o {
        font-size: 1.25rem;
        color: #ffc107;
    }
    .course-rating-stars .fa-star-o {
        color: #ddd;
        font-size: 1.25rem;
    }
    .course-rating-value {
        font-weight: 700;
        font-size: 1.1rem;
        color: #2d3748;
    }
    .course-rating-count {
        color: #718096;
        font-size: 0.95rem;
    }
</style>
@endsection


@section('title', $course->fullname)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <!-- Course Title -->
                    <h2 class="card-title fw-bold text-primary mb-3" style="font-size: 1.8rem;">                                                                
                        {{ $course->fullname }}
                    </h2>

                    <!-- Course Rating -->
                    @if($course->rating && $course->rating > 0)
                    <div class="course-rating">
                        <div class="course-rating-stars">
                            @php
                                $fullStars = floor($course->rating);
                                $hasHalfStar = ($course->rating - $fullStars) >= 0.5;
                                $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                            @endphp
                            @for ($i = 0; $i < $fullStars; $i++)
                                <i class="fa fa-star"></i>
                            @endfor
                            @if ($hasHalfStar)
                                <i class="fa fa-star-half-o"></i>
                            @endif
                            @for ($i = 0; $i < $emptyStars; $i++)
                                <i class="fa fa-star-o"></i>
                            @endfor
                        </div>
                        <span class="course-rating-value">{{ number_format($course->rating, 1) }}</span>
                        <span class="course-rating-count">({{ number_format($course->rating * 20) }}+ reviews)</span>
                    </div>
                    @endif

                    <!-- Status and Provider -->
                    <div class="mb-3 text-muted small">
                        <strong>Status:</strong> {{ $course->is_active ? 'Active' : 'Inactive' }}
                        @if($course->provider)
                            | <strong>Provider:</strong> {{ $course->provider }}
                        @endif
                    </div>

                    <!-- Cover Image -->
                    @if($course->cover_image)
                        <div class="text-center mb-4">
                            <img src="{{ $course->cover_image }}" 
                                 alt="{{ $course->fullname }}" 
                                 class="img-fluid rounded-3 shadow-sm" 
                                 style="max-height: 320px; object-fit: cover;">
                        </div>
                    @endif

                    <!-- Summary -->
                    @if($course->summary)
                        <div class="mb-4">
                            <h5 class="fw-semibold mb-2 text-secondary">Summary</h5>
                            <div class="text-dark" style="line-height: 1.7;">
                                {!! nl2br(e($course->summary)) !!}
                            </div>
                        </div>
                    @endif

                    <!-- Content -->
                    @if($course->content)
                        <div class="mb-4">
                            <h5 class="fw-semibold mb-2 text-secondary">Content</h5>
                            <div class="text-dark" style="line-height: 1.7;">
                                {!! nl2br(e($course->content)) !!}
                            </div>
                        </div>
                    @endif

                    <!-- Action Button -->
                    <div class="mt-4">
                        @if($course->is_moodle)
                            <a href="https://khub.africacdc.org/elearning/course/view.php?id={{ $course->moodle_id }}" target="_blank" class="btn theme-primary text-white px-4">
                                Go to Course on Moodle
                            </a>
                        @else
                            <a href="{{ $course->course_url }}" target="_blank" class="btn theme-secondary text-white px-4">
                                Go to Course
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
