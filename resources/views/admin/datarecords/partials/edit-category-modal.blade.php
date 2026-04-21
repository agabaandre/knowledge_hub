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

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" value="1" name="show_menu" id="edit_show_menu">
                        <label class="form-check-label" for="edit_show_menu">Show on menu</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    (function () {
        $('#edit_category').on('show.bs.modal', function (event) {
            var btn = $(event.relatedTarget);
            if (!btn || !btn.length) {
                return;
            }
            var payload = btn.data('category');
            if (typeof payload === 'string') {
                try {
                    payload = JSON.parse(payload);
                } catch (e) {
                    payload = null;
                }
            }
            if (!payload || !payload.id) {
                return;
            }
            $('#edit_category_id').val(payload.id);
            $('#edit_category_name').val(payload.name || '');
            $('#edit_category_url').val(payload.url_path || '');
            $('#edit_show_menu').prop('checked', !!payload.show_on_menu);
        });
    })();
</script>
