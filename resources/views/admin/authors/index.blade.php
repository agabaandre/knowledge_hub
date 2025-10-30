@extends('admin.layouts.tabular')

@section('styles')
 <style>
    .af-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px}
    .af-card-header{padding:12px 16px;border-bottom:1px solid #e2e8f0;background:#f8fafc}
    .af-card-body{padding:16px}
 </style>
@endsection

@section('content')
<div class="row">
    <div class="card col-lg-12 af-card">
        <div class="af-card-header d-flex align-items-center justify-content-between">
            <strong>Authors</strong>
            <div class="d-flex align-items-center" style="gap:8px;">
                <form class="form-inline" method="get" action="{{ url('admin/authors') }}">
                    <input type="text" name="term" value="{{ request('term') }}" class="form-control form-control-sm" placeholder="Search name...">
                    <button class="btn btn-outline-dark btn-sm ml-1" type="submit"><i class="fa fa-search"></i></button>
                </form>
                <button class="btn btn-primary btn-sm" onclick="openCreate()"><i class="fa fa-plus mr-1"></i>Add Author</button>
            </div>
        </div>
        <div class="af-card-body">
            @include('layouts.partials.alerts')
            <table class="table table-striped table-hover table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Name</th>
                        <th style="width:140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($authors as $idx => $a)
                    <tr>
                        <td>{{ $authors->firstItem() + $idx }}</td>
                        <td>{{ $a->name }}</td>
                        <td>
                            <button class="btn btn-outline-dark btn-sm mr-1" onclick="openEdit({{ $a->id }})"><i class="fa fa-edit"></i></button>
                            <a class="btn btn-outline-danger btn-sm" href="{{ url('admin/authors/delete') }}?id={{ $a->id }}"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="py-2">{{ $authors->links() }}</div>
        </div>
    </div>
</div>

<div class="modal fade" id="authorModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Save Author</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <form method="post" action="{{ url('admin/authors/store') }}">
        @csrf
        <div class="modal-body">
            <input type="hidden" name="id" id="id">
            <div class="form-group">
                <label>Name</label>
                <input type="text" class="form-control" name="name" id="name" required>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
// Auto-hide alerts after a few seconds for a cleaner UX
setTimeout(function(){ $('.alert').fadeOut(); }, 3500);
const rows = @json($authors->items());
function openCreate(){ document.getElementById('id').value=''; document.getElementById('name').value=''; $('#authorModal').modal('show'); }
function openEdit(id){ const a = rows.find(x=>x.id===id); if(!a) return; document.getElementById('id').value=a.id; document.getElementById('name').value=a.name; $('#authorModal').modal('show'); }
</script>
@endsection

 