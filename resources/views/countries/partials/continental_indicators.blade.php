@if(!empty($continental_indicators))
<div class="continental-indicators mt-4" id="indicatorSummariesPanel">
    <h3 class="continental-indicators__title" id="indicatorSummariesTitle">
        <i class="fa fa-chart-bar me-2"></i><span id="indicatorSummariesTitleText">Continental indicators</span>
    </h3>
    <p class="continental-indicators__subtitle text-muted" id="indicatorSummariesSubtitle">
        Africa-wide averages or totals from published indicators. Click any card to explore it on the map.
    </p>
    <div class="continental-indicators__list" id="indicatorSummariesList">
        @foreach($continental_indicators as $indicator)
            @include('countries.partials.indicator_summary_card', ['indicator' => $indicator])
        @endforeach
    </div>
    @include('common.owid_attribution', ['compact' => true])
</div>
@endif
