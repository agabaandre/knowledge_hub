<div class="modal" id="edit{{ $row->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Administrative Unit</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ url('admin/adminunits/save') }}" enctype="multipart/form-data" method="post" id="edit-adminunit-form-{{ $row->id }}" class="filetypes">
                <div class="modal-body">
                    @include('admin.adminunits.partials.form_fields', ['row' => $row])
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary btn-sm" data-dismiss="modal" type="button">Cancel</button>
                    <button class="btn btn-primary btn-sm" type="submit">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
