@extends(admin_layout())
@section('content')

<div class="page-header">
    <h1 class="page-title">{{ __('admin_nav.search_history') }}</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.configure') }}">{{ __('admin_nav.settings') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('admin_nav.search_history') }}</li>
        </ol>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header fw-bold">Top search phrases</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead><tr><th>Phrase</th><th class="text-end">Count</th></tr></thead>
                        <tbody>
                            @forelse($topPhrases as $row)
                                <tr>
                                    <td>{{ $row->phrase }}</td>
                                    <td class="text-end">{{ number_format((int) $row->search_count) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-muted px-3 py-2">No data yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header fw-bold">Highly searched keywords <span class="text-muted small fw-normal">(from last 5,000 logged searches, tokenized)</span></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead><tr><th>Keyword</th><th class="text-end">Count</th></tr></thead>
                        <tbody>
                            @forelse($topTokens as $word => $cnt)
                                <tr>
                                    <td><span class="badge bg-light text-dark border">{{ $word }}</span></td>
                                    <td class="text-end">{{ number_format($cnt) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-muted px-3 py-2">No data yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row bg-white py-4 rounded">
    <div class="col-md-12">
        <form method="get" class="row g-2 align-items-end mb-3" action="{{ route('admin.search-logs.index') }}">
            <div class="col-md-4">
                <label class="form-label small mb-0">Search term contains</label>
                <input type="text" name="term" value="{{ request('term') }}" class="form-control" placeholder="Filter by text in query">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-0">User</label>
                <select name="user_id" class="form-select">
                    <option value="">All users</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ (string) request('user_id') === (string) $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-0">Per page</label>
                <select name="rows" class="form-select">
                    @foreach([25, 50, 100, 200] as $r)
                        <option value="{{ $r }}" {{ (int) request('rows', 50) === $r ? 'selected' : '' }}>{{ $r }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Apply</button>
                <a href="{{ route('admin.search-logs.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>

        @if($logs->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead>
                        <tr class="text-bold">
                            <th style="width:4%">#</th>
                            <th style="width:12%">When</th>
                            <th style="width:14%">User</th>
                            <th>Search term</th>
                            <th style="width:8%" class="text-end">Results</th>
                            <th style="width:16%">Keyword stats</th>
                            <th style="width:10%" class="small">IP</th>
                            <th style="width:10%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $idx => $log)
                            @php
                                $tokens = \App\Models\SearchLog::tokenizeForAnalysis($log->term);
                                $runUrl = url('/records/search').'?'.http_build_query(['term' => $log->term], '', '&', PHP_QUERY_RFC3986);
                            @endphp
                            <tr>
                                <td>{{ $logs->firstItem() + $idx }}</td>
                                <td>{{ $log->created_at?->format('Y-m-d H:i') }}</td>
                                <td>
                                    @if($log->user)
                                        {{ $log->user->name }}<br><span class="small text-muted">#{{ $log->user_id }}</span>
                                    @else
                                        <span class="text-muted">Guest</span>
                                    @endif
                                </td>
                                <td><code class="small">{{ Str::limit($log->term, 120) }}</code></td>
                                <td class="text-end">{{ $log->results_count !== null ? number_format($log->results_count) : '—' }}</td>
                                <td>
                                    @foreach(array_slice($tokens, 0, 6) as $w)
                                        <span class="badge bg-secondary me-1 mb-1">{{ $w }}</span>
                                    @endforeach
                                    @if(count($tokens) > 6)
                                        <span class="badge bg-light text-muted border">+{{ count($tokens) - 6 }}</span>
                                    @endif
                                    @if(count($tokens) === 0)
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ $log->ip_address ?? '—' }}</td>
                                <td>
                                    <a href="{{ $runUrl }}" target="_blank" rel="noopener" class="btn btn-sm btn-primary mb-1 d-block">Run search</a>
                                    <button type="button" class="btn btn-sm btn-outline-secondary w-100 btn-open-keywords"
                                        data-term="{{ e($log->term) }}"
                                        data-keywords='@json($tokens)'>Split keywords</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $logs->appends(request()->all())->links() }}
        @else
            <p class="text-muted">No search logs found.</p>
        @endif
    </div>
</div>

<div class="modal fade" id="searchLogKeywordsModal" tabindex="-1" aria-labelledby="searchLogKeywordsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="searchLogKeywordsModalLabel">Keywords for decision-making</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted mb-2">Original query:</p>
                <p class="fw-medium" id="searchLogKeywordsOriginal"></p>
                <p class="small text-muted mb-2">Tokenized list (unique, min. 2 characters):</p>
                <ul class="list-group mb-3" id="searchLogKeywordsList"></ul>
                <p class="small text-muted mb-1">Comma-separated (for spreadsheets / analysis):</p>
                <pre class="bg-light p-2 rounded small" id="searchLogKeywordsCsv" style="white-space: pre-wrap;"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-primary" id="searchLogKeywordsCopy"><i class="fa fa-copy"></i> Copy CSV</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
(function () {
    var modalEl = document.getElementById('searchLogKeywordsModal');
    if (!modalEl || typeof bootstrap === 'undefined') return;
    var modal = new bootstrap.Modal(modalEl);
    var listEl = document.getElementById('searchLogKeywordsList');
    var origEl = document.getElementById('searchLogKeywordsOriginal');
    var csvEl = document.getElementById('searchLogKeywordsCsv');
    document.querySelectorAll('.btn-open-keywords').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var term = btn.getAttribute('data-term') || '';
            var keywords = [];
            try {
                keywords = JSON.parse(btn.getAttribute('data-keywords') || '[]');
            } catch (e) {
                keywords = [];
            }
            origEl.textContent = term;
            listEl.innerHTML = '';
            keywords.forEach(function (k) {
                var li = document.createElement('li');
                li.className = 'list-group-item';
                li.textContent = k;
                listEl.appendChild(li);
            });
            if (keywords.length === 0) {
                var li = document.createElement('li');
                li.className = 'list-group-item text-muted';
                li.textContent = 'No tokens extracted.';
                listEl.appendChild(li);
            }
            csvEl.textContent = keywords.join(', ');
            modal.show();
        });
    });
    document.getElementById('searchLogKeywordsCopy').addEventListener('click', function () {
        var t = csvEl.textContent || '';
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(t).then(function () {
                var b = this;
                var old = b.innerHTML;
                b.innerHTML = '<i class="fa fa-check"></i> Copied';
                setTimeout(function () { b.innerHTML = old; }, 1500);
            }.bind(this)).catch(function () { alert('Copy failed.'); });
        } else {
            alert(t);
        }
    });
})();
</script>
@endsection
