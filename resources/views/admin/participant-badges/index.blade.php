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
    @endphp

    {{-- Automated award job + queue health --}}
    <div class="card mb-4">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h3 class="card-title mb-0">Automated badge awarding</h3>
                <p class="text-muted small mb-0 mt-1">Runs monthly for the previous calendar month. Scheduled: 1st of each month at 01:00 (server time).</p>
            </div>
            @if($awardJobRunning)
                <span class="badge badge-warning text-dark mt-2 mt-md-0">Job running…</span>
            @endif
        </div>
        <div class="card-body">
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
                            <li><strong>Badges awarded:</strong> {{ number_format((int) ($lastAwardRun['badges_awarded'] ?? 0)) }}</li>
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
            <p class="text-muted small mb-0">
                CLI: <code>php artisan badges:award-community</code> (sync) or
                <code>php artisan badges:award-community --queue</code> (background).
                Defaults to the <strong>previous month</strong> when year/month are omitted.
            </p>
        </div>
    </div>

    {{-- Badge key (legend) --}}
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title mb-0">Badge key</h3>
            <p class="text-muted small mb-0 mt-1">Community contributor badges are earned from monthly activity in a community of practice (or can be awarded manually below).</p>
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
                                <div class="small"><strong>Threshold:</strong> {{ $bt->contribution_threshold }}+ contributions / month</div>
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
            <h3 class="card-title mb-0">Manually award a badge</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.participant-badges.award') }}" class="row">
                @csrf
                <div class="col-md-4 mb-3">
                    <label class="form-label">Participant (user) <span class="text-danger">*</span></label>
                    <select name="user_id" class="form-control select2" required data-placeholder="Select user">
                        <option value=""></option>
                        @foreach($usersForAward as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} &lt;{{ $u->email }}&gt;</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Community of practice <span class="text-danger">*</span></label>
                    <select name="community_of_practice_id" class="form-control" required>
                        <option value="">— Select —</option>
                        @foreach($communities as $c)
                            <option value="{{ $c->id }}">{{ $c->community_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Badge type <span class="text-danger">*</span></label>
                    <select name="badge_type_id" class="form-control" required>
                        <option value="">— Select —</option>
                        @foreach($badgeTypes->where('is_active', true) as $bt)
                            <option value="{{ $bt->id }}">{{ $bt->name }} ({{ $bt->contribution_threshold }}+)</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Year <span class="text-danger">*</span></label>
                    <input type="number" name="year" class="form-control" value="{{ now()->year }}" min="2000" max="2100" required>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Month <span class="text-danger">*</span></label>
                    <select name="month" class="form-control" required>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ (int) now()->month === $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Contributions count</label>
                    <input type="number" name="contributions_count" class="form-control" min="0" placeholder="Defaults to badge threshold">
                    <small class="text-muted">Optional; stored on the award record.</small>
                </div>
                <div class="col-md-4 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Award badge</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Awards list --}}
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title mb-0">Participants with community badges</h3>
            <p class="text-muted small mb-0 mt-1">Each row is one award. <strong>Publications</strong> counts resources where the user is uploader (<code>user_id</code>) or linked author (<code>author_id</code>).</p>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped table-bordered table-hover table-sm">
                <thead>
                    <tr>
                        <th>Participant</th>
                        <th>Email</th>
                        <th>Author / source</th>
                        <th class="text-right">Publications</th>
                        <th>Community</th>
                        <th>Badge</th>
                        <th>Period</th>
                        <th class="text-right">Contrib.</th>
                        <th>Awarded</th>
                        <th width="90"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($badgeAwards as $row)
                        <tr>
                            <td>{{ $row->user->name ?? '—' }}</td>
                            <td>{{ $row->user->email ?? '—' }}</td>
                            <td>
                                @if($row->user && $row->user->author)
                                    {{ $row->user->author->name }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-right">{{ (int) ($row->publications_total ?? 0) }}</td>
                            <td>{{ $row->community->community_name ?? '—' }}</td>
                            <td>
                                @if($row->badgeType)
                                    <span class="badge badge-pill px-2 py-1" style="background:{{ $row->badgeType->badge_color }};color:#111;">{{ $row->badgeType->name }}</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $row->year }}-{{ str_pad($row->month, 2, '0', STR_PAD_LEFT) }}</td>
                            <td class="text-right">{{ $row->contributions_count }}</td>
                            <td>{{ $row->awarded_at ? $row->awarded_at->format('Y-m-d H:i') : '—' }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.participant-badges.revoke', $row) }}" onsubmit="return confirm('Remove this badge award?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Revoke</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">No badge awards yet. Use the form above or run <code>php artisan badges:award-community</code>.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="py-2">{{ $badgeAwards->links() }}</div>
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
                                @if($author->user && $author->user->badges->isNotEmpty())
                                    @foreach($author->user->badges->unique('badge_type_id') as $ub)
                                        @if($ub->badgeType)
                                            <span class="badge badge-pill mr-1 mb-1" style="background:{{ $ub->badgeType->badge_color }};color:#111;">{{ $ub->badgeType->name }}</span>
                                        @endif
                                    @endforeach
                                    <div class="small text-muted mt-1">{{ $author->user->badges->count() }} award(s) total</div>
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
</script>
@endsection
