@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title mb-3">{{ $course->fullname }}</h2>
                    <div class="mb-2 text-muted">
                        <strong>Status:</strong> {{ $course->is_active ? 'Active' : 'Inactive' }}
                        @if($course->provider)
                            | <strong>Provider:</strong> {{ $course->provider }}
                        @endif
                    </div>

                    
                    @if($course->cover_image)
                        <div class="text-center mb-4">
                            <img src="{{$course->cover_image }}" 
                                 alt="{{ $course->fullname }}" 
                                 class="img-fluid rounded" 
                                 style="max-height: 300px; max-width: 100%;">
                        </div>
                    @endif
                    <div class="mb-3">
                        <strong>Summary:</strong>
                        <div>{!! nl2br($course->summary) !!}</div>
                    </div>
                    @if($course->content)
                        <div class="mb-3">
                            <strong>Content:</strong>
                            <div>{!! nl2br($course->content) !!}</div>
                        </div>
                    @endif
                    <div class="mt-4">
                        @if($course->is_moodle)
                            <a href="https://khub.africacdc.org/elearning/course/view.php?id={{$course->moodle_id}}" target="_blank" class="btn btn-primary">
                                Go to Course on Moodle
                            </a>
                        @else
                            <a href="{{ $course->course_url }}" target="_blank" class="btn btn-dark">
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