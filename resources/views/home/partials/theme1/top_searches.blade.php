<section class="middle gray" style="background-color: #f5f8fb;">
    <div class="container py-5">
        <div class="row justify-content-center" data-aos="fade-in">
            <div class="col-12">
                <div class="sec_title position-relative text-center mb-5">
                    <h2 class="ft-bold">Top Searches</h2>
                </div>
            </div>
        </div>
        <div class="row" id="top_searches">

            <div class="listings-container margin-top-35">

                @php
                    $i = 0;
                @endphp
                @foreach ($recent as $row)
                    @php
                        $i++;
                        $likes = count($row->favourited);
                    @endphp


                    <a href="{{ url('records/resource') }}?id={{ $row->id }}" class="task-listing">
                        <div class="task-listing-bid d-none d-sm-block"
                            style="background-image: url('{{ $row->cover }}'); background-size: cover;">
                        </div>
                        <div class="task-listing-details">

                            <!-- Details -->
                            <div class="task-listing-description">
                                <h3 class="task-listing-title">{!! truncate($row->title, 100) !!}</h3>
                                <ul class="task-icons">
                                    <li><i class="icon-material-outline-location-on"></i>{{ $row->author->name }}</li>
                                    <li><i class="icon-material-outline-access-time"></i>Last updated:
                                        {{ time_ago($row->updated_at) }}</li>
                                </ul>
                                <p class="task-listing-text">{!! truncate($row->description ?? 'N/A', 200) !!}</p>
                                <div class="task-tags">
                                    <span>{{ @$row->category->category_name }}</span>
                                    <span>{{ $row->visits }} Views </span>
                                    <span>{{ count($row->comments) }} Comments</span>
                                </div>
                            </div>

                        </div>

                        <div class="task-listing-bid">
                            <div class="task-listing-bid-inner row">
                                <div class="task-offers d-none d-sm-block">
                                    <strong>{{ $row->theme->description }}</strong>
                                    <span class="text-sm">{{ $row->sub_theme->description }}</span>
                                </div>
                                <span style="min-width:100%;" class="button button-sliding-icon ripple-effect"
                                    onclick="window.location.href={{ url('records/resource') }}?id={{ $row->id }}">
                                    Browse Resource
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach

            </div>
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
