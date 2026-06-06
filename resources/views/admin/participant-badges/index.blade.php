@extends(admin_layout())

@section('styles')
    @include('common.table')
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">Participant badge management</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin') }}">Admin</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0)">Publish</a></li>
                <li class="breadcrumb-item active" aria-current="page">Participant badges</li>
            </ol>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @php
        $defaultPeriod = $defaultPeriod ?? ['year' => now()->subMonth()->year, 'month' => now()->subMonth()->month];
        $queueHealth = $queueHealth ?? [];
        $lastAwardRun = $lastAwardRun ?? null;
        $awardJobRunning = $awardJobRunning ?? null;
        $awardJobProgress = $awardJobProgress ?? null;
        $badgeAudit = $badgeAudit ?? ['total' => 0, 'mismatches' => 0, 'rows' => []];
        $initialProgressPercent = (int) ($awardJobProgress['percent'] ?? 0);
        $initialProgressProcessed = (int) ($awardJobProgress['processed'] ?? 0);
        $initialProgressTotal = (int) ($awardJobProgress['total'] ?? 0);
    @endphp

    {{-- Automated award job + queue health + audit (collapsed) --}}
    <div class="card mb-4">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center"
             id="badgeAutomationHeading"
             data-toggle="collapse"
             data-target="#badgeAutomationPanel"
             aria-expanded="false"
             aria-controls="badgeAutomationPanel"
             style="cursor: pointer;">
            <div class="pr-3">
                <h3 class="card-title mb-0">
                    <i class="fa fa-chevron-{{ ($awardJobRunning || ($badgeAudit['mismatches'] ?? 0) > 0) ? 'down' : 'right' }} mr-2 badge-automation-chevron" aria-hidden="true"></i>
                    Automated badge awarding &amp; audit
                </h3>
                <p class="text-muted small mb-0 mt-1">Recalculates lifetime badges from current approved hub content (duplicate publications deduped). Scheduled: 1st of each month at 01:00.</p>
            </div>
            <div class="d-flex flex-wrap align-items-center mt-2 mt-md-0">
                @if(($badgeAudit['mismatches'] ?? 0) > 0)
                    <span class="badge badge-danger mr-2">{{ number_format((int) $badgeAudit['mismatches']) }} audit mismatch(es)</span>
                @endif
                @if($awardJobRunning)
                    <span class="badge badge-warning text-dark" id="badgeJobRunningBadge">Job running…</span>
                @endif
            </div>
        </div>
        <div id="badgeAutomationPanel" class="collapse {{ ($awardJobRunning || ($badgeAudit['mismatches'] ?? 0) > 0) ? 'show' : '' }}" aria-labelledby="badgeAutomationHeading">
        <div class="card-body border-top">
            <div id="badgeAwardProgressWrap" class="mb-4 {{ ($awardJobRunning || $initialProgressPercent > 0) ? '' : 'd-none' }}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong class="small mb-0">Award job progress</strong>
                    <span class="small text-muted" id="badgeAwardProgressLabel">
                        @if($initialProgressTotal > 0)
                            {{ number_format($initialProgressProcessed) }} / {{ number_format($initialProgressTotal) }} users ({{ $initialProgressPercent }}%)
                        @else
                            Starting…
                        @endif
                    </span>
                </div>
                <div class="progress" style="height: 1.25rem;">
                    <div id="badgeAwardProgressBar"
                         class="progress-bar progress-bar-striped {{ $awardJobRunning ? 'progress-bar-animated' : '' }} bg-success"
                         role="progressbar"
                         style="width: {{ max(2, $initialProgressPercent) }}%;"
                         aria-valuenow="{{ $initialProgressPercent }}"
                         aria-valuemin="0"
                         aria-valuemax="100"></div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-lg-6 mb-3 mb-lg-0">
                    <h5 class="h6 font-weight-bold">Queue status</h5>
                    <ul class="list-unstyled small mb-0">
                        <li><strong>Driver:</strong> <code>{{ $queueHealth['driver'] ?? 'unknown' }}</code>
                            @if(!empty($queueHealth['driver_ok']))
                                <span class="badge badge-success ml-1">OK</span>
                            @else
                                <span class="badge badge-danger ml-1">Unreachable</span>
                            @endif
                        </li>
                        <li><strong>Pending jobs (default queue):</strong>
                            @if(isset($queueHealth['pending_jobs']))
                                {{ number_format((int) $queueHealth['pending_jobs']) }}
                            @else
                                <span class="text-muted">n/a</span>
                            @endif
                        </li>
                        <li><strong>Failed jobs:</strong> {{ number_format((int) ($queueHealth['failed_jobs'] ?? 0)) }}</li>
                    </ul>
                    <p class="text-muted small mt-2 mb-0">{{ $queueHealth['worker_hint'] ?? '' }}</p>
                    @if(!empty($queueHealth['error']))
                        <p class="text-danger small mt-2 mb-0">{{ $queueHealth['error'] }}</p>
                    @endif
                </div>
                <div class="col-lg-6">
                    <h5 class="h6 font-weight-bold">Last award run</h5>
                    @if($lastAwardRun)
                        <ul class="list-unstyled small mb-0">
                            <li><strong>Period:</strong> {{ $lastAwardRun['period_label'] ?? (($lastAwardRun['year'] ?? '?').'-'.str_pad((string) ($lastAwardRun['month'] ?? '?'), 2, '0', STR_PAD_LEFT)) }}</li>
                            <li><strong>Status:</strong>
                                @if(($lastAwardRun['status'] ?? '') === 'completed')
                                    <span class="text-success">Completed</span>
                                @elseif(($lastAwardRun['status'] ?? '') === 'failed')
                                    <span class="text-danger">Failed</span>
                                @else
                                    {{ $lastAwardRun['status'] ?? '—' }}
                                @endif
                            </li>
                            <li><strong>Users processed:</strong> {{ number_format((int) ($lastAwardRun['users_processed'] ?? 0)) }}</li>
                            <li><strong>Badge upgrades:</strong> {{ number_format((int) ($lastAwardRun['badges_upgraded'] ?? 0)) }}</li>
                            <li><strong>Community rows synced:</strong> {{ number_format((int) ($lastAwardRun['community_rows_synced'] ?? 0)) }}</li>
                            <li><strong>Emails queued:</strong> {{ number_format((int) ($lastAwardRun['emails_queued'] ?? 0)) }}</li>
                            <li><strong>Finished:</strong> {{ isset($lastAwardRun['finished_at']) ? \Carbon\Carbon::parse($lastAwardRun['finished_at'])->format('Y-m-d H:i') : '—' }}</li>
                            <li><strong>Triggered by:</strong> {{ $lastAwardRun['triggered_by'] ?? '—' }}</li>
                        </ul>
                        @if(!empty($lastAwardRun['error']))
                            <p class="text-danger small mt-2 mb-0">{{ $lastAwardRun['error'] }}</p>
                        @endif
                    @else
                        <p class="text-muted small mb-0">No automated run recorded yet.</p>
                    @endif
                </div>
            </div>

            <hr>

            <h5 class="h6 font-weight-bold mb-3">Run badge awarding now</h5>
            <form method="POST" action="{{ route('admin.participant-badges.run-award-job') }}" class="row align-items-end" onsubmit="return confirm('Run community badge awarding for the selected month?');">
                @csrf
                <div class="col-md-2 mb-3">
                    <label class="form-label">Year</label>
                    <input type="number" name="year" class="form-control" value="{{ old('year', $defaultPeriod['year']) }}" min="2000" max="2100" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Month</label>
                    <select name="month" class="form-control" required>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ (int) old('month', $defaultPeriod['month']) === $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Run mode</label>
                    <select name="run_mode" class="form-control" required>
                        <option value="queue" {{ old('run_mode', 'queue') === 'queue' ? 'selected' : '' }}>Queue (background)</option>
                        <option value="sync" {{ old('run_mode') === 'sync' ? 'selected' : '' }}>Run now (synchronous)</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <button type="submit" class="btn btn-primary" {{ $awardJobRunning ? 'disabled' : '' }}>
                        <i class="fa fa-play mr-1"></i> Run badge award job
                    </button>
                </div>
            </form>
            <p class="text-muted small mb-4">
                CLI: <code>php artisan badges:award-community</code> · <code>php artisan badges:recalculate-lifetime</code> (full lifetime recalc).
            </p>

            <hr>

            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="h6 font-weight-bold mb-1">Badge audit</h5>
                    <p class="text-muted small mb-0">Compares stored badge data with a live recount of approved publications, forums, and comments. Duplicate publications (same DOI, link, or title) count once.</p>
                </div>
                <div class="mt-2 mt-md-0">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="badgeAuditRefreshBtn">
                        <i class="fa fa-refresh mr-1"></i> Refresh audit
                    </button>
                    <label class="small mb-0 ml-2">
                        <input type="checkbox" id="badgeAuditOnlyMismatches" checked> Only mismatches
                    </label>
                </div>
            </div>

            <div class="alert alert-light border small mb-3">
                <strong>{{ number_format((int) ($badgeAudit['total'] ?? 0)) }}</strong> badge holder(s) tracked.
                <span id="badgeAuditMismatchSummary">
                    @if(($badgeAudit['mismatches'] ?? 0) > 0)
                        <span class="text-danger font-weight-bold">{{ number_format((int) $badgeAudit['mismatches']) }} need correction</span> — run <code>php artisan badges:recalculate-lifetime</code> or the award job above.
                    @else
                        No mismatches in the current sample.
                    @endif
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover mb-0" id="badgeAuditTable">
                    <thead>
                        <tr>
                            <th>Participant</th>
                            <th>Status</th>
                            <th class="text-right">Stored</th>
                            <th class="text-right">Live</th>
                            <th class="text-right">Δ</th>
                            <th>Stored tier</th>
                            <th>Earned tier</th>
                            <th class="text-right">Dup. pubs</th>
                        </tr>
                    </thead>
                    <tbody id="badgeAuditTableBody">
                        @forelse($badgeAudit['rows'] ?? [] as $row)
                            <tr>
                                <td>
                                    <div>{{ $row['name'] ?? '—' }}</div>
                                    <div class="small text-muted">{{ $row['email'] ?? '' }}</div>
                                </td>
                                <td>
                                    @if(($row['status'] ?? '') === 'over_awarded')
                                        <span class="badge badge-danger">Over-awarded</span>
                                    @elseif(($row['status'] ?? '') === 'stale')
                                        <span class="badge badge-warning text-dark">Stale</span>
                                    @elseif(($row['status'] ?? '') === 'under_awarded')
                                        <span class="badge badge-info">Under-awarded</span>
                                    @else
                                        <span class="badge badge-success">OK</span>
                                    @endif
                                </td>
                                <td class="text-right">{{ number_format((int) ($row['stored_contributions'] ?? 0)) }}</td>
                                <td class="text-right">{{ number_format((int) ($row['live_contributions'] ?? 0)) }}</td>
                                <td class="text-right {{ ($row['contribution_delta'] ?? 0) > 0 ? 'text-danger' : '' }}">{{ ($row['contribution_delta'] ?? 0) > 0 ? '+' : '' }}{{ number_format((int) ($row['contribution_delta'] ?? 0)) }}</td>
                                <td>{{ $row['stored_badge_name'] ?? '—' }}</td>
                                <td>{{ $row['earned_badge_name'] ?? '—' }}</td>
                                <td class="text-right">{{ number_format((int) ($row['breakdown']['duplicate_publications_excluded'] ?? 0)) }}</td>
                            </tr>
                        @empty
                            <tr id="badgeAuditEmptyRow">
                                <td colspan="8" class="text-center text-muted">No audit rows to show.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        </div>
    </div>

    {{-- Badge key (legend) --}}
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title mb-0">Badge key</h3>
            <p class="text-muted small mb-0 mt-1">Lifetime contributor badges grow with total hub contributions (resources, forums, comments). Community activity is shown as stars / drill-down on the public profile.</p>
        </div>
        <div class="card-body">
            <div class="row">
                @forelse($badgeTypes as $bt)
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="border rounded p-3 h-100 d-flex align-items-start">
                            @if($bt->image_path)
                                <img src="{{ asset($bt->image_path) }}" alt="" class="mr-2" style="width:40px;height:40px;object-fit:contain;">
                            @else
                                <span class="rounded d-inline-flex align-items-center justify-content-center mr-2 flex-shrink-0"
                                      style="width:40px;height:40px;background:{{ $bt->badge_color }};border:1px solid rgba(0,0,0,.1);"></span>
                            @endif
                            <div>
                                <strong>{{ $bt->name }}</strong>
                                @if(!$bt->is_active)
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                                <div class="small text-muted mt-1">{{ $bt->description }}</div>
                                <div class="small"><strong>Threshold:</strong> {{ $bt->contribution_threshold }}+ lifetime contributions</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-muted">No badge types configured. Run migrations / seeders for <code>badge_types</code>.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Manual award --}}
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title mb-0">Manually set lifetime badge</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.participant-badges.award') }}" class="row">
                @csrf
                <div class="col-md-5 mb-3">
                    <label class="form-label">Participant (user) <span class="text-danger">*</span></label>
                    <select name="user_id" class="form-control select2" required data-placeholder="Select user">
                        <option value=""></option>
                        @foreach($usersForAward as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} &lt;{{ $u->email }}&gt;</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Badge tier <span class="text-danger">*</span></label>
                    <select name="badge_type_id" class="form-control" required>
                        <option value="">— Select —</option>
                        @foreach($badgeTypes->where('is_active', true) as $bt)
                            <option value="{{ $bt->id }}">{{ $bt->name }} ({{ $bt->contribution_threshold }}+ lifetime)</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Lifetime contributions</label>
                    <input type="number" name="lifetime_contributions" class="form-control" min="0" placeholder="Auto from hub">
                </div>
                <div class="col-md-12 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Set lifetime badge</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Lifetime badges list --}}
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title mb-0">Lifetime contributor badges</h3>
            <p class="text-muted small mb-0 mt-1">One badge per user; strength grows with total hub contributions and only upgrades over time.</p>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped table-bordered table-hover table-sm">
                <thead>
                    <tr>
                        <th>Participant</th>
                        <th>Email</th>
                        <th>Author</th>
                        <th>Badge tier</th>
                        <th class="text-right">Lifetime contrib.</th>
                        <th>Last upgraded</th>
                        <th width="90"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lifetimeBadges as $row)
                        <tr>
                            <td>{{ $row->user->name ?? '—' }}</td>
                            <td>{{ $row->user->email ?? '—' }}</td>
                            <td>{{ $row->user->author->name ?? '—' }}</td>
                            <td>
                                @if($row->badgeType)
                                    <span class="badge badge-pill px-2 py-1" style="background:{{ $row->badgeType->badge_color }};color:#111;">{{ $row->badgeType->name }}</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-right">{{ number_format((int) $row->lifetime_contributions) }}</td>
                            <td>{{ $row->last_upgraded_at ? $row->last_upgraded_at->format('Y-m-d H:i') : '—' }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.participant-badges.revoke', $row) }}" onsubmit="return confirm('Remove lifetime badge tier for this user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Revoke</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No lifetime badges yet. Run <code>php artisan badges:recalculate-lifetime</code> or the monthly job.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="py-2">{{ $lifetimeBadges->links() }}</div>
        </div>
    </div>

    {{-- Authors / data sources --}}
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title mb-0">Authors &amp; data sources (publication counts)</h3>
            <p class="text-muted small mb-0 mt-1">Publication authors with at least one resource. Linked portal users show community badges when applicable.</p>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped table-bordered table-hover table-sm">
                <thead>
                    <tr>
                        <th>Author / source</th>
                        <th class="text-right">Publications</th>
                        <th>Linked user</th>
                        <th>Community badges (user)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($authors as $author)
                        <tr>
                            <td>{{ $author->name }}</td>
                            <td class="text-right">{{ $author->publications_count }}</td>
                            <td>
                                @if($author->user)
                                    {{ $author->user->name }} &lt;{{ $author->user->email }}&gt;
                                @else
                                    <span class="text-muted">No linked account</span>
                                @endif
                            </td>
                            <td>
                                @if($author->user && $author->user->lifetimeBadge && $author->user->lifetimeBadge->badgeType)
                                    <span class="badge badge-pill mr-1 mb-1" style="background:{{ $author->user->lifetimeBadge->badgeType->badge_color }};color:#111;">
                                        {{ $author->user->lifetimeBadge->badgeType->name }}
                                    </span>
                                    <div class="small text-muted mt-1">{{ number_format((int) $author->user->lifetimeBadge->lifetime_contributions) }} lifetime</div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No authors with publications found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="py-2">{{ $authors->links() }}</div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    if (typeof $.fn.select2 === 'function') {
        $('.select2').select2({ width: '100%' });
    }

    (function () {
        var jobStatusUrl = @json(route('admin.participant-badges.job-status'));
        var auditUrl = @json(route('admin.participant-badges.audit'));
        var pollTimer = null;

        function statusBadgeHtml(status) {
            if (status === 'over_awarded') return '<span class="badge badge-danger">Over-awarded</span>';
            if (status === 'stale') return '<span class="badge badge-warning text-dark">Stale</span>';
            if (status === 'under_awarded') return '<span class="badge badge-info">Under-awarded</span>';
            return '<span class="badge badge-success">OK</span>';
        }

        function renderAuditRows(rows) {
            var $body = $('#badgeAuditTableBody');
            $body.empty();
            if (!rows || !rows.length) {
                $body.append('<tr><td colspan="8" class="text-center text-muted">No audit rows to show.</td></tr>');
                return;
            }
            rows.forEach(function (row) {
                var delta = parseInt(row.contribution_delta || 0, 10);
                var deltaClass = delta > 0 ? 'text-danger' : '';
                var deltaPrefix = delta > 0 ? '+' : '';
                $body.append(
                    '<tr>' +
                    '<td><div>' + (row.name || '—') + '</div><div class="small text-muted">' + (row.email || '') + '</div></td>' +
                    '<td>' + statusBadgeHtml(row.status) + '</td>' +
                    '<td class="text-right">' + Number(row.stored_contributions || 0).toLocaleString() + '</td>' +
                    '<td class="text-right">' + Number(row.live_contributions || 0).toLocaleString() + '</td>' +
                    '<td class="text-right ' + deltaClass + '">' + deltaPrefix + Number(delta).toLocaleString() + '</td>' +
                    '<td>' + (row.stored_badge_name || '—') + '</td>' +
                    '<td>' + (row.earned_badge_name || '—') + '</td>' +
                    '<td class="text-right">' + Number((row.breakdown && row.breakdown.duplicate_publications_excluded) || 0).toLocaleString() + '</td>' +
                    '</tr>'
                );
            });
        }

        function refreshAudit() {
            var onlyMismatches = $('#badgeAuditOnlyMismatches').is(':checked') ? 1 : 0;
            $.get(auditUrl, { only_mismatches: onlyMismatches, limit: 200 })
                .done(function (data) {
                    renderAuditRows(data.rows || []);
                    var mismatches = parseInt(data.mismatches || 0, 10);
                    var total = parseInt(data.total || 0, 10);
                    var summary = mismatches > 0
                        ? '<span class="text-danger font-weight-bold">' + mismatches.toLocaleString() + ' need correction</span> — run <code>php artisan badges:recalculate-lifetime</code> or the award job above.'
                        : 'No mismatches in the current sample.';
                    $('#badgeAuditMismatchSummary').html(summary);
                });
        }

        function updateProgress(progress, running) {
            var $wrap = $('#badgeAwardProgressWrap');
            var $bar = $('#badgeAwardProgressBar');
            var $label = $('#badgeAwardProgressLabel');
            var $runningBadge = $('#badgeJobRunningBadge');

            if (!progress && !running) {
                return;
            }

            $wrap.removeClass('d-none');
            var percent = progress ? parseInt(progress.percent || 0, 10) : 0;
            var processed = progress ? parseInt(progress.processed || 0, 10) : 0;
            var total = progress ? parseInt(progress.total || 0, 10) : 0;

            $bar.css('width', Math.max(2, percent) + '%').attr('aria-valuenow', percent);
            if (running) {
                $bar.addClass('progress-bar-animated');
            } else {
                $bar.removeClass('progress-bar-animated');
            }

            if (total > 0) {
                $label.text(processed.toLocaleString() + ' / ' + total.toLocaleString() + ' users (' + percent + '%)');
            } else if (running) {
                $label.text('Starting…');
            }

            if (running) {
                if (!$runningBadge.length) {
                    $('#badgeAutomationHeading .d-flex.flex-wrap.align-items-center').append('<span class="badge badge-warning text-dark" id="badgeJobRunningBadge">Job running…</span>');
                }
            } else {
                $runningBadge.remove();
            }
        }

        function pollJobStatus() {
            $.get(jobStatusUrl)
                .done(function (data) {
                    updateProgress(data.progress, data.running);
                    if (data.running) {
                        pollTimer = window.setTimeout(pollJobStatus, 2000);
                    } else {
                        pollTimer = null;
                        if (data.last_run && data.last_run.status === 'completed') {
                            refreshAudit();
                        }
                    }
                });
        }

        $('#badgeAutomationPanel').on('show.bs.collapse', function () {
            $('.badge-automation-chevron').removeClass('fa-chevron-right').addClass('fa-chevron-down');
        }).on('hide.bs.collapse', function () {
            $('.badge-automation-chevron').removeClass('fa-chevron-down').addClass('fa-chevron-right');
        });

        $('#badgeAuditRefreshBtn').on('click', refreshAudit);
        $('#badgeAuditOnlyMismatches').on('change', refreshAudit);

        @if($awardJobRunning || (session('success') && str_contains((string) session('success'), 'queued')))
            pollJobStatus();
        @endif
    })();
</script>
@endsection
