@extends('layouts.app')

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
</style>
@endsection

@section('content')
<!-- ======================= Banner ======================== -->
<div class="bg-light rounded py-5" style="background-image: url({{ asset('frontend/img/dots.png') }}); background-repeat:repeat-x; background-size:contain;">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 ft-medium fs-lg">
                        Knowledge Hub Courses
                    </h4>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ======================= Course Cards ======================== -->
<div class="container middle">
    <div class="row justify-content-center mt-3">
        @foreach ($courses as $row)
            <div class="col-md-6 col-lg-4 d-flex mb-4" data-aos="fade-up">
                <div class="course-card w-100">
                    <img src="{{ $row->cover_image ?? asset('frontend/img/default-course.jpg') }}" alt="Course Image" class="course-image">

                    <div class="course-body">
                        <div>
                            <h4 class="course-title">
                                {{ truncate($row->fullname, 60) }}
                            </h4>
                            <div class="course-meta mb-2">
                                <strong>Status:</strong> {{ $row->is_active ? 'Active' : 'Inactive' }}
                                @if ($row->provider)
                                    | <strong>Provider:</strong> {{ $row->provider }}
                                @endif
                            </div>
                            @if ($row->summary)
                                <p class="course-summary">
                                    {!! truncate($row->summary, 180) !!}
                                </p>
                            @endif
                        </div>

                        <div class="course-button mt-2">
                            @if($row->is_moodle)
                                <a href="https://khub.africacdc.org/elearning/course/view.php?id={{$row->moodle_id}}" target="_blank" class="btn theme-primary btn-sm">
                                    View on Moodle
                                </a>
                            @elseif($row->content)
                                <a href="{{ url('courses/details/'.$row->id) }}" class="btn theme-secondary btn-sm">
                                    Details
                                </a>
                            @else
                                <a href="{{ $row->course_url }}" target="_blank" class="btn theme-primary btn-sm">
                                    View Course
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="row justify-content-center mt-3">
        {{ $courses->links() }}
    </div>
</div>
@endsection
