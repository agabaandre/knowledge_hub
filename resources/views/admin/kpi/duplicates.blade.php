@extends(admin_layout())

@section('content')
<div class="page-header">
    <h1 class="page-title">Review duplicate KPI records</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('admin/kpi') }}">Indicators</a></li>
            <li class="breadcrumb-item active">Duplicates</li>
        </ol>
    </div>
</div>

@include('admin.kpi.partials.data_management_panel', ['show_create' => false])

@if(!empty($indicator_groups) || !empty($subject_area_groups))
<div class="alert alert-info">
    Duplicate groups are detected by matching OWID slug/URL, normalized names, or identical subject-area search settings.
    The highlighted record in each group is the suggested keeper (published records and those with more country data are preferred).
</div>
@else
<div class="alert alert-success">No duplicate indicator or subject area groups were found.</div>
@endif

@if(!empty($indicator_groups))
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">Duplicate indicators ({{ count($indicator_groups) }} groups)</h3>
        <form method="POST" action="{{ url('admin/kpi/dedupe/indicators/auto') }}" class="js-kpi-queued-task" data-confirm="Automatically merge all duplicate indicator groups?">
            @csrf
            <button type="submit" class="btn btn-warning btn-sm"><i class="fa fa-compress"></i> Auto-merge all</button>
        </form>
    </div>
    <div class="card-body">
        @foreach($indicator_groups as $group)
        <div class="border rounded p-3 mb-3">
            <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                <div>
                    <strong>{{ $group['reason'] }}</strong>
                    <div class="text-muted small">{{ $group['label'] }}</div>
                </div>
                <form method="POST" action="{{ url('admin/kpi/dedupe/indicators/merge') }}" onsubmit="return confirm('Merge duplicates in this group into the selected keeper?');">
                    @csrf
                    <input type="hidden" name="keep_id" value="{{ $group['keep_id'] }}">
                    @foreach($group['members'] as $member)
                        @if((int) $member['id'] !== (int) $group['keep_id'])
                            <input type="hidden" name="duplicate_ids[]" value="{{ $member['id'] }}">
                        @endif
                    @endforeach
                    <button type="submit" class="btn btn-sm btn-outline-primary">Merge group (keep #{{ $group['keep_id'] }})</button>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Source</th>
                            <th>OWID slug</th>
                            <th>Subject area</th>
                            <th>Values</th>
                            <th>Keep?</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($group['members'] as $member)
                        <tr class="{{ (int) $member['id'] === (int) $group['keep_id'] ? 'table-success' : '' }}">
                            <td>{{ $member['id'] }}</td>
                            <td>{{ $member['name'] }}</td>
                            <td>{{ ucfirst($member['status']) }}</td>
                            <td>{{ strtoupper($member['source']) }}</td>
                            <td><code>{{ $member['owid_chart_slug'] ?: '—' }}</code></td>
                            <td>{{ $member['subject_area'] ?: '—' }}</td>
                            <td>{{ $member['data_records'] }}</td>
                            <td>{{ (int) $member['id'] === (int) $group['keep_id'] ? 'Suggested keeper' : 'Duplicate' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

@if(!empty($subject_area_groups))
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">Duplicate subject areas ({{ count($subject_area_groups) }} groups)</h3>
        <form method="POST" action="{{ url('admin/kpi/subject-areas/dedupe/auto') }}" class="js-kpi-queued-task" data-confirm="Automatically merge all duplicate subject area groups?">
            @csrf
            <button type="submit" class="btn btn-warning btn-sm"><i class="fa fa-compress"></i> Auto-merge all</button>
        </form>
    </div>
    <div class="card-body">
        @foreach($subject_area_groups as $group)
        <div class="border rounded p-3 mb-3">
            <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                <div>
                    <strong>{{ $group['reason'] }}</strong>
                    <div class="text-muted small">{{ $group['label'] }}</div>
                </div>
                <form method="POST" action="{{ url('admin/kpi/subject-areas/dedupe/merge') }}" onsubmit="return confirm('Merge duplicates in this group into the selected keeper?');">
                    @csrf
                    <input type="hidden" name="keep_id" value="{{ $group['keep_id'] }}">
                    @foreach($group['members'] as $member)
                        @if((int) $member['id'] !== (int) $group['keep_id'])
                            <input type="hidden" name="duplicate_ids[]" value="{{ $member['id'] }}">
                        @endif
                    @endforeach
                    <button type="submit" class="btn btn-sm btn-outline-primary">Merge group (keep #{{ $group['keep_id'] }})</button>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>OWID topic</th>
                            <th>Search query</th>
                            <th>Indicators</th>
                            <th>Keep?</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($group['members'] as $member)
                        <tr class="{{ (int) $member['id'] === (int) $group['keep_id'] ? 'table-success' : '' }}">
                            <td>{{ $member['id'] }}</td>
                            <td>{{ $member['name'] }}</td>
                            <td><code>{{ $member['slug'] ?: '—' }}</code></td>
                            <td>{{ $member['owid_topic'] ?: '—' }}</td>
                            <td>{{ $member['owid_search_query'] ?: '—' }}</td>
                            <td>{{ $member['indicators'] }}</td>
                            <td>{{ (int) $member['id'] === (int) $group['keep_id'] ? 'Suggested keeper' : 'Duplicate' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection
