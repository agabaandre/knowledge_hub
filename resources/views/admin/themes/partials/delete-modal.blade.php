<div class="modal fade" id="delete-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete thematic area</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Deleting this thematic area requires selecting a replacement theme.</p>
                <p class="mb-2">
                    All related subthemes and publications will be mapped to:
                    <strong id="deleteThemeName">selected thematic area</strong>.
                </p>
                <div class="form-group mb-0">
                    <label for="replacement_theme_id">Replacement thematic area <span class="text-danger">*</span></label>
                    <select id="replacement_theme_id" class="form-control no-select2" data-placeholder="Select replacement thematic area">
                        <option value="">Select replacement thematic area</option>
                        @foreach(($allThemesForMapping ?? []) as $themeOption)
                            <option value="{{ $themeOption->id }}">{{ $themeOption->description }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Subthemes and related publications will be moved before delete.</small>
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
        const replacementThemeId = $('#replacement_theme_id').val();
        if (!replacementThemeId) {
            showDeleteNotice('Please select the thematic area to map the data to.', 'warning');
            return;
        }
        if (String(replacementThemeId) === String(toDeleteRow)) {
            showDeleteNotice('Please select a different thematic area.', 'warning');
            return;
        }

        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        fetch(@json(url('admin/themes/delete')), {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                id: Number(toDeleteRow),
                replacement_theme_id: Number(replacementThemeId)
            })
        })
            .then(res => res.json())
            .then(res => {
                if (res.status !== 'success') {
                    showDeleteNotice(res.message || 'Failed to delete thematic area.', 'error');
                    return;
                }
                $('#delete-modal').modal('hide');
                const movedSubs = res?.data?.mapped_subthemes ?? 0;
                const movedPubs = res?.data?.mapped_publications ?? 0;
                showDeleteNotice(
                    `${res.message || 'Theme deleted.'} Mapped ${movedSubs} subthemes and ${movedPubs} publications.`,
                    'success',
                    function () { window.location.reload(); }
                );
            })
            .catch(() => showDeleteNotice('Failed to delete thematic area.', 'error'));
    }

    function openDeleteModal(row = 0, rowName = '') {
        toDeleteRow = row;
        toDeleteRowName = rowName || '';
        const titleEl = document.getElementById('deleteThemeName');
        if (titleEl) {
            titleEl.textContent = toDeleteRowName || `ID ${toDeleteRow}`;
        }

        const $modal = $('#delete-modal');
        const $select = $('#replacement_theme_id');
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
