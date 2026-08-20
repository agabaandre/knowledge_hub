<div class="modal fade" id="delete-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete tag</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Deleting this tag requires choosing a replacement tag.</p>
                <p class="mb-2">
                    Publications, forums, events, and communities that use
                    <strong id="deleteTagName">this tag</strong>
                    will be remapped to the replacement before the tag is removed.
                </p>
                <div class="form-group mb-0">
                    <label for="replacement_tag_id">Replacement tag <span class="text-danger">*</span></label>
                    <select id="replacement_tag_id" class="form-control no-select2" data-placeholder="Search and select replacement tag">
                        <option value="">Select replacement tag</option>
                        @foreach(($allTagsForMapping ?? []) as $tagOption)
                            <option value="{{ $tagOption->id }}">{{ $tagOption->tag_text }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Type to search. Duplicate links after merge are removed automatically.</small>
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
        const replacementTagId = $('#replacement_tag_id').val();
        if (!replacementTagId) {
            showDeleteNotice('Please select the tag to map content to.', 'warning');
            return;
        }
        if (String(replacementTagId) === String(toDeleteRow)) {
            showDeleteNotice('Please select a different tag.', 'warning');
            return;
        }

        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        fetch(@json(url('admin/tags/delete')), {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                id: Number(toDeleteRow),
                replacement_tag_id: Number(replacementTagId)
            })
        })
            .then(res => res.json())
            .then(res => {
                if (res.status !== 'success') {
                    showDeleteNotice(res.message || 'Failed to delete tag.', 'error');
                    return;
                }
                $('#delete-modal').modal('hide');
                const d = res?.data ?? {};
                const parts = [
                    `Publications: ${d.moved_publication_tags ?? 0} remapped`,
                    `duplicates removed: ${d.removed_publication_tag_duplicates ?? 0}`,
                    `Forums: ${d.moved_forum_tags ?? 0}`,
                    `Events: ${d.moved_event_tags ?? 0}`,
                    `Communities: ${d.moved_community_tags ?? 0}`,
                ];
                showDeleteNotice(
                    (res.message || 'Tag deleted.') + ' ' + parts.join('; ') + '.',
                    'success',
                    function () { window.location.reload(); }
                );
            })
            .catch(() => showDeleteNotice('Failed to delete tag.', 'error'));
    }

    function openDeleteModal(row = 0, rowName = '') {
        toDeleteRow = row;
        toDeleteRowName = rowName || '';
        const titleEl = document.getElementById('deleteTagName');
        if (titleEl) {
            titleEl.textContent = toDeleteRowName || `ID ${toDeleteRow}`;
        }

        const $modal = $('#delete-modal');
        const $select = $('#replacement_tag_id');
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
