<section class="middle gray" style="background-color: #f5f8fb;">
    <style>
        /* Fix broken cards on theme1 */
        .task-listing {
            display: flex;
            align-items: stretch;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .task-listing:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }
        
        .task-listing-bid {
            min-width: 240px;
            width: 240px;
            background-size: contain;
            background-position: center;
            background-repeat: no-repeat;
            background-color: transparent;
            flex-shrink: 0;
            padding: 4px;
        }
        
        .task-listing-details {
            flex: 1;
            padding: 16px;
        }
        
        .task-listing-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: #2d3748;
        }
        
        .task-listing-text {
            color: #4a5568;
            margin-top: 10px;
        }
        
        .task-tags {
            margin-top: 15px;
        }
        
        .task-tags span {
            display: inline-block;
            padding: 4px 12px;
            background: #e2e8f0;
            border-radius: 4px;
            margin-right: 8px;
            font-size: 0.875rem;
            color: #4a5568;
        }
        
        .task-icons {
            list-style: none;
            padding: 0;
            margin: 10px 0;
        }
        
        .task-icons li {
            display: inline-block;
            margin-right: 15px;
            color: #718096;
            font-size: 0.9rem;
        }
        
        @media (max-width: 767px) {
            .task-listing {
                flex-direction: row;
                flex-wrap: wrap;
            }
            
            .task-listing-bid {
                width: 120px !important;
                height: 120px !important;
                min-width: 120px !important;
                float: left !important;
                margin-right: 12px !important;
                margin-bottom: 8px !important;
                margin-left: 2px !important;
                margin-top: 2px !important;
                padding: 4px !important;
                background-size: contain !important;
            }
            
            .task-listing-details {
                width: 100% !important;
                overflow: hidden !important;
                text-align: justify !important;
            }
            
            /* Clear float after content on mobile */
            .task-listing::after {
                content: "";
                display: table;
                clear: both;
            }
        }
    </style>
    <div class="container py-5">
        <div class="row justify-content-center" data-aos="fade-in">
            <div class="col-12">
                <div class="sec_title position-relative text-center mb-5">
                    <h2 class="ft-bold">Top Searches</h2>
                </div>
            </div>
        </div>
        <div class="row" id="top_searches">

            <div class="listings-container margin-top-35" style="width: 100%;">

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
                            style="background-image: url('{{ $row->cover }}'); background-size: contain; background-repeat: no-repeat; background-position: center; background-color: transparent; min-width: 240px; width: 240px;">
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
                                    <strong>{{ $row->theme->description ?? '' }}</strong>
                                    <span class="text-sm">{{ $row->sub_theme->description ?? '' }}</span>
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
