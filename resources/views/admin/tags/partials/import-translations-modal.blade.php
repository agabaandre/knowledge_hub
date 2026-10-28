<div class="modal fade" id="import-translations-modal" tabindex="-1" role="dialog" aria-labelledby="importTranslationsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="post" action="{{ route('tags.import-translations') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importTranslationsLabel">Import topic translations</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ ui_t('general.close', 'Close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        Upload the CSV or XLSX exported from <strong>Export for translation</strong>.
                        Fill <code>tag_text_translated</code> and <code>overview_translated</code> (or edit
                        <code>tag_text</code> / <code>overview</code> in place). Rows are matched by <code>id</code>.
                        French characters and apostrophes (d’entrée, Santé) are preserved.
                    </p>
                    <div class="form-group">
                        <label for="translations_file">File (CSV / XLSX)</label>
                        <input type="file" class="form-control-file" id="translations_file" name="file"
                               accept=".csv,.txt,.xlsx,.xls,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                               required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ ui_t('general.cancel', 'Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ ui_t('general.import', 'Import') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
