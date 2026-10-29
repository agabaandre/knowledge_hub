<div class="modal fade" id="delete-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ ui_t('admin_themes.delete_theme_title', 'Delete thematic area') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ ui_t('general.close', 'Close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>{{ ui_t('admin_themes.delete_theme_intro', 'Deleting this thematic area requires selecting a replacement theme.') }}</p>
                <p class="mb-2">
                    {{ ui_t('admin_themes.delete_theme_mapped_to', 'All related subthemes and publications will be mapped to:') }}
                    <strong id="deleteThemeName">{{ ui_t('admin_themes.selected_theme', 'selected thematic area') }}</strong>.
                </p>
                <div class="form-group mb-0">
                    <label for="replacement_theme_id">{{ ui_t('admin_themes.replacement_theme', 'Replacement thematic area') }} <span class="text-danger">*</span></label>
                    <select id="replacement_theme_id" class="form-control no-select2" data-placeholder="{{ ui_t('admin_themes.select_replacement_theme', 'Select replacement thematic area') }}">
                        <option value="">{{ ui_t('admin_themes.select_replacement_theme', 'Select replacement thematic area') }}</option>
                        @foreach(($allThemesForMapping ?? []) as $themeOption)
                            <option value="{{ $themeOption->id }}">{{ $themeOption->description }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">{{ ui_t('admin_themes.replacement_theme_help', 'Subthemes and related publications will be moved before delete.') }}</small>
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
        pleaseSelectReplacement: @json(ui_t('admin_themes.please_select_replacement_theme', 'Please select the thematic area to map the data to.')),
        pleaseSelectDifferent: @json(ui_t('admin_themes.please_select_different_theme', 'Please select a different thematic area.')),
        failedDelete: @json(ui_t('admin_themes.failed_delete_theme', 'Failed to delete thematic area.')),
        deleted: @json(ui_t('admin_themes.theme_deleted', 'Theme deleted.')),
        deletedMapped: @json(ui_t('admin_themes.theme_deleted_mapped', ':message Mapped :subthemes subthemes and :publications publications.'))
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
        const replacementThemeId = $('#replacement_theme_id').val();
        if (!replacementThemeId) {
            showDeleteNotice(lmThemes.pleaseSelectReplacement, 'warning');
            return;
        }
        if (String(replacementThemeId) === String(toDeleteRow)) {
            showDeleteNotice(lmThemes.pleaseSelectDifferent, 'warning');
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
                    showDeleteNotice(res.message || lmThemes.failedDelete, 'error');
                    return;
                }
                $('#delete-modal').modal('hide');
                const movedSubs = res?.data?.mapped_subthemes ?? 0;
                const movedPubs = res?.data?.mapped_publications ?? 0;
                const msg = lmThemes.deletedMapped
                    .replace(':message', res.message || lmThemes.deleted)
                    .replace(':subthemes', String(movedSubs))
                    .replace(':publications', String(movedPubs));
                showDeleteNotice(msg, 'success', function () { window.location.reload(); });
            })
            .catch(() => showDeleteNotice(lmThemes.failedDelete, 'error'));
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
