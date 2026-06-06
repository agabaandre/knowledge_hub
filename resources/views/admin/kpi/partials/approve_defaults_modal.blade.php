@php
    $recommended = kpi_recommended_defaults_list();
    $publishableCount = collect($recommended)->filter(fn ($item) => $item['discovered'] && $item['status'] !== 'published')->count();
@endphp
<div class="modal fade" id="approveDefaultsModal" tabindex="-1" role="dialog" aria-labelledby="approveDefaultsModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <form method="POST" action="{{ url('admin/kpi/owid/approve-defaults') }}" id="approveDefaultsForm" class="modal-content js-kpi-queued-task">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="approveDefaultsModalTitle">Publish recommended indicators</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">
                    Review the curated default set from Our World in Data. Uncheck any indicators you do not want to publish on member state pages.
                    Only discovered indicators can be published; run <strong>Fetch new indicators</strong> first if any are missing.
                </p>

                <div class="d-flex flex-wrap align-items-center mb-3" style="gap: 8px;">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="approveDefaultsSelectPublishable">
                        Select publishable ({{ $publishableCount }})
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="approveDefaultsSelectAll">Select all discovered</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="approveDefaultsSelectNone">Clear selection</button>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 2.5rem;"></th>
                                <th>Indicator</th>
                                <th>Subject area</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recommended as $item)
                            @php
                                $canPublish = $item['discovered'] && $item['status'] !== 'published';
                                $isPublished = $item['status'] === 'published';
                                $isMissing = ! $item['discovered'];
                            @endphp
                            <tr class="{{ $isMissing ? 'table-secondary' : '' }}">
                                <td class="text-center align-middle">
                                    <input
                                        type="checkbox"
                                        class="approve-defaults-slug"
                                        name="slugs[]"
                                        value="{{ $item['slug'] }}"
                                        {{ $canPublish ? 'checked' : '' }}
                                        {{ ($isMissing || $isPublished) ? 'disabled' : '' }}
                                        data-publishable="{{ $canPublish ? '1' : '0' }}"
                                        data-discovered="{{ $item['discovered'] ? '1' : '0' }}"
                                    >
                                </td>
                                <td>
                                    <strong>{{ $item['name'] }}</strong>
                                    <div class="small text-muted"><code>{{ $item['slug'] }}</code></div>
                                    @if($item['url'])
                                        <a href="{{ $item['url'] }}" target="_blank" rel="noopener noreferrer" class="small">View on OWID</a>
                                    @endif
                                </td>
                                <td class="align-middle">{{ $item['subject_area'] ?? '—' }}</td>
                                <td class="align-middle">
                                    @if($isMissing)
                                        <span class="badge badge-secondary">Not discovered</span>
                                    @elseif($isPublished)
                                        <span class="badge badge-success">Already published</span>
                                    @elseif($item['status'] === 'recalled')
                                        <span class="badge badge-warning">Recalled</span>
                                    @else
                                        <span class="badge badge-info">Draft</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-muted text-center">No recommended indicators configured.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="form-check mt-3">
                    <input type="checkbox" class="form-check-input" name="narrations" id="approve_defaults_modal_narrations" value="1" checked>
                    <label class="form-check-label" for="approve_defaults_modal_narrations">Also queue AI country summaries for published indicators</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-info" id="approveDefaultsSubmitBtn" {{ $publishableCount === 0 ? 'disabled' : '' }}>
                    Publish selected
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    var form = document.getElementById('approveDefaultsForm');
    if (!form) return;

    function boxes() {
        return Array.prototype.slice.call(form.querySelectorAll('.approve-defaults-slug'));
    }

    function enabledBoxes() {
        return boxes().filter(function (el) { return !el.disabled; });
    }

    function selectedCount() {
        return enabledBoxes().filter(function (el) { return el.checked; }).length;
    }

    function updateSubmitState() {
        var btn = document.getElementById('approveDefaultsSubmitBtn');
        if (btn) {
            btn.disabled = selectedCount() === 0;
            btn.textContent = selectedCount() > 0
                ? 'Publish selected (' + selectedCount() + ')'
                : 'Publish selected';
        }
    }

    function setChecked(predicate) {
        enabledBoxes().forEach(function (el) {
            el.checked = predicate(el);
        });
        updateSubmitState();
    }

    document.getElementById('approveDefaultsSelectPublishable')?.addEventListener('click', function () {
        setChecked(function (el) { return el.getAttribute('data-publishable') === '1'; });
    });
    document.getElementById('approveDefaultsSelectAll')?.addEventListener('click', function () {
        setChecked(function (el) { return el.getAttribute('data-discovered') === '1'; });
    });
    document.getElementById('approveDefaultsSelectNone')?.addEventListener('click', function () {
        setChecked(function () { return false; });
    });

    boxes().forEach(function (el) {
        el.addEventListener('change', updateSubmitState);
    });

    form.addEventListener('submit', function (event) {
        if (selectedCount() === 0) {
            event.preventDefault();
            event.stopImmediatePropagation();
            alert('Select at least one indicator to publish.');
            return false;
        }
    }, true);

    updateSubmitState();
})();
</script>
