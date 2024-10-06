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

                <div class="col-12 col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-duration="800"
                    data-aos-once="true">
                    <!-- Start Feature One -->
                    <div class="bg-white shadow-lg rounded-4 overflow-hidden">
                        <div class="p-4">
                            <a href="#"
                                class="font-size-3 d-block mb-1 text-gray">{{ truncate($row->author->name, 40) }}</a>
                            <h2 class="mt-n1">
                                <a class="font-size-2 text-black-2  mb-3 d-block"
                                    href="{{ url('records/resource') }}?id={{ $row->id }}">
                                    {!! truncate($row->title, 40) !!}
                                </a>
                            </h2>
                            {!! truncate($row->description, 100) ?? '' !!}
                            <ul class="list-unstyled mb-3 card-tag-list bg-light py-3 rounded-3">
                                <li class="d-inline-block">
                                    <a href="" class="text-warning font-size-3 rounded-3 px-3 py-1">
                                        <i class="fa fa-briefcase mr-2 font-weight-bold"></i>
                                        {{ $row->sub_theme->description ?? 'N/A' }}
                                    </a>
                                </li>
                                <li class="d-inline-block">
                                    <a href="" class=" text-success font-size-3 rounded-3 px-3 py-1">
                                        <i class="fa fa-dollar-sign mr-2 font-weight-bold"></i>
                                        {{ @$row->category->category_name }}
                                    </a>
                                </li>
                            </ul>
                            <p class="mb-4 font-size-4 text-gray">
                                Last updated: {{ time_ago($row->updated_at) }}
                                <br>{{ $row->visits }} Views
                                <br>{{ count($row->comments) }} Comments
                            </p>
                            <div class="card-btn-group">
                                <a class="btn btn-primary text-uppercase btn-medium rounded-3 col-lg-12"
                                    href="{{ url('records/resource') }}?id={{ $row->id }}">Browse Resource</a>
                            </div>
                        </div>
                    </div>
                    <!-- End Feature One -->
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
