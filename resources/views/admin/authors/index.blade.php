@extends(admin_layout('tabular'))

@section('styles')
 <style>
    .af-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px}
    .af-card-header{padding:12px 16px;border-bottom:1px solid #e2e8f0;background:#f8fafc}
    .af-card-body{padding:16px}
    .authors-admin-table td,.authors-admin-table th{vertical-align:middle}
    .authors-admin-table .col-actions{white-space:nowrap;width:1%}
    .authors-admin-table .author-name{word-break:break-word}
    .authors-admin-table .text-placeholder{color:#94a3b8;font-style:italic}
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
            <table class="table table-striped table-hover table-bordered authors-admin-table mb-0">
                <thead class="thead-light">
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Name</th>
                        <th class="col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($authors as $idx => $a)
                    @php
                        $displayName = trim((string) ($a->name ?? ''));
                        $labelForAttrs = $displayName !== '' ? $displayName : ('Author #'.$a->id);
                    @endphp
                    <tr>
                        <td>{{ $authors->firstItem() + $idx }}</td>
                        <td class="author-name">
                            @if($displayName !== '')
                                {{ $displayName }}
                            @else
                                <span class="text-placeholder" title="No name in database">Untitled source</span>
                                <span class="text-muted small">(ID {{ $a->id }})</span>
                            @endif
                        </td>
                        <td class="col-actions">
                            @canany(['delete_publication_metadata', 'update_sources', 'add_authors'])
                            <button type="button" class="btn btn-outline-dark btn-sm mr-1" data-author-id="{{ $a->id }}" data-author-name="{{ e($labelForAttrs) }}" onclick="openAuthorEdit(this)"><i class="fa fa-edit"></i></button>
                            @endcanany
                            @can('delete_publication_metadata')
                            <button type="button" class="btn btn-outline-danger btn-sm"
                                data-delete-id="{{ $a->id }}"
                                data-delete-name="{{ e($labelForAttrs) }}"
                                onclick="openDeleteAuthorModal(this); return false;"><i class="fa fa-trash"></i></button>
                            @endcan
                            @unless(auth()->user()->can('delete_publication_metadata') || auth()->user()->can('update_sources') || auth()->user()->can('add_authors'))
                                <span class="text-muted small">—</span>
                            @endunless
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
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="authorModalTitle">Save author</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <form method="post" action="{{ url('admin/authors/store') }}" id="authorForm">
        @csrf
        <div class="modal-body" style="max-height:70vh;overflow-y:auto;">
            <input type="hidden" name="id" id="id">
            <div id="authorTimestamps" class="small text-muted mb-3 d-none border-bottom pb-2">
              <div><strong>ID:</strong> <span id="author_id_readonly">—</span></div>
              <div><strong>Created:</strong> <span id="author_created_at">—</span></div>
              <div><strong>Updated:</strong> <span id="author_updated_at">—</span></div>
            </div>
            <div class="form-group">
                <label for="name">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="name" id="name" maxlength="100" required autocomplete="off">
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="icon">Icon (Font Awesome class)</label>
                <input type="text" class="form-control text-monospace" name="icon" id="icon" maxlength="30" placeholder="fa fa-archive">
                <small class="form-text text-muted">Default: <code>fa fa-archive</code></small>
              </div>
              <div class="form-group col-md-6">
                <label for="is_organsiation">Organization source</label>
                <select class="form-control" name="is_organsiation" id="is_organsiation">
                  <option value="Yes">Yes</option>
                  <option value="No">No</option>
                </select>
                <small class="form-text text-muted">Column <code>is_organsiation</code> in the database.</small>
              </div>
            </div>
            <div class="form-group">
              <label for="address">Address</label>
              <input type="text" class="form-control" name="address" id="address" maxlength="200" autocomplete="street-address">
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="telephone">Telephone</label>
                <input type="text" class="form-control" name="telephone" id="telephone" maxlength="13" autocomplete="tel">
              </div>
              <div class="form-group col-md-6">
                <label for="email">Email</label>
                <input type="text" class="form-control" name="email" id="email" maxlength="20" autocomplete="email">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="orcid">ORCID</label>
                <input type="text" class="form-control" name="orcid" id="orcid" maxlength="19" placeholder="Optional">
              </div>
              <div class="form-group col-md-6">
                <label for="logo">Logo filename</label>
                <input type="text" class="form-control" name="logo" id="logo" maxlength="100" placeholder="author.png">
                <small class="form-text text-muted">Default: <code>author.png</code></small>
              </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary" id="authorFormSubmit">Save</button>
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
var authorsAdminFetchUrl = "{{ url('admin/authors') }}";
function authorFormDefaults(){
  var s = document.getElementById('is_organsiation');
  for (var i = s.options.length - 1; i >= 0; i--) {
    if (s.options[i].textContent.indexOf('(legacy)') !== -1) { s.remove(i); }
  }
  document.getElementById('id').value = '';
  document.getElementById('name').value = '';
  document.getElementById('icon').value = 'fa fa-archive';
  document.getElementById('is_organsiation').value = 'Yes';
  document.getElementById('address').value = '';
  document.getElementById('telephone').value = '';
  document.getElementById('email').value = '';
  document.getElementById('orcid').value = '';
  document.getElementById('logo').value = 'author.png';
  document.getElementById('authorTimestamps').classList.add('d-none');
  document.getElementById('authorModalTitle').textContent = 'Add author';
}
function formatIsoLocal(iso){
  if (!iso) return '—';
  try { var d = new Date(iso); return isNaN(d.getTime()) ? iso : d.toLocaleString(); } catch (e) { return iso; }
}
function setOrgansiationSelect(raw){
  var s = document.getElementById('is_organsiation');
  var v = (raw === null || raw === undefined) ? 'Yes' : String(raw).trim();
  if (v === '0' || v.toLowerCase() === 'no' || v === 'false') { s.value = 'No'; return; }
  if (v === '1' || v.toLowerCase() === 'yes' || v === 'true' || v === '') { s.value = 'Yes'; return; }
  if (v === 'Yes' || v === 'No') { s.value = v; return; }
  var opt = document.createElement('option');
  opt.value = v;
  opt.textContent = v + ' (legacy)';
  opt.selected = true;
  s.appendChild(opt);
}
// Auto-hide alerts after a few seconds for a cleaner UX
setTimeout(function(){ $('.alert').fadeOut(); }, 3500);
function openCreate(){
  authorFormDefaults();
  $('#authorModal').modal('show');
}
function openAuthorEdit(btn){
  var id = btn.getAttribute('data-author-id');
  if (!id) return;
  authorFormDefaults();
  document.getElementById('authorModalTitle').textContent = 'Edit author';
  document.getElementById('authorTimestamps').classList.remove('d-none');
  document.getElementById('author_id_readonly').textContent = id;
  document.getElementById('author_created_at').textContent = '…';
  document.getElementById('author_updated_at').textContent = '…';
  $('#authorModal').modal('show');
  fetch(authorsAdminFetchUrl + '/' + encodeURIComponent(id), {
    credentials: 'same-origin',
    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
  }).then(function(r){
    if (!r.ok) throw new Error('HTTP ' + r.status);
    return r.json();
  }).then(function(data){
    document.getElementById('id').value = data.id;
    document.getElementById('name').value = data.name || '';
    document.getElementById('icon').value = data.icon || 'fa fa-archive';
    setOrgansiationSelect(data.is_organsiation);
    document.getElementById('address').value = data.address || '';
    document.getElementById('telephone').value = data.telephone || '';
    document.getElementById('email').value = data.email || '';
    document.getElementById('orcid').value = data.orcid || '';
    document.getElementById('logo').value = data.logo || 'author.png';
    document.getElementById('author_created_at').textContent = formatIsoLocal(data.created_at);
    document.getElementById('author_updated_at').textContent = formatIsoLocal(data.updated_at);
  }).catch(function(){
    alert('Could not load author details. Please refresh and try again.');
    $('#authorModal').modal('hide');
  });
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

 