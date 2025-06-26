@extends('layouts.app')

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
