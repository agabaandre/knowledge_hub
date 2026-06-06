<div class="modal fade country-kpi-modal" id="countryKpiModal" tabindex="-1" role="dialog" aria-labelledby="countryKpiModalLabel" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content country-kpi-modal__content">
            <div class="modal-header country-kpi-modal__header">
                <div>
                    <p class="country-kpi-modal__eyebrow mb-1" id="countryKpiModalSubject"></p>
                    <h5 class="modal-title ft-bold mb-0" id="countryKpiModalLabel"></h5>
                </div>
                <button type="button" class="btn-close btn-close-white country-kpi-modal__close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body country-kpi-modal__body">
                <div class="country-kpi-modal__hero">
                    <div>
                        <div class="country-kpi-modal__value" id="countryKpiModalValue"></div>
                        <div class="country-kpi-modal__denomination text-muted" id="countryKpiModalDenomination"></div>
                    </div>
                    <div class="country-kpi-modal__period text-muted" id="countryKpiModalPeriod"></div>
                </div>
                <div id="countryKpiModalChartWrap" class="country-kpi-modal__chart-wrap" style="display:none;">
                    <div id="countryKpiModalChart" style="width:100%;height:280px;"></div>
                </div>
                <div id="countryKpiModalNarration" class="country-kpi-modal__narration" style="display:none;"></div>
                <div id="countryKpiModalAttribution" class="country-kpi-modal__attribution"></div>
            </div>
            <div class="modal-footer country-kpi-modal__footer">
                <a href="#" id="countryKpiModalOwidLink" class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener noreferrer" style="display:none;">
                    <i class="fa fa-chart-line mr-1"></i> View on Our World in Data
                </a>
                <button type="button" class="btn btn-secondary btn-sm country-kpi-modal__close" data-bs-dismiss="modal" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
