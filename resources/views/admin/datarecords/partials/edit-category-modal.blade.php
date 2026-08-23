<div class="modal" id="edit_category" tabindex="-1" role="dialog" aria-labelledby="edit_category_title">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ url('admin/datarecords/categories/update') }}" method="post">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="edit_category_title">Edit Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_category_id" value="">

                    <div class="mb-3">
                        <label class="form-label" for="edit_category_name">Category name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_category_name" name="name" required maxlength="255" placeholder="Category name">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="edit_category_url">URL path</label>
                        <input type="text" class="form-control" id="edit_category_url" name="url" maxlength="500" placeholder="URL path">
                    </div>

                    @include('admin.datarecords.partials.category-access-fields', ['prefix' => 'edit'])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('modal-scripts')
<script>
    (function () {
        function fillCategoryEditForm(btn) {
            if (!btn || !btn.length) {
                return;
            }
            $('#edit_category_id').val(btn.attr('data-id') || '');
            $('#edit_category_name').val(btn.attr('data-name') || '');
            $('#edit_category_url').val(btn.attr('data-url') || '');
            $('#edit_show_menu').prop('checked', btn.attr('data-show-menu') === '1');
            $('#edit_is_special').prop('checked', btn.attr('data-special') === '1');
            $('#edit_is_restricted').prop('checked', btn.attr('data-restricted') === '1');
            $('#edit_required_permission').val(btn.attr('data-permission') || '');
        }

        $(document).on('click', '.js-edit-category', function () {
            fillCategoryEditForm($(this));
        });

        $('#edit_category').on('show.bs.modal', function (event) {
            fillCategoryEditForm($(event.relatedTarget));
        });
    })();
</script>
@endpush
