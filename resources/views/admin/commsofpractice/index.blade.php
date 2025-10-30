@extends('admin.layouts.tabular')

@section('styles')
 @include('common.table')
 @include('partials.general.summernote')
@endsection

@section('content')
<!-- PAGE-HEADER -->
<div class="page-header">
    <h1 class="page-title">Communities of Practice</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Dropdown Lists</a></li>
            <li class="breadcrumb-item active" aria-current="page">Communities of Practice</li>
        </ol>
    </div>
</div>
<!-- PAGE-HEADER END -->
<div class="row">
	<div class="card col-lg-12">
		<div class="card-header d-flex align-items-center justify-content-between">
			<div></div>
			<div class="text-right">
				<button type="button" class="btn btn-primary btn-sm" onclick="openCreateModal()"><i class="fa fa-plus mr-1"></i>Add Community</button>
				<button type="button" class="btn btn-outline-dark btn-sm" onclick="openEditSelected()"><i class="fa fa-edit mr-1"></i>Edit Selected</button>
			</div>
		</div>
	
		<div class="card-body text-left">

		  {!! $uitable !!}

		</div>

	</div>

    @include('admin.commsofpractice.partials.create-modal')

    @endsection

@section('scripts')
@include('partials.general.summernote')
<script>
$(function(){
    function initSN(){
        var $el = $('#description');
        if ($el.length && !$el.hasClass('summernote-sm')) {
            $el.addClass('summernote-sm');
        }
        // summernote partial will pick it up automatically
    }
    initSN();
    $(document).on('shown.bs.modal', '#create-modal, #addCommunityModal', function(){ initSN(); });

    // Borrowed idea from tags: make edit discoverable from the table itself
    // Bind jqGrid events when grid is ready
    function wireGridShortcuts(){
        var grid = jQuery('#list1');
        if (!grid.length || !grid.jqGrid) return false;
        try {
            grid.jqGrid('setGridParam', {
                ondblClickRow: function(id){
                    grid.jqGrid('setSelection', id);
                    openEditSelected();
                }
            });
        } catch(e) { /* no-op */ }
        return true;
    }

    // try immediately, then after a brief delay in case grid renders late
    if (!wireGridShortcuts()) { setTimeout(wireGridShortcuts, 600); }
});

function openCreateModal(){
    $('#id').val('');
    $('#community_name').val('');
    $('#description').val('');
    if ($('#description').data('summernote')) { $('#description').summernote('code',''); }
    $('#create-modal').modal('show');
}

function openEditSelected(){
    var grid = jQuery('#list1');
    if (!grid.length || !grid.jqGrid) { return openCreateModal(); }
    var selId = grid.jqGrid('getGridParam','selrow');
    if (!selId) {
        var arr = grid.jqGrid('getGridParam','selarrrow') || [];
        if (arr.length) selId = arr[0];
    }
    if (!selId) { alert('Please select a row first.'); return; }
    // Prefer fetching fresh data from server to avoid jqGrid field name mismatches
    $.getJSON('{{ url('admin/commsofpractice/get') }}', { id: selId })
      .done(function(resp){
        if(resp && resp.status==='success'){
            var d = resp.data || {};
            $('#id').val(d.id || selId);
            $('#community_name').val(d.community_name || '');
            if ($('#description').data('summernote')) { $('#description').summernote('code', d.description || ''); } else { $('#description').val(d.description || ''); }
            $('#create-modal').modal('show');
        } else {
            fallbackFromGrid();
        }
      })
      .fail(fallbackFromGrid);

    function fallbackFromGrid(){
        try{
            var row = grid.jqGrid('getRowData', selId) || {};
            $('#id').val(row.id || selId);
            $('#community_name').val(row.community_name || row.Community || row.community || '');
            var desc = row.description || row.Description || '';
            if ($('#description').data('summernote')) { $('#description').summernote('code', desc); } else { $('#description').val(desc); }
            $('#create-modal').modal('show');
        }catch(e){
            alert('Could not load record for edit.');
        }
    }
}
</script>
@endsection