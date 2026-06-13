@foreach ($courses as $row)
    <div class="col-md-6 col-lg-4 d-flex mb-4" data-aos="fade-up">
        <div class="course-card w-100">
            <img src="{{ $row->cover_image }}" alt="Course Image" class="course-image">

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
                    @if (!empty($row->rating) && (float) $row->rating > 0)
                        <div class="course-rating">
                            <div class="course-rating-stars">
                                @php
                                    $ratingValue = (float) $row->rating;
                                    $fullStars = floor($ratingValue);
                                    $hasHalfStar = ($ratingValue - $fullStars) >= 0.5;
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
                            <span class="course-rating-value">{{ number_format($ratingValue, 1) }}</span>
                            <span class="course-rating-count">({{ number_format($ratingValue * 20) }}+ reviews)</span>
                        </div>
                    @endif
                    @if ($row->summary)
                        <p class="course-summary">
                            {!! truncate($row->summary, 180) !!}
                        </p>
                    @endif
                </div>

                <div class="course-button mt-2">
                    @if ($row->isExternalCourse() && $row->external_course_url)
                        <a href="{{ $row->external_course_url }}" target="_blank" rel="noopener noreferrer" class="btn theme-primary btn-sm">
                            Open on eLearning Platform
                        </a>
                    @elseif ($row->content)
                        <a href="{{ url('courses/details/'.$row->id) }}" class="btn theme-secondary btn-sm">
                            Details
                        </a>
                    @elseif ($row->course_url)
                        <a href="{{ $row->course_url }}" target="_blank" rel="noopener noreferrer" class="btn theme-primary btn-sm">
                            View Course
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endforeach
