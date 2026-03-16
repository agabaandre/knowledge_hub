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
        <h1 class="page-title">KPI Indicator Data</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">KPI Indicator Data</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <!-- Filters Card -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="card-title mb-0">Filter Indicator Data</h3>
                        <small class="text-muted">Filter by member state</small>
                    </div>
                    <div>
                        @can('manage_kpis')
                        <a href="#create-modal" data-toggle="modal" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus"></i> Add Indicator Data
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ url('admin/kpi/data') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group mb-0">
                                    <label class="form-label" for="country_id">Member State</label>
                                    @include('partials.countries.dropdown',[
                                        'onchange' => 'this.form.submit()',
                                        'selected' => @$search->country_id,
                                        'allfield' => 'All Countries'
                                    ])
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group mb-0">
                                    <label class="form-label" for="kpi_id">Indicator</label>
                                    <select class="form-control js-example-basic-single" name="kpi_id" id="kpi_id" onchange="this.form.submit()">
                                        <option value="">All Indicators</option>
                                        @foreach($kpis as $kpi)
                                            <option value="{{ $kpi->id }}" {{ @$search->kpi_id == $kpi->id ? 'selected' : '' }}>
                                                {{ $kpi->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group mb-0">
                                    <label class="form-label">&nbsp;</label>
                                    <div>
                                        <a href="{{ url('admin/kpi/data') }}" class="btn btn-secondary btn-sm">Clear</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Indicator Data Table Card -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">Indicator Data</h3>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('alert-success'))
                        <div class="alert alert-success">{{ session('alert-success') }}</div>
                    @endif
                    @if(session('alert-danger'))
                        <div class="alert alert-danger">{{ session('alert-danger') }}</div>
                    @endif

                    <!-- Indicator Data Table -->
                    <div class="table-responsive">
                        <table id="kpiDataTable" class="table table-striped table-bordered table-hover" style="border-radius: 0;">
                            <thead>
                                <tr>
                                    <th width="60px">#</th>
                                    <th>Indicator</th>
                                    <th width="20%">Country</th>
                                    <th width="15%">Period</th>
                                    <th width="15%">Value</th>
                                    @can('manage_kpis')
                                    <th width="150px">Actions</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kpi_data as $idx => $data)
                                <tr>
                                    <td><span class="text-muted">{{ $kpi_data->firstItem() + $idx }}</span></td>
                                    <td><strong>{{ $data->kpi->name ?? 'N/A' }}</strong></td>
                                    <td>{{ $data->country_name ?? 'N/A' }}</td>
                                    <td>{{ $data->period ?? 'N/A' }}</td>
                                    <td>{{ $data->kpi_value ?? 'N/A' }}</td>
                                    @can('manage_kpis')
                                    <td>
                                        <a href="javascript:void(0);" onclick="openEditModal('{{ $data->kpi_id }}', '{{ $data->country_id }}', '{{ $data->period }}')" class="btn btn-sm btn-outline-primary mr-1" title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="javascript:void(0);" onclick="openDeleteModal('{{ $data->kpi_id }}', '{{ $data->country_id }}', '{{ $data->period }}')" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                    @endcan
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $kpi_data->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include modals -->
    @can('manage_kpis')
    @include('admin.kpi.partials.add-indicator-data-modal')
    @include('admin.kpi.partials.edit-indicator-data-modal', [
        'kpis' => $kpis,
        'countries' => $countries
    ])
    @include('admin.kpi.partials.delete-data-modal')
    @endcan

@endsection

@section('scripts')
    @include('common.select2')
    <script>
        function openEditModal(kpi_id, country_id, period) {
            $.ajax({
                url: '{{ url("admin/kpi/get_data") }}',
                method: 'GET',
                data: { 
                    kpi_id: kpi_id,
                    country_id: country_id,
                    period: period
                },
                success: function(response) {
                    if (response.success && response.data) {
                        var data = response.data;
                        // Store old values for update
                        $('#edit_old_kpi_id').val(data.old_kpi_id);
                        $('#edit_old_country_id').val(data.old_country_id);
                        $('#edit_old_period').val(data.old_period);
                        // Set form values
                        $('#edit_kpi_id').val(data.kpi_id).trigger('change');
                        $('#edit_country_id').val(data.country_id).trigger('change');
                        $('#edit_year').val(data.year).trigger('change');
                        $('#edit_month').val(data.month).trigger('change');
                        $('#edit_indicator_value').val(data.value);
                        $('#edit-modal').modal('show');
                    } else {
                        alert('Failed to load data');
                    }
                },
                error: function() {
                    alert('An error occurred while loading data');
                }
            });
        }

        function openDeleteModal(kpi_id, country_id, period) {
            $('#delete_kpi_id').val(kpi_id);
            $('#delete_country_id').val(country_id);
            $('#delete_period').val(period);
            $('#delete-data-modal').modal('show');
        }

        // Initialize Select2 for indicator filter dropdown
        $(document).ready(function() {
            $('#kpi_id').select2({
                placeholder: 'All Indicators',
                allowClear: true,
                width: '100%'
            });
        });

        // Initialize Select2 for create modal
        $('#create-modal').on('shown.bs.modal', function () {
            $('.country-select, .kpi-select').select2({
                dropdownParent: $('#create-modal'),
                width: '100%'
            });
        });
        
        // Initialize Select2 for edit modal
        $('#edit-modal').on('shown.bs.modal', function () {
            $('#edit_kpi_id, #edit_country_id, #edit_year, #edit_month').select2({
                dropdownParent: $('#edit-modal'),
                width: '100%'
            });
        });
    </script>
@endsection
