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
                <p>Deleting this subtheme requires selecting a replacement subtheme.</p>
                <p class="mb-2">
                    Publications mapped to
                    <strong id="deleteSubthemeName">selected subtheme</strong>
                    will be moved before deletion.
                </p>
                <div class="form-group mb-0">
                    <label for="replacement_subtheme_id">Replacement subtheme <span class="text-danger">*</span></label>
                    <select id="replacement_subtheme_id" class="form-control">
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
            const replacementSubthemeId = (document.getElementById('replacement_subtheme_id') || {}).value;
            if (!replacementSubthemeId) {
                alert('Please select the subtheme to map the publications to.');
                return;
            }
            if (String(replacementSubthemeId) === String(toDeleteRow)) {
                alert('Please select a different subtheme.');
                return;
            }
            let url = `{{ url('admin/subthemes/delete')}}?id=${toDeleteRow}&replacement_subtheme_id=${replacementSubthemeId}`;

                fetch(url, {headers: {'Accept': 'application/json'}})
                .then(res => res.json())
                .then(res => {
                    if (res.status !== 'success') {
                        alert(res.message || 'Failed to delete subtheme.');
                        return;
                    }
                    $('#delete-modal').modal('hide');
                    window.location.reload();
                })
                .catch(() => alert('Failed to delete subtheme.'));
        }


        function openDeleteModal (row = 0, rowName = '') {
            toDeleteRow = row;
            toDeleteRowName = rowName || '';
            const titleEl = document.getElementById('deleteSubthemeName');
            if (titleEl) {
                titleEl.textContent = toDeleteRowName || `ID ${toDeleteRow}`;
            }
            const replacementSelect = document.getElementById('replacement_subtheme_id');
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