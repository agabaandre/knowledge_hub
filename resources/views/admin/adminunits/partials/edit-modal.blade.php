<div class="modal" id="edit{{$row->id}}">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalToggleLabel">Edit Administrative Unit</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
            </div>
            <form action="{{ url('admin/adminunits/save') }}" enctype="multipart/form-data" method="post" id='filetypes' class='filetypes'>
        
            <div class="modal-body">
                @include('admin.adminunits.partials.form_fields')
            </div>
            <div class="modal-footer">
                <!-- Toogle to second dialog -->
                <button class="btn btn-outline-danger btn-sm submit" type="submits">Update</button>
            </div>
            </form>
        </div>
    </div>
</div>