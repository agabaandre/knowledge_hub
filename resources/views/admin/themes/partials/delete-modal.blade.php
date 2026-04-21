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
                <p>Deleting this thematic area requires selecting a replacement theme.</p>
                <p class="mb-2">
                    All related subthemes and publications will be mapped to:
                    <strong id="deleteThemeName">selected thematic area</strong>.
                </p>
                <div class="form-group mb-0">
                    <label for="replacement_theme_id">Replacement thematic area <span class="text-danger">*</span></label>
                    <select id="replacement_theme_id" class="form-control">
                        <option value="">Select replacement thematic area</option>
                        @foreach(($allThemesForMapping ?? []) as $themeOption)
                            <option value="{{ $themeOption->id }}">{{ $themeOption->description }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Subthemes and related publications will be moved before delete.</small>
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

        function deleteRow () {
            const replacementThemeId = (document.getElementById('replacement_theme_id') || {}).value;
            if (!replacementThemeId) {
                alert('Please select the thematic area to map the data to.');
                return;
            }
            if (String(replacementThemeId) === String(toDeleteRow)) {
                alert('Please select a different thematic area.');
                return;
            }
            let url = `{{ url('admin/themes/delete')}}?id=${toDeleteRow}&replacement_theme_id=${replacementThemeId}`;

                fetch(url, {headers: {'Accept': 'application/json'}})
                .then(res => res.json())
                .then(res => {
                    if (res.status !== 'success') {
                        alert(res.message || 'Failed to delete thematic area.');
                        return;
                    }
                    $('#delete-modal').modal('hide');
                    window.location.reload();
                })
                .catch(() => alert('Failed to delete thematic area.'));
        }


        function openDeleteModal (row = 0, rowName = '') {
            toDeleteRow = row;
            toDeleteRowName = rowName || '';
            const titleEl = document.getElementById('deleteThemeName');
            if (titleEl) {
                titleEl.textContent = toDeleteRowName || `ID ${toDeleteRow}`;
            }
            const replacementSelect = document.getElementById('replacement_theme_id');
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