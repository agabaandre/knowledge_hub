<div class="modal" id="add-subcategory-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.subcategories.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="add_category_name">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="add_category_name" name="category_name" required maxlength="255">
                    </div>
                    <div class="form-group">
                        <label for="add_category_desc">Description</label>
                        <input type="text" class="form-control" id="add_category_desc" name="category_desc" maxlength="500">
                    </div>
                    <div class="form-group">
                        <label for="add_linked_data_categories">Categories</label>
                        <select class="form-control select2" id="add_linked_data_categories" name="linked_data_categories[]" multiple data-placeholder="Select one or more categories">
                            @foreach(($dataCategories ?? []) as $dc)
                                <option value="{{ (int) $dc->id }}" selected>{{ $dc->category_name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">By default all data categories are linked. You can unselect any.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>
