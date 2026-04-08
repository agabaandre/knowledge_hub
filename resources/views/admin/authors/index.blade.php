@extends(admin_layout('tabular'))

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
                @canany(['delete_publication_metadata', 'update_sources', 'add_authors'])
                <button class="btn btn-primary btn-sm" onclick="openCreate()"><i class="fa fa-plus mr-1"></i>Add Author</button>
                @endcanany
                @can('delete_publication_metadata')
                <button type="button" class="btn btn-outline-secondary btn-sm" data-toggle="modal" data-target="#mergeAuthorsModal"><i class="fa fa-compress mr-1"></i>Merge authors</button>
                @endcan
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
                            @canany(['delete_publication_metadata', 'update_sources', 'add_authors'])
                            <button type="button" class="btn btn-outline-dark btn-sm mr-1" data-author-id="{{ $a->id }}" data-author-name="{{ e($a->name) }}" onclick="openAuthorEdit(this)"><i class="fa fa-edit"></i></button>
                            @endcanany
                            @can('delete_publication_metadata')
                            <button type="button" class="btn btn-outline-danger btn-sm"
                                data-delete-id="{{ $a->id }}"
                                data-delete-name="{{ e($a->name) }}"
                                onclick="openDeleteAuthorModal(this); return false;"><i class="fa fa-trash"></i></button>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="py-2">{{ $authors->links() }}</div>
        </div>
    </div>
</div>

@canany(['delete_publication_metadata', 'update_sources', 'add_authors'])
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
@endcanany

@can('delete_publication_metadata')
<div class="modal fade" id="mergeAuthorsModal" tabindex="-1" role="dialog" aria-labelledby="mergeAuthorsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="mergeAuthorsModalLabel">Merge authors</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <form method="post" action="{{ url('admin/authors/merge') }}" id="mergeAuthorsForm" onsubmit="return confirmMerge();">
        @csrf
        <div class="modal-body">
          <p class="text-muted small">Choose the <strong>author to keep</strong>, then select one or more <strong>duplicate authors</strong> to merge into it. All publications (and related summaries or staging records) linked to the duplicates are reassigned to the kept author. User accounts that were linked to duplicate authors are updated to the kept author (forum activity stays with those users). Duplicate author rows are deleted after reassignment.</p>
          <div class="form-group">
            <label for="merge_keep_id"><strong>Primary author</strong> (keep this record)</label>
            <select name="keep_id" id="merge_keep_id" class="form-control" required>
              <option value="">— Select —</option>
              @foreach($authorsForMerge as $opt)
                <option value="{{ $opt->id }}">{{ $opt->name }} (ID {{ $opt->id }})</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label for="merge_merge_ids"><strong>Authors to merge</strong> (will be removed after reassignment)</label>
            <select name="merge_ids[]" id="merge_merge_ids" class="form-control" multiple size="10" required>
              @foreach($authorsForMerge as $opt)
                <option value="{{ $opt->id }}">{{ $opt->name }} (ID {{ $opt->id }})</option>
              @endforeach
            </select>
            <small class="form-text text-muted">Hold Ctrl (Windows) or ⌘ (Mac) to select multiple.</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-warning">Merge</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="deleteAuthorModal" tabindex="-1" role="dialog" aria-labelledby="deleteAuthorModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header border-danger bg-light">
        <h5 class="modal-title text-danger" id="deleteAuthorModalLabel"><i class="fa fa-exclamation-triangle mr-1"></i> Delete author</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <form method="post" action="{{ url('admin/authors/delete') }}" id="deleteAuthorForm">
        @csrf
        <input type="hidden" name="id" id="delete_author_id" value="">
        <div class="modal-body">
          <p class="mb-2">You are about to delete this author record:</p>
          <ul class="list-unstyled mb-0 pl-0">
            <li><strong>Name:</strong> <span id="delete_author_name_display" class="text-dark"></span></li>
            <li><strong>ID:</strong> <span id="delete_author_id_display" class="text-monospace text-dark"></span></li>
          </ul>
          <p class="text-muted small mt-3 mb-0">This cannot be undone. If publications or user accounts still reference this author, the delete may fail—use <strong>Merge authors</strong> to reassign first.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Delete author</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endcan
@endsection

@section('scripts')
<script>
// Auto-hide alerts after a few seconds for a cleaner UX
setTimeout(function(){ $('.alert').fadeOut(); }, 3500);
function openCreate(){ document.getElementById('id').value=''; document.getElementById('name').value=''; $('#authorModal').modal('show'); }
function openAuthorEdit(btn){
  var id = btn.getAttribute('data-author-id');
  var name = btn.getAttribute('data-author-name') || '';
  document.getElementById('id').value = id;
  document.getElementById('name').value = name;
  $('#authorModal').modal('show');
}
function openDeleteAuthorModal(btn){
  var id = btn.getAttribute('data-delete-id');
  var name = btn.getAttribute('data-delete-name') || '';
  document.getElementById('delete_author_id').value = id;
  document.getElementById('delete_author_name_display').textContent = name;
  document.getElementById('delete_author_id_display').textContent = id;
  $('#deleteAuthorModal').modal('show');
}
function confirmMerge(){
  var keep = document.getElementById('merge_keep_id');
  var sel = document.getElementById('merge_merge_ids');
  if (!keep || !sel) return true;
  var keepId = parseInt(keep.value, 10);
  var merged = Array.prototype.slice.call(sel.selectedOptions).map(function(o){ return parseInt(o.value, 10); });
  if (!keepId || merged.length === 0) { alert('Select a primary author and at least one author to merge.'); return false; }
  if (merged.indexOf(keepId) !== -1) { alert('The primary author cannot be in the merge list.'); return false; }
  return confirm('Merge ' + merged.length + ' author(s) into \"' + keep.options[keep.selectedIndex].text + '\"? This cannot be undone.');
}
</script>
@endsection

 