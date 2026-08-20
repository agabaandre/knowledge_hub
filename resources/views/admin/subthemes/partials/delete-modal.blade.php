<div class="modal fade" id="delete-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete subtheme</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Deleting this subtheme requires selecting a replacement subtheme.</p>
                <p class="mb-2">
                    Publications mapped to
                    <strong id="deleteSubthemeName">selected subtheme</strong>
                    will be moved before deletion.
                </p>
                <div class="form-group mb-0">
                    <label for="replacement_subtheme_id">Replacement subtheme <span class="text-danger">*</span></label>
                    <select id="replacement_subtheme_id" class="form-control no-select2" data-placeholder="Select replacement subtheme">
                        <option value="">Select replacement subtheme</option>
                        @foreach(($allSubthemesForMapping ?? []) as $subthemeOption)
                            <option value="{{ $subthemeOption->id }}">
                                {{ $subthemeOption->description }}@if($subthemeOption->theme) — {{ $subthemeOption->theme->description }}@endif
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">All related publications will be mapped to this subtheme first.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteRow()">Yes, delete and remap</button>
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

    function deleteRow() {
        const replacementSubthemeId = $('#replacement_subtheme_id').val();
        if (!replacementSubthemeId) {
            showDeleteNotice('Please select the subtheme to map the publications to.', 'warning');
            return;
        }
        if (String(replacementSubthemeId) === String(toDeleteRow)) {
            showDeleteNotice('Please select a different subtheme.', 'warning');
            return;
        }

        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        fetch(@json(url('admin/subthemes/delete')), {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                id: Number(toDeleteRow),
                replacement_subtheme_id: Number(replacementSubthemeId)
            })
        })
            .then(res => res.json())
            .then(res => {
                if (res.status !== 'success') {
                    showDeleteNotice(res.message || 'Failed to delete subtheme.', 'error');
                    return;
                }
                $('#delete-modal').modal('hide');
                const movedPubs = res?.data?.mapped_publications ?? 0;
                showDeleteNotice(
                    `${res.message || 'Subtheme deleted.'} Mapped ${movedPubs} publications.`,
                    'success',
                    function () { window.location.reload(); }
                );
            })
            .catch(() => showDeleteNotice('Failed to delete subtheme.', 'error'));
    }

    function openDeleteModal(row = 0, rowName = '') {
        toDeleteRow = row;
        toDeleteRowName = rowName || '';
        const titleEl = document.getElementById('deleteSubthemeName');
        if (titleEl) {
            titleEl.textContent = toDeleteRowName || `ID ${toDeleteRow}`;
        }

        const $modal = $('#delete-modal');
        const $select = $('#replacement_subtheme_id');
        $select.find('option').prop('disabled', false);
        $select.find('option').filter(function () {
            return String(this.value) === String(toDeleteRow);
        }).prop('disabled', true);
        $select.val('');

        if (typeof window.khInitModalSelect2 === 'function') {
            window.khInitModalSelect2($select, $modal);
            $select.val(null).trigger('change');
        }

        $modal.modal('show');
    }
</script>
