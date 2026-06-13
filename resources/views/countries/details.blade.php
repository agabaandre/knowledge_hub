
@extends('layouts.plain')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/plugins/highcharts/css/highcharts.css') }}"/>
<style>
    .country-kpi-section {
        margin-top: 0;
    }
    .country-kpi-section .crp_box.fl_color {
        margin-top: 0;
    }
    .country-kpi-subject {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(26, 86, 50, 0.08);
        padding: 1.25rem 1.25rem 0.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid rgba(26, 86, 50, 0.08);
    }
    .country-kpi-subject__head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid rgba(180, 162, 105, 0.35);
    }
    .country-kpi-subject__title {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 700;
        color: #1A5632;
    }
    .country-kpi-subject__count {
        font-size: 0.8rem;
        font-weight: 600;
        color: #58595B;
        background: #f4f7f5;
        border-radius: 999px;
        padding: 0.25rem 0.75rem;
        white-space: nowrap;
    }
    .country-kpi-subject .dro_140.country-kpi-tile {
        background: #ffffff;
        border-radius: 10px;
        padding: 1.5rem 1rem;
        box-shadow: 0 0 20px 0 rgb(62 28 131 / 10%);
        border: 1px solid transparent;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        align-items: center;
    }
    .country-kpi-tile .country-kpi-tile__body {
        flex: 1;
        min-width: 0;
        padding-right: 6px;
    }
    .country-kpi-tile__title {
        font-size: 11pt !important;
        line-height: 1.25 !important;
        margin: 0 0 5px !important;
        font-weight: 500 !important;
    }
    .country-kpi-tile__drill-badge {
        flex-shrink: 0;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(26, 86, 50, 0.1);
        color: #1A5632;
        font-size: 0.65rem;
        margin-left: 4px;
    }
    .country-kpi-tile__value {
        font-size: 14px;
        line-height: 1.6;
        margin: 0;
    }
    .country-kpi-tile__denomination {
        display: block;
        font-size: 0.72rem;
        line-height: 1.35;
        margin-top: 0.1rem;
        color: #64748b !important;
    }
    .country-kpi-tile__period {
        display: block;
        font-size: 0.72rem;
        line-height: 1.3;
        margin-top: 0.15rem;
    }
    .country-kpi-tile--drilldown {
        cursor: pointer;
    }
    .country-kpi-tile--drilldown:hover,
    .country-kpi-tile--drilldown:focus {
        outline: none;
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(26, 86, 50, 0.14);
        border-color: rgba(26, 86, 50, 0.2);
    }
    .country-kpi-tile--drilldown:focus-visible {
        box-shadow: 0 0 0 3px rgba(26, 86, 50, 0.25);
    }
    .country-kpi-modal__content {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.2);
    }
    .country-kpi-modal__header {
        background: linear-gradient(135deg, #1A5632 0%, #2d7a47 100%);
        color: #fff;
        border: none;
        padding: 1.25rem 1.5rem;
    }
    .country-kpi-modal__header .close,
    .country-kpi-modal__header .btn-close {
        opacity: 0.9;
        filter: brightness(0) invert(1);
    }
    .country-kpi-modal__eyebrow {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        opacity: 0.85;
        margin: 0;
    }
    .country-kpi-modal__header .modal-title {
        color: #fff;
        font-size: 1.25rem;
    }
    .country-kpi-modal__hero {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .country-kpi-modal__value {
        font-size: 2rem;
        font-weight: 700;
        color: #9F2241;
        line-height: 1.1;
    }
    .country-kpi-modal__denomination {
        font-size: 0.95rem;
        color: #475569;
        margin-top: 0.35rem;
        line-height: 1.45;
        max-width: 36rem;
    }
    .country-kpi-modal__chart-wrap {
        margin-bottom: 1rem;
        padding: 0.5rem;
        background: #f8fafc;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }
    .country-kpi-modal__narration {
        font-size: 0.95rem;
        line-height: 1.65;
        color: #334155;
        margin-bottom: 1rem;
        padding: 1rem;
        background: #fffbeb;
        border-left: 4px solid #B4A269;
        border-radius: 0 8px 8px 0;
    }
    .country-kpi-modal__attribution {
        font-size: 0.8rem;
        color: #64748b;
    }
    .country-kpi-modal__footer {
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
    }
</style>
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
    <div class="row justify-content-center mb-2 country-kpi-section">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-2">
            <div class="sec_title position-relative text-center mb-4 mt-4">
                <h2 class="ft-bold">Country indicators</h2>
                <p class="text-muted mb-0">Latest published indicators for {{ $country->name }}, matched using ISO country codes. Tap a card with <i class="fa fa-chevron-right" aria-hidden="true"></i> for trends, context, and source details.</p>
                @include('common.owid_attribution')
            </div>
        </div>

        @foreach($kpi_groups as $group)
        <div class="col-lg-12 mb-3">
            <div class="country-kpi-subject">
                <div class="country-kpi-subject__head">
                    <h4 class="country-kpi-subject__title">{{ $group['subject_area_name'] }}</h4>
                    <span class="country-kpi-subject__count">{{ count($group['items']) }} indicator{{ count($group['items']) === 1 ? '' : 's' }}</span>
                </div>
                <div class="crp_box fl_color" style="margin-top:0;padding:0.5rem 0.75rem 0.25rem;background:transparent;box-shadow:none;">
                    <div class="row align-items-center">
                        @foreach($group['items'] as $kpi)
                            @include('countries.partials.kpi_indicator_card', ['kpi' => $kpi])
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @elseif(count($kpis)>0)
    {{-- Legacy flat layout with same tile treatment --}}
    <div class="row justify-content-center mb-2 country-kpi-section">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-2">
            <div class="sec_title position-relative text-center mb-4 mt-4">
                <h2 class="ft-bold">Country Statistics</h2>
                @include('common.owid_attribution')
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="country-kpi-subject">
                <div class="crp_box fl_color" style="margin-top:0;padding:0.5rem 0.75rem 0.25rem;background:transparent;box-shadow:none;">
                    <div class="row align-items-center">
                        @foreach($kpis as $kpi)
                            @include('countries.partials.kpi_indicator_card', ['kpi' => $kpi])
                        @endforeach
                    </div>
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

@if((!empty($kpi_groups) || count($kpis ?? []) > 0) && !empty($kpi_chart_payload))
    @include('countries.partials.kpi_drilldown_modal')
@endif
<!-- ======================= Countries ======================== -->
@endsection
@section('scripts')
@if((!empty($kpi_groups) || count($kpis ?? []) > 0) && !empty($kpi_chart_payload))
<script src="{{ asset('assets/plugins/highcharts/highcharts.js') }}"></script>
<script>
(function () {
    var kpiPayload = @json($kpi_chart_payload ?? []);
    var auGreen = '{{ settings()->au_corporate_green ?? '#1A5632' }}';
    var auRed = '{{ settings()->au_red ?? '#9F2241' }}';
    var owidSite = @json(owid_site_url());
    var owidLicense = @json(owid_license_url());
    var modalChart = null;
    var kpiModalInstance = null;
    var suppressCardOpenUntil = 0;

    function getModalElement() {
        return document.getElementById('countryKpiModal');
    }

    function ensureModalOnBody() {
        var modalEl = getModalElement();
        if (modalEl && modalEl.parentElement !== document.body) {
            document.body.appendChild(modalEl);
        }
        return modalEl;
    }

    function getKpiModal() {
        var modalEl = ensureModalOnBody();
        if (!modalEl || typeof bootstrap === 'undefined' || !bootstrap.Modal) {
            return null;
        }
        if (!kpiModalInstance) {
            kpiModalInstance = bootstrap.Modal.getOrCreateInstance(modalEl, {
                backdrop: true,
                keyboard: true,
                focus: true
            });
        }
        return kpiModalInstance;
    }

    function closeKpiModal() {
        var modal = getKpiModal();
        if (modal) {
            modal.hide();
        } else if (typeof $ !== 'undefined') {
            $('#countryKpiModal').modal('hide');
        }
    }

    function cleanupModalArtifacts() {
        document.querySelectorAll('.modal-backdrop').forEach(function (backdrop) {
            backdrop.remove();
        });
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    }

    function buildAttributionHtml(owidUrl) {
        var explore = owidUrl || (owidSite + '/');
        return 'Data from <a href="' + owidSite + '/" target="_blank" rel="noopener noreferrer">Our World in Data</a> ' +
            '(<a href="' + owidLicense + '" target="_blank" rel="noopener noreferrer">CC BY 4.0</a>). ' +
            '<a href="' + explore + '" target="_blank" rel="noopener noreferrer">View chart details</a>.';
    }

    function openKpiModal(kpiId, subjectName) {
        if (Date.now() < suppressCardOpenUntil) {
            return;
        }

        var data = kpiPayload[kpiId];
        if (!data) return;

        document.getElementById('countryKpiModalSubject').textContent = subjectName || 'Country indicator';
        document.getElementById('countryKpiModalLabel').textContent = data.name || '';
        var valueLine = data.display_value || String(data.latest_value);
        if (data.value_type === 'percent' && valueLine.indexOf('%') === -1) {
            valueLine = valueLine + '%';
        }
        document.getElementById('countryKpiModalValue').textContent = valueLine;

        var denominationEl = document.getElementById('countryKpiModalDenomination');
        var denomination = data.unit_plain || '';
        if (denomination) {
            denominationEl.style.display = 'block';
            denominationEl.textContent = denomination;
        } else {
            denominationEl.style.display = 'none';
            denominationEl.textContent = '';
        }

        document.getElementById('countryKpiModalPeriod').textContent = data.latest_period
            ? 'Latest period: ' + data.latest_period
            : '';

        var chartWrap = document.getElementById('countryKpiModalChartWrap');
        if (data.has_chart && typeof Highcharts !== 'undefined') {
            chartWrap.style.display = 'block';
            if (modalChart) {
                modalChart.destroy();
                modalChart = null;
            }
            modalChart = Highcharts.chart('countryKpiModalChart', {
                chart: { type: 'areaspline', height: 280 },
                title: { text: null },
                colors: [auGreen],
                credits: {
                    enabled: true,
                    text: 'Data: Our World in Data (CC BY 4.0)',
                    href: data.owid_chart_url || (owidSite + '/'),
                    style: { fontSize: '11px', color: '#64748b' }
                },
                xAxis: {
                    categories: data.labels,
                    crosshair: true,
                    labels: { style: { color: '#64748b' } }
                },
                yAxis: {
                    title: { text: data.chart_unit || data.unit_full || data.unit || 'Value' },
                    gridLineColor: '#e2e8f0'
                },
                tooltip: {
                    shared: true,
                    valueDecimals: 2
                },
                plotOptions: {
                    areaspline: {
                        fillColor: {
                            linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                            stops: [
                                [0, Highcharts.color(auGreen).setOpacity(0.25).get('rgba')],
                                [1, Highcharts.color(auGreen).setOpacity(0.02).get('rgba')]
                            ]
                        },
                        marker: { radius: 3, fillColor: auRed }
                    }
                },
                series: [{
                    name: data.name,
                    data: data.values
                }]
            });
        } else {
            chartWrap.style.display = 'none';
            if (modalChart) {
                modalChart.destroy();
                modalChart = null;
            }
        }

        var narrationEl = document.getElementById('countryKpiModalNarration');
        if (data.narration) {
            narrationEl.style.display = 'block';
            narrationEl.textContent = data.narration;
        } else {
            narrationEl.style.display = 'none';
            narrationEl.textContent = '';
        }

        document.getElementById('countryKpiModalAttribution').innerHTML = buildAttributionHtml(data.owid_chart_url);

        var owidLink = document.getElementById('countryKpiModalOwidLink');
        if (data.owid_chart_url) {
            owidLink.href = data.owid_chart_url;
            owidLink.style.display = 'inline-flex';
        } else {
            owidLink.style.display = 'none';
        }

        var modal = getKpiModal();
        if (modal) {
            modal.show();
        } else if (typeof $ !== 'undefined') {
            $('#countryKpiModal').modal('show');
        }
    }

    function subjectForTrigger(trigger) {
        var subject = trigger.closest('.country-kpi-subject');
        if (!subject) return '';
        var title = subject.querySelector('.country-kpi-subject__title');
        return title ? title.textContent.trim() : '';
    }

    document.addEventListener('click', function (e) {
        if (e.target.closest('.modal') || e.target.closest('.modal-backdrop')) {
            return;
        }

        var modalEl = getModalElement();
        if (modalEl && modalEl.classList.contains('show')) {
            return;
        }

        if (Date.now() < suppressCardOpenUntil) {
            return;
        }

        var trigger = e.target.closest('.kpi-drilldown-trigger');
        if (!trigger) return;

        var kpiId = trigger.getAttribute('data-kpi-id');
        if (!kpiId) return;

        e.preventDefault();
        e.stopPropagation();
        openKpiModal(kpiId, subjectForTrigger(trigger));
    });

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Enter' && e.key !== ' ') return;
        var trigger = e.target.closest('.kpi-drilldown-trigger');
        if (!trigger) return;
        e.preventDefault();
        var kpiId = trigger.getAttribute('data-kpi-id');
        if (kpiId) {
            openKpiModal(kpiId, subjectForTrigger(trigger));
        }
    });

    function bindModalLifecycle() {
        var modalEl = ensureModalOnBody();
        if (!modalEl) return;

        modalEl.addEventListener('hide.bs.modal', function () {
            suppressCardOpenUntil = Date.now() + 400;
        });

        modalEl.addEventListener('hidden.bs.modal', function () {
            if (modalChart) {
                modalChart.destroy();
                modalChart = null;
            }
            cleanupModalArtifacts();
        });

        modalEl.querySelectorAll('.country-kpi-modal__close, [data-bs-dismiss="modal"]').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                closeKpiModal();
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindModalLifecycle);
    } else {
        bindModalLifecycle();
    }
})();
</script>
@endif
@if($countryPublicationsInfiniteScroll ?? false)
<script>
    window.publicationsListingInfiniteScrollConfig = {
        enabled: true,
        pageUrl: @json(route('countries.publications-page', ['slug' => $country->slug ?? $country->id]))
    };
    window.PUBLICATIONS_LISTING_INFINITE_COMPLETE = 'All publications loaded';
    window.PUBLICATIONS_LISTING_INFINITE_ERROR = 'Could not load more publications. Tap to retry.';
</script>
<script src="{{ asset('js/publications-listing-infinite.js') }}?v={{ @filemtime(public_path('js/publications-listing-infinite.js')) }}"></script>
@endif
@endsection
