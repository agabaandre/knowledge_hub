<div class="modal fade" id="delete-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ ui_t('admin_themes.delete_subtheme_title', 'Delete subtheme') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ ui_t('general.close', 'Close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>{{ ui_t('admin_themes.delete_subtheme_intro', 'Deleting this subtheme requires selecting a replacement subtheme.') }}</p>
                <p class="mb-2">
                    {{ ui_t('admin_themes.delete_subtheme_mapped_prefix', 'Publications mapped to') }}
                    <strong id="deleteSubthemeName">{{ ui_t('admin_themes.selected_subtheme', 'selected subtheme') }}</strong>
                    {{ ui_t('admin_themes.delete_subtheme_mapped_suffix', 'will be moved before deletion.') }}
                </p>
                <div class="form-group mb-0">
                    <label for="replacement_subtheme_id">{{ ui_t('admin_themes.replacement_subtheme', 'Replacement subtheme') }} <span class="text-danger">*</span></label>
                    <select id="replacement_subtheme_id" class="form-control no-select2" data-placeholder="{{ ui_t('admin_themes.select_replacement_subtheme', 'Select replacement subtheme') }}">
                        <option value="">{{ ui_t('admin_themes.select_replacement_subtheme', 'Select replacement subtheme') }}</option>
                        @foreach(($allSubthemesForMapping ?? []) as $subthemeOption)
                            <option value="{{ $subthemeOption->id }}">
                                {{ $subthemeOption->description }}@if($subthemeOption->theme) — {{ $subthemeOption->theme->description }}@endif
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">{{ ui_t('admin_themes.replacement_subtheme_help', 'All related publications will be mapped to this subtheme first.') }}</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">{{ ui_t('general.cancel', 'Cancel') }}</button>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteRow()">{{ ui_t('admin_themes.yes_delete_remap', 'Yes, delete and remap') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
    var toDeleteRow = '';
    var toDeleteRowName = '';
    var lmThemes = {
        success: @json(ui_t('admin_themes.success', 'Success')),
        notice: @json(ui_t('admin_themes.notice', 'Notice')),
        pleaseSelectReplacement: @json(ui_t('admin_themes.please_select_replacement_subtheme', 'Please select the subtheme to map the publications to.')),
        pleaseSelectDifferent: @json(ui_t('admin_themes.please_select_different_subtheme', 'Please select a different subtheme.')),
        failedDelete: @json(ui_t('admin_themes.failed_delete_subtheme', 'Failed to delete subtheme.')),
        deleted: @json(ui_t('admin_themes.subtheme_deleted', 'Subtheme deleted.')),
        deletedMapped: @json(ui_t('admin_themes.subtheme_deleted_mapped', ':message Mapped :publications publications.'))
    };

    function showDeleteNotice(message, type = 'info', onClose = null) {
        if (typeof swal === 'function') {
            const result = swal(type === 'success' ? lmThemes.success : lmThemes.notice, message, type);
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
            showDeleteNotice(lmThemes.pleaseSelectReplacement, 'warning');
            return;
        }
        if (String(replacementSubthemeId) === String(toDeleteRow)) {
            showDeleteNotice(lmThemes.pleaseSelectDifferent, 'warning');
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
                    showDeleteNotice(res.message || lmThemes.failedDelete, 'error');
                    return;
                }
                $('#delete-modal').modal('hide');
                const movedPubs = res?.data?.mapped_publications ?? 0;
                const msg = lmThemes.deletedMapped
                    .replace(':message', res.message || lmThemes.deleted)
                    .replace(':publications', String(movedPubs));
                showDeleteNotice(msg, 'success', function () { window.location.reload(); });
            })
            .catch(() => showDeleteNotice(lmThemes.failedDelete, 'error'));
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
