<div class="modal" id="edit-subcategory-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editSubcategoryForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_category_name">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_category_name" name="category_name" required maxlength="255">
                    </div>
                    <div class="form-group">
                        <label for="edit_category_desc">Description</label>
                        <input type="text" class="form-control" id="edit_category_desc" name="category_desc" maxlength="500">
                    </div>
                    <div class="form-group">
                        <label for="edit_linked_data_categories">Linked Data Categories</label>
                        <select class="form-control select2" id="edit_linked_data_categories" name="linked_data_categories[]" multiple data-placeholder="Select one or more categories">
                            @foreach(($dataCategories ?? []) as $dc)
                                <option value="{{ (int) $dc->id }}">{{ $dc->category_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('modal-scripts')
<script>
(function() {
    var baseUrl = "{{ url('admin/subcategories') }}";
    $('#edit-subcategory-modal').on('show.bs.modal', function(event) {
        var btn = $(event.relatedTarget);
        var id = btn.data('id');
        $('#editSubcategoryForm').attr('action', baseUrl + '/' + id);
        $('#edit_category_name').val(btn.data('category_name'));
        $('#edit_category_desc').val(btn.data('category_desc') || '');
        var linked = btn.data('linked_categories') || [];
        linked = Array.isArray(linked) ? linked.map(String) : [];
        $('#edit_linked_data_categories option').prop('selected', false);
        linked.forEach(function (id) {
            $('#edit_linked_data_categories option[value="' + id + '"]').prop('selected', true);
        });
        $('#edit_linked_data_categories').trigger('change');
    });
})();
</script>
@endpush
