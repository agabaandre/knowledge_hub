
@extends('layouts.plain')

@section('styles')


@endsection
@section('content')      	
<!-- ======================= Countries ======================== -->
<section class="space gray">
    
    <div class="container">

    @php
        $eng = $engagement_stats ?? [];
        $hubPubs = (int) ($eng['publications_count'] ?? 0);
        $hubUsers = (int) ($eng['enrolled_users_count'] ?? 0);
        $hubForums = (int) ($eng['forum_discussions_count'] ?? 0);
    @endphp

    @if(count($publications)==0 && count($kpis)== 0 && $hubPubs === 0 && $hubUsers === 0 && $hubForums === 0)

    <div class="row justify-content-center mb-2">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-2">
            <div class="sec_title position-relative text-center mb-5">
                <h2 class="ft-bold text-muted">No data available for selected member state</h2>
            </div>
        </div>
    </div>

    @endif

    @if($hubPubs > 0 || $hubUsers > 0 || $hubForums > 0)
    <div class="row justify-content-center mb-2">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-2">
            <div class="sec_title position-relative text-center mb-5">
                <h2 class="ft-bold">Knowledge Hub activity</h2>
                <p class="text-muted mb-0">Totals for this member state on the Knowledge Hub (resources, enrolled users, and forum threads started by users from this country).</p>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="crp_box fl_color ovr_top">
                <div class="row align-items-center">
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mt-2">
                        <div class="dro_140">
                            <div class="dro_141 de">
                                <img src="{{ asset('assets/img/common/stats.png')}}" style="max-width:35px;" alt=""/>
                            </div>
                            <div class="dro_142">
                                <h6 style="font-size: 11pt!important;">Publications linked to this country</h6>
                                <p class="color-red text-bold">{{ number_format($hubPubs) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mt-2">
                        <div class="dro_140">
                            <div class="dro_141 de">
                                <img src="{{ asset('assets/img/common/stats.png')}}" style="max-width:35px;" alt=""/>
                            </div>
                            <div class="dro_142">
                                <h6 style="font-size: 11pt!important;">Users enrolled (this country)</h6>
                                <p class="color-red text-bold">{{ number_format($hubUsers) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mt-2">
                        <div class="dro_140">
                            <div class="dro_141 de">
                                <img src="{{ asset('assets/img/common/stats.png')}}" style="max-width:35px;" alt=""/>
                            </div>
                            <div class="dro_142">
                                <h6 style="font-size: 11pt!important;">Forum discussions (started here)</h6>
                                <p class="color-red text-bold">{{ number_format($hubForums) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(!empty($kpi_groups))
    <div class="row justify-content-center mb-2">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-2">
            <div class="sec_title position-relative text-center mb-4">
                <h2 class="ft-bold">Country indicators</h2>
                <p class="text-muted mb-0">Latest published indicators for {{ $country->name }}, matched using ISO country codes.</p>
                @include('common.owid_attribution')
            </div>
        </div>

        @foreach($kpi_groups as $group)
        <div class="col-lg-12 mb-4">
            <h4 class="ft-bold mb-3">{{ $group['subject_area_name'] }}</h4>
            <div class="row">
                @foreach($group['items'] as $kpi)
                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 mb-3">
                    <div class="crp_box fl_color ovr_top h-100 p-3">
                        <h6 class="mb-1">{{ $kpi->kpi_name }}</h6>
                        <p class="color-red text-bold mb-1" style="font-size: 1.35rem;">
                            {{ number_format((float) $kpi->kpi_value, 2) }}
                            @if(!empty($kpi->unit_label))
                                <span class="text-muted" style="font-size: 0.85rem;">{{ $kpi->unit_label }}</span>
                            @endif
                        </p>
                        <small class="text-muted d-block mb-2">Latest period: {{ substr($kpi->period ?? '', 0, 4) }}</small>
                        @if(!empty($kpi->narration))
                            <p class="mb-2" style="font-size: 0.95rem;">{{ $kpi->narration }}</p>
                        @endif
                        @include('common.owid_attribution', ['chart' => $kpi, 'compact' => true])
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    @elseif(count($kpis)>0)
    {{-- Legacy flat layout fallback --}}
    <div class="row justify-content-center mb-2">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-2">
            <div class="sec_title position-relative text-center mb-5">
                <h2 class="ft-bold">Country Statistics</h2>
                @include('common.owid_attribution')
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="crp_box fl_color ovr_top">
                <div class="row align-items-center">
                @foreach($kpis as $kpi)
                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 mt-2">
                        <div class="dro_140">
                            <div class="dro_141 de">
                                <img src="{{ asset('assets/img/common/stats.png')}}" style="max-width:35px;"/>
                            </div>
                            <div class="dro_142">
                                <h6 style="font-size: 11pt!important;">{{$kpi->kpi_name}}</h6>
                                <p class="color-red text-bold">{{ number_format($kpi->kpi_value,2)}}</p>
                                @include('common.owid_attribution', ['chart' => $kpi, 'compact' => true])
                            </div>
                        </div>
                    </div>
                @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(count($publications)>0)
    
        <div class="row justify-content-center" data-aos="slide-down">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                <div class="sec_title position-relative text-center mb-5 mt-3">
                    <h2 class="ft-bold">Published Resources</h2>
                    <h4 class="ft-bold text-muted">Member State: {{$country->name}}</h4>
                </div>
            </div>
        </div>
      
        <div class="container">
            @include('publications.partials.publications')
        </div>
    @endif
        
    </div>
</section>
<!-- ======================= Countries ======================== -->
@endsection
@section('scripts')
@endsection
