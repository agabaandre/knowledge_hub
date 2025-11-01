<!-- Create/Edit Asset Type Modal -->
<div class="modal fade" id="create-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title">Create Asset Type</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form action="{{ url('admin/assettypes/save') }}" method="post" id="assetTypeForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    
                    <div class="form-group">
                        <label class="form-label" for="type_name">Type Name <span class="text-danger">*</span></label>
                        <input type="text" placeholder="Enter Type Name" class="form-control" id="type_name" name="type_name" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="type_desc">Description</label>
                        <textarea placeholder="Enter Description (Optional)" rows="4" class="form-control" id="type_desc" name="type_desc"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary btn-sm" data-dismiss="modal" type="button">Cancel</button>
                    <button class="btn btn-primary btn-sm" type="submit">Save Asset Type</button>
                </div>
            </form>
        </div>
    </div>
</div>

