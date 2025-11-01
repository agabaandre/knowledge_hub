@extends('admin.layouts.tabular')

@section('styles')
 @include('common.table')
 @include('partials.general.summernote')
 <style>
    .af-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px}
    .af-card .card-header{padding:12px 16px;border-bottom:1px solid #e2e8f0;background:#f8fafc}
    .af-card .card-body{padding:16px}
 </style>
@endsection

@section('content')
<div class="row">
    <div class="card col-lg-12 af-card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <strong>Communities of Practice</strong>
            <div>
                <button type="button" class="btn btn-primary btn-sm" onclick="openCreateModal()"><i class="fa fa-plus mr-1"></i>Add Community</button>
            </div>
        </div>
        <div class="card-body text-left">
            @include('layouts.partials.alerts')
            <table class="table table-striped table-hover table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Community</th>
                        <th>Description</th>
                        <th style="width:140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($communities as $idx => $c)
                    <tr>
                        <td>{{ $communities->firstItem() + $idx }}</td>
                        <td class="font-weight-600">{{ $c->community_name }}</td>
                        <td class="text-muted">{!! truncate(strip_tags($c->description), 140) !!}</td>
                        <td>
                            <button class="btn btn-outline-dark btn-sm mr-1" onclick="openEditCommunity({{ $c->id }})"><i class="fa fa-edit"></i></button>
                            @can('delete_publication_metadata')
                            <button class="btn btn-outline-danger btn-sm" onclick="openDeleteModal({{ $c->id }})"><i class="fa fa-trash"></i></button>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="py-2">{{ $communities->links() }}</div>
        </div>
    </div>

    @include('admin.commsofpractice.partials.create-modal')
    @include('admin.commsofpractice.partials.delete-modal')

    @endsection

@section('scripts')
@include('partials.general.summernote')
<script>
$(function(){
    function initSN(){
        var $el = $('#description');
        if ($el.length && !$el.hasClass('summernote-sm')) { $el.addClass('summernote-sm'); }
    }
    initSN();
    $(document).on('shown.bs.modal', '#create-modal', function(){ initSN(); });
});

function openCreateModal(){
    $('#id').val('');
    $('#community_name').val('');
    if ($('#description').data('summernote')) { $('#description').summernote('code',''); } else { $('#description').val(''); }
    $('#create-modal').modal('show');
}

function openEditCommunity(id){
    const rows = @json($communities->items());
    const item = rows.find(x => x.id === id);
    if (!item) { return; }
    $('#id').val(item.id);
    $('#community_name').val(item.community_name || '');
    if ($('#description').data('summernote')) { $('#description').summernote('code', item.description || ''); } else { $('#description').val(item.description || ''); }
    $('#create-modal').modal('show');
}
</script>
@endsection