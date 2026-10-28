@extends(admin_layout())

@section('content')
<div class="page-header mb-3">
    <div>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ url('admin') }}">{{ __('admin_nav.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.federation.index') }}">{{ __('admin_nav.federated_hubs_short') }}</a></li>
            <li class="breadcrumb-item active">{{ __('admin_nav.pending_federated_content') }}</li>
        </ol>
    </div>
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

<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">Review partner hub content</h3>
        <a href="{{ route('federation.browse') }}" class="btn btn-sm btn-outline-primary" target="_blank">View public catalogue</a>
    </div>
    <div class="card-body">
        <p class="text-muted small mb-3">
            Country hubs share content that is already approved locally. Items below must be approved on this central portal before they appear in search and the partner hub catalogue.
        </p>

        <form method="get" class="row g-2 mb-3">
            <div class="col-md-4">
                <select name="hub" class="form-control">
                    <option value="">All hubs</option>
                    @foreach($hubs as $hub)
                        <option value="{{ $hub->id }}" @selected((int) request('hub') === (int) $hub->id)>{{ $hub->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="type" class="form-control">
                    <option value="">All types</option>
                    <option value="publication" @selected(request('type') === 'publication')>Publications</option>
                    <option value="forum" @selected(request('type') === 'forum')>Forums</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-secondary w-100">{{ ui_t('general.filter', 'Filter') }}</button>
            </div>
        </form>

        @if($items->isEmpty())
            <div class="alert alert-info mb-0">No federated content is waiting for review.</div>
        @else
            <form method="post" action="{{ route('admin.federation.content-review') }}" id="fedReviewForm">
                @csrf
                <div class="d-flex gap-2 mb-3">
                    <button type="submit" name="action" value="approve" class="btn btn-success btn-sm" id="fedApproveBtn">{{ ui_t('general.approve_selected', 'Approve selected') }}</button>
                    <button type="submit" name="action" value="reject" class="btn btn-outline-danger btn-sm" id="fedRejectBtn">{{ ui_t('general.reject_selected', 'Reject selected') }}</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th style="width:36px;"><input type="checkbox" id="fedSelectAll"></th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Source hub</th>
                                <th>Updated</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                @php
                                    $payload = $item->payload ?? [];
                                    $hubBase = optional($item->hub)->normalizedBaseUrl() ?: '';
                                    $url = $item->content_type === 'forum'
                                        ? federated_forum_url($hubBase, $payload)
                                        : federated_publication_url($hubBase, $payload);
                                @endphp
                                <tr>
                                    <td><input type="checkbox" name="item_ids[]" value="{{ $item->id }}" class="fed-item-cb"></td>
                                    <td>
                                        <strong>{{ $item->title }}</strong>
                                        <div class="small text-muted">Remote ID {{ $item->remote_id }}</div>
                                    </td>
                                    <td><span class="badge bg-light text-dark">{{ ucfirst($item->content_type) }}</span></td>
                                    <td>{{ $item->hub->name ?? '—' }}</td>
                                    <td class="small text-muted">{{ optional($item->remote_updated_at)->diffForHumans() ?? '—' }}</td>
                                    <td>
                                        <a href="{{ $url }}" target="_blank" rel="noopener" class="btn btn-link btn-sm p-0">Preview on hub</a>
                                        <div class="mt-1 d-flex gap-2">
                                            <button type="submit" class="btn btn-success btn-sm py-0 px-2" form="fed-approve-{{ $item->id }}">{{ ui_t('general.approve', 'Approve') }}</button>
                                            <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2" form="fed-reject-{{ $item->id }}" onclick="return confirm(@json(ui_t('general.confirm_reject', 'Reject this item?')));">{{ ui_t('general.reject', 'Reject') }}</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
            @foreach($items as $item)
                <form method="post" action="{{ route('admin.federation.content-review') }}" id="fed-approve-{{ $item->id }}" class="d-none">
                    @csrf
                    <input type="hidden" name="action" value="approve">
                    <input type="hidden" name="item_ids[]" value="{{ $item->id }}">
                </form>
                <form method="post" action="{{ route('admin.federation.content-review') }}" id="fed-reject-{{ $item->id }}" class="d-none">
                    @csrf
                    <input type="hidden" name="action" value="reject">
                    <input type="hidden" name="item_ids[]" value="{{ $item->id }}">
                </form>
            @endforeach
            {{ $items->links() }}
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var all = document.getElementById('fedSelectAll');
    if (all) {
        all.addEventListener('change', function () {
            document.querySelectorAll('.fed-item-cb').forEach(function (cb) { cb.checked = all.checked; });
        });
    }
    var form = document.getElementById('fedReviewForm');
    if (!form) {
        return;
    }
    form.addEventListener('submit', function (e) {
        if (document.querySelectorAll('.fed-item-cb:checked').length) {
            var submitter = e.submitter || document.activeElement;
            if (submitter && submitter.value === 'reject' && !confirm('Reject selected items?')) {
                e.preventDefault();
            }
            return;
        }
        e.preventDefault();
        alert('Select at least one item.');
    });
})();
</script>
@endsection
