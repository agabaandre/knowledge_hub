@extends(admin_layout())

@section('styles')
<style>
    .appr-hero { background: linear-gradient(135deg, var(--theme-color-primary, #119A48) 0%, #0d7a38 100%); color: #fff; border-radius: 12px; padding: 1.35rem 1.6rem; margin-bottom: 1.25rem; }
    .appr-hero h1 { color: #fff; font-size: 1.4rem; margin: 0 0 .25rem; }
    .appr-hero p { margin: 0; opacity: .92; }
    .appr-card { border: 1px solid #e2e8f0; border-radius: 10px; background: #fff; text-decoration: none; color: inherit; display: block; padding: .9rem 1rem; height: 100%; }
    .appr-card:hover { border-color: var(--theme-color-primary, #119A48); box-shadow: 0 4px 14px rgba(15,23,42,.08); }
    .appr-card.active { border-color: var(--theme-color-primary, #119A48); box-shadow: inset 0 0 0 1px var(--theme-color-primary, #119A48); }
    .appr-card .label { font-size: .72rem; text-transform: uppercase; letter-spacing: .04em; color: #64748b; }
    .appr-card .value { font-size: 1.55rem; font-weight: 700; line-height: 1.2; }
    .appr-type { font-size: .72rem; font-weight: 600; text-transform: uppercase; letter-spacing: .03em; }
    .appr-empty { padding: 2.5rem 1rem; text-align: center; color: #64748b; }
</style>
@endsection

@section('content')
<div class="appr-hero">
    <h1>Pending approvals</h1>
    <p>Review only items waiting for a decision. Approved and rejected records stay on their original admin pages.</p>
</div>

@if(session('alert-success'))
    <div class="alert alert-success">{{ session('alert-success') }}</div>
@endif
@if(session('alert-danger'))
    <div class="alert alert-danger">{{ session('alert-danger') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="row g-2 mb-3">
    @php
        $tabs = [
            'all' => ['All pending', $counts['all'] ?? 0],
            'publication' => ['Publications', $counts['publication'] ?? 0],
            'forum' => ['Forums', $counts['forum'] ?? 0],
            'cop_participant' => ['CoP participants', $counts['cop_participant'] ?? 0],
            'federated' => ['Federated content', $counts['federated'] ?? 0],
        ];
    @endphp
    @foreach($tabs as $key => $tab)
        <div class="col-6 col-md">
            <a class="appr-card {{ $currentType === $key ? 'active' : '' }}" href="{{ route('admin.approvals.index', array_filter(['type' => $key === 'all' ? null : $key, 'q' => $q ?: null])) }}">
                <div class="label">{{ $tab[0] }}</div>
                <div class="value">{{ $tab[1] }}</div>
            </a>
        </div>
    @endforeach
</div>

<div class="card">
    <div class="card-body">
        <form method="get" class="row g-2 mb-3">
            @if($currentType !== 'all')
                <input type="hidden" name="type" value="{{ $currentType }}">
            @endif
            <div class="col-md-6">
                <input type="search" name="q" value="{{ $q }}" class="form-control" placeholder="Search title, submitter, or source">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-secondary w-100">Search</button>
            </div>
        </form>

        @if($items->isEmpty())
            <div class="appr-empty">
                <i class="fa fa-check-circle fa-2x mb-2 text-success"></i>
                <div>No items are waiting for approval{{ $currentType !== 'all' ? ' in this queue' : '' }}.</div>
            </div>
        @else
            <form method="post" action="{{ route('admin.approvals.review') }}" id="approvalsForm">
                @csrf
                <input type="hidden" name="action" id="approvalsAction" value="approve">
                <input type="hidden" name="filter_type" value="{{ $currentType }}">
                @if($q !== '')
                    <input type="hidden" name="q" value="{{ $q }}">
                @endif
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <button type="submit" class="btn btn-success btn-sm" onclick="document.getElementById('approvalsAction').value='approve';">Approve selected</button>
                    <button type="button" class="btn btn-outline-danger btn-sm" id="approvalsRejectSelectedBtn">Reject selected</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th style="width:36px;"><input type="checkbox" id="approvalsSelectAll"></th>
                                <th>Type</th>
                                <th>Item</th>
                                <th>Submitted by</th>
                                <th>Submitted</th>
                                <th class="text-end">Decision</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td><input type="checkbox" name="keys[]" value="{{ $item['key'] }}" class="approval-item-cb"></td>
                                    <td><span class="badge bg-light text-dark appr-type">{{ $item['type_label'] }}</span></td>
                                    <td>
                                        <a href="{{ $item['preview_url'] }}" class="fw-semibold">{{ $item['title'] }}</a>
                                        <div class="small text-muted">{{ $item['subtitle'] }}</div>
                                    </td>
                                    <td>{{ $item['submitted_by'] }}</td>
                                    <td class="small text-muted">{{ optional($item['submitted_at'])->diffForHumans() ?? '—' }}</td>
                                    <td class="text-end text-nowrap">
                                        <button type="submit" class="btn btn-success btn-sm py-0 px-2" name="action" value="approve" formaction="{{ route('admin.approvals.review') }}" form="approval-one-{{ $item['key'] }}">Approve</button>
                                        @if(in_array($item['type'], ['forum', 'publication'], true))
                                            <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2" onclick="approvalsRejectOne('{{ $item['type'] }}', {{ (int) $item['id'] }})">Reject</button>
                                        @else
                                            <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2" name="action" value="reject" formaction="{{ route('admin.approvals.review') }}" form="approval-one-{{ $item['key'] }}" onclick="return confirm('Reject this item?');">Reject</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
            @foreach($items as $item)
                <form method="post" action="{{ route('admin.approvals.review') }}" id="approval-one-{{ $item['key'] }}" class="d-none">
                    @csrf
                    <input type="hidden" name="type" value="{{ $item['type'] }}">
                    <input type="hidden" name="id" value="{{ $item['id'] }}">
                    <input type="hidden" name="filter_type" value="{{ $currentType }}">
                </form>
            @endforeach
            {{ $items->links() }}
        @endif
    </div>
</div>

<div class="modal fade" id="approvalsRejectModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" action="{{ route('admin.approvals.review') }}" class="modal-content">
            @csrf
            <input type="hidden" name="action" value="reject">
            <input type="hidden" name="filter_type" value="{{ $currentType }}">
            <input type="hidden" name="type" id="rejectType" value="">
            <input type="hidden" name="id" id="rejectId" value="">
            <div id="rejectKeys"></div>
            <div class="modal-header">
                <h5 class="modal-title">Reject item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label">Reason</label>
                <textarea name="rejected_reason" class="form-control" rows="4" minlength="10" required placeholder="Explain why this is being rejected (required for forums; recommended for publications)."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Reject</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function approvalsRejectOne(type, id) {
    document.getElementById('rejectType').value = type;
    document.getElementById('rejectId').value = id;
    document.getElementById('rejectKeys').innerHTML = '';
    var modal = document.getElementById('approvalsRejectModal');
    if (window.bootstrap && bootstrap.Modal) {
        bootstrap.Modal.getOrCreateInstance(modal).show();
    } else if (window.jQuery) {
        jQuery(modal).modal('show');
    }
}
(function () {
    var all = document.getElementById('approvalsSelectAll');
    if (all) {
        all.addEventListener('change', function () {
            document.querySelectorAll('.approval-item-cb').forEach(function (cb) { cb.checked = all.checked; });
        });
    }
    var rejectSelected = document.getElementById('approvalsRejectSelectedBtn');
    if (rejectSelected) {
        rejectSelected.addEventListener('click', function () {
            var checked = document.querySelectorAll('.approval-item-cb:checked');
            if (!checked.length) {
                alert('Select at least one item.');
                return;
            }
            document.getElementById('rejectType').value = '';
            document.getElementById('rejectId').value = '';
            var holder = document.getElementById('rejectKeys');
            holder.innerHTML = '';
            checked.forEach(function (cb) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'keys[]';
                input.value = cb.value;
                holder.appendChild(input);
            });
            var modal = document.getElementById('approvalsRejectModal');
            if (window.bootstrap && bootstrap.Modal) {
                bootstrap.Modal.getOrCreateInstance(modal).show();
            } else if (window.jQuery) {
                jQuery(modal).modal('show');
            }
        });
    }
})();
</script>
@endsection
