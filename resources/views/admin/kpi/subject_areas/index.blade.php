@extends(admin_layout())

@section('content')
<div class="page-header">
    <h1 class="page-title">KPI Subject Areas</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('admin/kpi') }}">Indicators</a></li>
            <li class="breadcrumb-item active">Subject Areas</li>
        </ol>
    </div>
</div>

@include('admin.kpi.partials.data_management_panel', ['show_create' => false])

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">Subject areas (OWID topics)</h3>
        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#subjectAreaModal" onclick="resetSubjectAreaForm()">
            <i class="fa fa-plus"></i> Add subject area
        </button>
    </div>
    <div class="card-body">
        @if(session('alert-success'))
            <div class="alert alert-success">{{ session('alert-success') }}</div>
        @endif
        @if(session('alert-warning'))
            <div class="alert alert-warning">{{ session('alert-warning') }}</div>
        @endif
        @if(session('alert-danger'))
            <div class="alert alert-danger">{{ session('alert-danger') }}</div>
        @endif

        <p class="text-muted">Subject areas group indicators on country pages and control which Our World in Data topics are searched when you run <strong>Fetch new indicators</strong> above.</p>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>OWID topic</th>
                        <th>Search query</th>
                        <th>Sort</th>
                        <th>Active</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subject_areas as $idx => $area)
                    <tr>
                        <td>{{ $subject_areas->firstItem() + $idx }}</td>
                        <td>{{ $area->name }}</td>
                        <td><code>{{ $area->owid_topic ?: '—' }}</code></td>
                        <td><small>{{ $area->owid_search_query ?: '—' }}</small></td>
                        <td>{{ $area->sort_order }}</td>
                        <td>{{ $area->is_active ? 'Yes' : 'No' }}</td>
                        <td class="text-nowrap">
                            <form method="POST" action="{{ url('admin/kpi/owid/discover') }}" class="d-inline js-kpi-queued-task" data-confirm="Fetch new indicators for {{ $area->name }}?">
                                @csrf
                                <input type="hidden" name="subject_area_id" value="{{ $area->id }}">
                                <button type="submit" class="btn btn-sm btn-outline-success" title="Fetch indicators for this area"><i class="fa fa-cloud-download-alt"></i></button>
                            </form>
                            <button class="btn btn-sm btn-outline-primary" onclick="editSubjectArea({{ $area->id }})"><i class="fa fa-edit"></i></button>
                            <a href="{{ url('admin/kpi/subject-areas/delete?id='.$area->id) }}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this subject area?')"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $subject_areas->links() }}
    </div>
</div>

<div class="modal fade" id="subjectAreaModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ url('admin/kpi/subject-areas/save') }}" class="modal-content">
            @csrf
            <input type="hidden" name="id" id="sa_id">
            <div class="modal-header"><h5 class="modal-title">Subject area</h5></div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" id="sa_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>OWID topic</label>
                    <input type="text" name="owid_topic" id="sa_topic" class="form-control" placeholder="Health">
                    <small class="text-muted">Optional Algolia topic filter. Leave blank to search by query only.</small>
                </div>
                <div class="form-group">
                    <label>OWID search query</label>
                    <input type="text" name="owid_search_query" id="sa_search_query" class="form-control" placeholder="health mortality disease">
                    <small class="text-muted">Used when discovering charts. Falls back here if the topic filter is invalid.</small>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="sa_description" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Sort order</label>
                    <input type="number" name="sort_order" id="sa_sort" class="form-control" value="100">
                </div>
                <div class="form-check">
                    <input type="checkbox" name="is_active" id="sa_active" value="1" checked class="form-check-input">
                    <label class="form-check-label" for="sa_active">Active</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function resetSubjectAreaForm() {
    document.getElementById('sa_id').value = '';
    document.getElementById('sa_name').value = '';
    document.getElementById('sa_topic').value = '';
    document.getElementById('sa_search_query').value = '';
    document.getElementById('sa_description').value = '';
    document.getElementById('sa_sort').value = '100';
    document.getElementById('sa_active').checked = true;
}
function editSubjectArea(id) {
    fetch('{{ url('admin/kpi/subject-areas/get') }}?id=' + id)
        .then(r => r.json())
        .then(data => {
            if (!data.success) return;
            const a = data.subject_area;
            document.getElementById('sa_id').value = a.id;
            document.getElementById('sa_name').value = a.name || '';
            document.getElementById('sa_topic').value = a.owid_topic || '';
            document.getElementById('sa_search_query').value = a.owid_search_query || '';
            document.getElementById('sa_description').value = a.description || '';
            document.getElementById('sa_sort').value = a.sort_order || 100;
            document.getElementById('sa_active').checked = !!a.is_active;
            $('#subjectAreaModal').modal('show');
        });
}
</script>
@endsection
