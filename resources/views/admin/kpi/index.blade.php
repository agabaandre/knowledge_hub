@extends(admin_layout())

@section('styles')
    @include('common.table')
    <style>
        .card { border: 1px solid #e2e8f0; border-radius: 0; margin-bottom: 1.5rem; }
        .card-header { background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 1rem 1.5rem; }
        .card-body { padding: 1.5rem; }
        .form-group { margin-bottom: 1.5rem; }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">KPI Indicators</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">KPI Indicators</li>
            </ol>
        </div>
    </div>

    @include('admin.kpi.partials.data_management_panel', ['show_create' => true])

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Search &amp; filter indicators</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ url('admin/kpi') }}" class="mb-0">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group mb-2">
                                    <label class="small text-muted">Search</label>
                                    <input type="text" name="term" class="form-control" placeholder="Name or description" value="{{ @$search->term ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-2">
                                    <label class="small text-muted">Status</label>
                                    <select name="status" class="form-control" onchange="this.form.submit()">
                                        <option value="">All statuses</option>
                                        <option value="published" {{ (@$search->status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
                                        <option value="draft" {{ (@$search->status ?? '') === 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="recalled" {{ (@$search->status ?? '') === 'recalled' ? 'selected' : '' }}>Recalled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group mb-2">
                                    <label class="small text-muted">Source</label>
                                    <select name="source" class="form-control" onchange="this.form.submit()">
                                        <option value="">All sources</option>
                                        <option value="owid" {{ (@$search->source ?? '') === 'owid' ? 'selected' : '' }}>Our World in Data</option>
                                        <option value="manual" {{ (@$search->source ?? '') === 'manual' ? 'selected' : '' }}>Manual</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="form-group mb-2 w-100">
                                    <button type="submit" class="btn btn-primary btn-sm mr-1">Filter</button>
                                    <a href="{{ url('admin/kpi') }}" class="btn btn-secondary btn-sm">Clear</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Indicators</h3>
                    <small class="text-muted d-block">Approve indicators to show them on member state pages. Use recall to hide without deleting.</small>
                </div>
                <div class="card-body">
                    @if(session('alert-success'))
                        <div class="alert alert-success">{{ session('alert-success') }}</div>
                    @endif
                    @if(session('alert-info'))
                        <div class="alert alert-info">{{ session('alert-info') }}</div>
                    @endif
                    @if(session('alert-warning'))
                        <div class="alert alert-warning">{{ session('alert-warning') }}</div>
                    @endif
                    @if(session('alert-danger'))
                        <div class="alert alert-danger">{{ session('alert-danger') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table id="indicatorsTable" class="table table-striped table-bordered table-hover" style="border-radius: 0;">
                            <thead>
                                <tr>
                                    <th width="60px">#</th>
                                    <th>Indicator</th>
                                    <th>Description</th>
                                    <th width="15%">Subject Area</th>
                                    <th width="10%">Frequency</th>
                                    <th width="10%">Status</th>
                                    <th width="220px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($indicators as $idx => $row)
                                <tr>
                                    <td><span class="text-muted">{{ $indicators->firstItem() + $idx }}</span></td>
                                    <td>
                                        <strong>{{ $row->name ?? 'N/A' }}</strong>
                                        @if($owidChartUrl = owid_chart_url($row))
                                            <br><a href="{{ $owidChartUrl }}" target="_blank" rel="noopener noreferrer" class="small">View on Our World in Data</a>
                                        @endif
                                    </td>
                                    <td>{!! Str::limit(strip_tags($row->description ?? ''), 80) !!}</td>
                                    <td>{{ $row->subjectArea ? $row->subjectArea->name : 'N/A' }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $row->frequency ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        @php $status = $row->status ?? 'draft'; @endphp
                                        <span class="badge badge-{{ $status === 'published' ? 'success' : ($status === 'recalled' ? 'warning' : 'secondary') }}">{{ ucfirst($status) }}</span>
                                        @if(($row->source ?? '') === 'owid')
                                            <span class="badge badge-light border">OWID</span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">
                                        @if(($row->status ?? '') !== 'published')
                                        <form method="POST" action="{{ url('admin/kpi/approve') }}" class="d-inline js-kpi-queued-task" data-confirm="Publish this indicator on member state pages?">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $row->id }}">
                                            <button type="submit" class="btn btn-sm btn-success mr-1" title="Approve for publication"><i class="fa fa-check"></i> Publish</button>
                                        </form>
                                        @endif
                                        @if(($row->status ?? '') === 'published')
                                        <form method="POST" action="{{ url('admin/kpi/recall') }}" class="d-inline" onsubmit="return confirm('Recall this indicator from public pages?');">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $row->id }}">
                                            <button type="submit" class="btn btn-sm btn-warning mr-1" title="Recall from publication"><i class="fa fa-undo"></i> Recall</button>
                                        </form>
                                        @endif
                                        @if(($row->source ?? '') === 'owid' && !kpi_manual_data_only())
                                        <form method="POST" action="{{ url('admin/kpi/owid/sync-one') }}" class="d-inline js-kpi-queued-task" data-confirm="Refresh country values for this indicator?">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $row->id }}">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary mr-1" title="Refresh values"><i class="fa fa-sync"></i></button>
                                        </form>
                                        @endif
                                        <a href="{{ url('admin/kpi/data?kpi_id='.$row->id) }}" class="btn btn-sm btn-outline-info mr-1" title="View country values"><i class="fa fa-table"></i></a>
                                        <a href="javascript:void(0);" onclick="openEditModal('{{ $row->id }}')" class="btn btn-sm btn-outline-primary mr-1" title="Edit"><i class="fa fa-edit"></i></a>
                                        <a href="javascript:void(0);" onclick="openDeleteModal('{{ $row->id }}')" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $indicators->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.kpi.partials.delete-modal')
    @include('admin.kpi.partials.create-modal', ['subject_areas' => $subject_areas])
    @include('admin.kpi.partials.edit-modal', ['subject_areas' => $subject_areas])
@endsection

@section('scripts')
    @include('common.select2')
    <script>
        $('#create-modal').on('shown.bs.modal', function () {
            $('#subject_area, #frequency').select2({ dropdownParent: $('#create-modal') });
        });
        $('#edit-modal').on('shown.bs.modal', function () {
            $('#edit_subject_area, #edit_frequency').select2({ dropdownParent: $('#edit-modal') });
        });
    </script>
@endsection
