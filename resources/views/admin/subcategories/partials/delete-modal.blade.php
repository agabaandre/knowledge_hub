<div class="modal" id="delete-subcategory-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Deleting this category requires selecting a replacement category.</p>
                <p class="mb-2">
                    Existing subcategories and publications under
                    <strong id="deleteCategoryName">selected category</strong>
                    will be mapped before deletion.
                </p>
                <div class="form-group mb-0">
                    <label for="replacement_category_id">Replacement category <span class="text-danger">*</span></label>
                    <select id="replacement_category_id" class="form-control">
                        <option value="">Select replacement category</option>
                        @foreach(($allCategoriesForMapping ?? []) as $catOption)
                            <option value="{{ $catOption->id }}">{{ $catOption->category_name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Subcategories and publications will be moved first.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">Yes, Delete</button>
            </div>
        </div>
    </div>
</div>
