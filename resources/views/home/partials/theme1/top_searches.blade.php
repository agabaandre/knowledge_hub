<section class="middle gray" style="background-color: #2c3e50;">
    <div class="container py-5">
        <div class="row justify-content-center" data-aos="fade-in">
            <div class="col-12">
                <div class="sec_title position-relative text-center mb-5">
                    <h2 class="ft-bold" style="color: #ecf0f1;">Top Searches</h2>
                </div>
            </div>
        </div>
        <div class="row" id="top_searches">

            @php
                $i = 0;
            @endphp
            @foreach ($recent as $row)
                @php
                    $i++;
                    $likes = count($row->favourited);
                @endphp

                <div class="col-md-6 mb-4" data-aos="zoom-in">
                    <div class="card border-0 shadow-sm" style="border-radius: 10px; background-color: #ecf0f1;">
                        <div class="card-body d-flex flex-column">
                            <h4 class="card-title fs-md mb-2 ft-medium text-truncate" style="color: #2980b9;">
                                <a href="{{ url('records/resource') }}?id={{ $row->id }}"
                                    title="{!! $row->title !!}">
                                    {!! truncate($row->title, 40) !!}
                                </a>
                            </h4>
                            <p class="card-text text-muted small mb-2">
                                <i class="fa fa-bank mr-1"></i>Source: {{ truncate($row->author->name, 40) }}
                            </p>
                            <p class="card-text text-muted small mb-2">
                                <i class="lni lni-briefcase mr-1"></i>Theme: {!! truncate($row->theme->description ?? '', 40) !!}
                            </p>
                            <p class="card-text text-muted small mb-2">
                                <i class="lni lni-archive mr-1"></i>Sub Theme: {!! $row->sub_theme->description ?? '' !!}
                            </p>
                            <p class="card-text text-muted small mb-2">
                                <i class="lni lni-empty-file mr-1"></i>Category: {{ @$row->category->category_name }}
                            </p>
                            @if ($likes > 0)
                                <p class="card-text text-muted small mb-2">
                                    <i class="lni lni-heart theme-text mr-1"></i> {{ $likes }}
                                    Like{{ $likes > 1 ? 's' : '' }}
                                </p>
                            @endif
                            <div>
                                <p class="card-text text-muted small mb-2">
                                    <i class="lni lni-calendar mr-1"></i>Last updated: {{ time_ago($row->updated_at) }}
                                </p>
                                <p class="card-text text-muted small mb-2">
                                    <i class="fa fa-eye mr-1"></i>{{ $row->visits }} Views
                                </p>
                                <p class="card-text text-muted small mb-2">
                                    <i class="fa fa-comments"></i> {{ count($row->comments) }} Comments
                                </p>
                                @auth
                                    <div class="btn btn-outline-dark btn-sm mt-2 favbtn">
                                        @include ('common.favourites_btn')
                                    </div>
                                @endauth
                                <a href="{{ url('records/resource') }}?id={{ $row->id }}"
                                    class="btn btn-sm theme-bg text-white ft-medium apply-btn fs-sm rounded mt-3">Browse
                                    Resource</a>
                            </div>
                        </div>
                        @include('home.partials.comments')
                    </div>
                </div>
            @endforeach
        </div>
        <div class="row justify-content-center mt-4">
            <div class="col-12 text-center">
                <a id="explore" href="{{ url('records') }}"
                    class="btn btn-md theme-bg rounded text-light hover-theme">
                    Explore More Resources<i class="lni lni-arrow-right-circle ml-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>
