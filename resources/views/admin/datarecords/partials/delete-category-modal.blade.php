<!-- First modal dialog -->
<div class="modal" id="delete-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalToggleLabel">Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
            </div>
            <div class="modal-body">
                <p>Deleting this category requires selecting a replacement category.</p>
                <p class="mb-2">
                    Existing data subcategories and records under
                    <strong id="deleteDataCategoryName">selected category</strong>
                    will be mapped before deletion.
                </p>
                <div class="form-group mb-0">
                    <label for="replacement_data_category_id">Replacement category <span class="text-danger">*</span></label>
                    <select id="replacement_data_category_id" class="form-control">
                        <option value="">Select replacement category</option>
                        @foreach(($allCategoriesForMapping ?? []) as $catOption)
                            <option value="{{ $catOption->id }}">{{ $catOption->category_name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Subcategories and records will be moved first.</small>
                </div>
            </div>
            <div class="modal-footer">
                <!-- Toogle to second dialog -->
                <button class="btn btn-outline-danger btn-sm edit"
                        onclick="deleteRow()">Yes Delete</button>
            </div>
        </div>
    </div>
</div>

    <script>
        var toDeleteRow = '';
        var toDeleteRowName = '';
        function showDeleteNotice(message, type = 'info', onClose = null) {
            if (typeof swal === 'function') {
                const result = swal(type === 'success' ? 'Success' : 'Notice', message, type);
                if (result && typeof result.then === 'function') {
                    result.then(function () {
                        if (typeof onClose === 'function') onClose();
                    });
                } else if (typeof onClose === 'function') {
                    setTimeout(onClose, 300);
                }
                return;
            }
            alert(message);
            if (typeof onClose === 'function') onClose();
        }
        function deleteRow () {
            const replacementCategoryId = (document.getElementById('replacement_data_category_id') || {}).value;
            if (!replacementCategoryId) {
                showDeleteNotice('Please select the category to map data to.', 'warning');
                return;
            }
            if (String(replacementCategoryId) === String(toDeleteRow)) {
                showDeleteNotice('Please select a different category.', 'warning');
                return;
            }
            let url = `{{ url('admin/datarecords/categories/delete')}}?id=${toDeleteRow}&replacement_category_id=${replacementCategoryId}`;

                fetch(url, {headers: {'Accept': 'application/json'}})
                .then(res => res.json())
                .then(res => {
                    if (res.status !== 'success') {
                        showDeleteNotice(res.message || 'Failed to delete category.', 'error');
                        return;
                    }
                    $('#delete-modal').modal('hide');
                    const movedSubs = res?.data?.moved_subcategories ?? 0;
                    const movedRecords = res?.data?.moved_records ?? 0;
                    showDeleteNotice(
                        `${res.message || 'Category deleted.'} Mapped ${movedSubs} subcategories and ${movedRecords} records.`,
                        'success',
                        function () { window.location.reload(); }
                    );
                })
                .catch(() => showDeleteNotice('Failed to delete category.', 'error'));
        }

        function openDeleteModal (row = 0, rowName = '') {
            toDeleteRow = row;
            toDeleteRowName = rowName || '';
            const titleEl = document.getElementById('deleteDataCategoryName');
            if (titleEl) {
                titleEl.textContent = toDeleteRowName || `ID ${toDeleteRow}`;
            }
            const replacementSelect = document.getElementById('replacement_data_category_id');
            if (replacementSelect) {
                replacementSelect.value = '';
                Array.from(replacementSelect.options).forEach((opt) => {
                    if (String(opt.value) === String(toDeleteRow)) {
                        opt.disabled = true;
                    } else if (opt.value !== '') {
                        opt.disabled = false;
                    }
                });
            }
            $('#delete-modal').modal('show');
        }
    </script>