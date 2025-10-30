@extends('admin.layouts.tabular')

@section('styles')
 @include('common.table')
 @include('partials.general.summernote')
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
            <strong>{{ $title ?? 'Tools' }}</strong>
            <button class="btn btn-primary btn-sm" onclick="openCreateTool()"><i class="fa fa-plus mr-1"></i>Add Tool</button>
        </div>
        <div class="af-card-body">
            <table class="table table-striped table-hover table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Tool</th>
                        <th>Category</th>
                        <th>URL</th>
                        <th style="width:180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tools as $idx => $tool)
                    <tr>
                        <td>{{ $tools->firstItem() + $idx }}</td>
                        <td>
                            <div class="font-weight-bold">{{ $tool->tool_name }}</div>
                            <div class="text-muted small">{!! truncate(strip_tags($tool->tool_desc), 120) !!}</div>
                        </td>
                        <td>{{ $tool->category->category_name ?? '-' }}</td>
                        <td>
                            @if($tool->tool_url)
                            <a href="{{ $tool->tool_url }}" target="_blank">Open</a>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-outline-dark btn-sm mr-1" onclick="openEditTool({{ $tool->id }})"><i class="fa fa-edit"></i></button>
                            @can('delete_meta_data')
                            <button class="btn btn-outline-danger btn-sm" onclick="confirmDelete({{ $tool->id }})"><i class="fa fa-trash"></i></button>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="py-2">{{ $tools->links() }}</div>
        </div>
    </div>

    @include('admin.tools.partials.create-modal')
    @include('admin.tools.partials.delete-modal')

    @endsection

@section('scripts')
@include('partials.general.summernote')
<script>
let currentId = '';
function openCreateTool(){
    currentId = '';
    $('#toolForm')[0].reset();
    $('#id').val('');
    if($('#tool_desc').data('summernote')) $('#tool_desc').summernote('code','');
    $('#toolModal').modal('show');
}
function openEditTool(id){
    currentId = id;
    const row = @json($tools->items());
    const tool = row.find(x => x.id === id);
    if(!tool){ return; }
    $('#id').val(tool.id);
    $('#tool_name').val(tool.tool_name);
    $('#tool_category_id').val(tool.tool_category_id);
    if($('#tool_desc').data('summernote')) $('#tool_desc').summernote('code', tool.tool_desc || ''); else $('#tool_desc').val(tool.tool_desc||'');
    $('#tool_url').val(tool.tool_url||'');
    $('#toolModal').modal('show');
}
function confirmDelete(id){
    $('#deleteId').val(id);
    $('#deleteModal').modal('show');
}
</script>
@endsection