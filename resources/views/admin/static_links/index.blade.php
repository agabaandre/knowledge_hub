@extends(admin_layout())

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Static Links</h4>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#create-static-link-modal">
                        <i class="fa fa-plus"></i> Add Link
                    </button>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Order</th>
                                <th>Link</th>
                                <th>Open in New Tab</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($links as $link)
                                <tr>
                                    <td>{{ $link->title }}</td>
                                    <td>{{ $link->order }}</td>
                                    <td><a href="{{ $link->link }}" target="{{ $link->open_in_new_tab ? '_blank' : '_self' }}">{{ $link->link }}</a></td>
                                    <td>{{ $link->open_in_new_tab ? 'Yes' : 'No' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info edit-link-btn" data-toggle="modal" data-target="#edit-static-link-modal" data-link='@json($link)'>Edit</button>
                                        <form action="{{ url('admin/static-links/delete/'.$link->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this link?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@include('admin.static_links.partials.create-modal')
@include('admin.static_links.partials.edit-modal')
@endsection

@section('scripts')
<script>
$(document).on('click', '.edit-link-btn', function() {
    var link = $(this).data('link');
    $('#edit_id').val(link.id);
    $('#edit_title').val(link.title);
    $('#edit_order').val(link.order);
    $('#edit_link').val(link.link);
    $('#edit_open_in_new_tab').prop('checked', link.open_in_new_tab == 1);
    $('#edit-static-link-form').attr('action', '/admin/static-links/update/' + link.id);
});
</script>
@endsection 