<!-- First modal dialog -->
<div class="modal" id="delete-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalToggleLabel">Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ ui_t('general.close', 'Close') }}">
						<span aria-hidden="true">&times;</span>
					</button>
            </div>
            <div class="modal-body">
                <p>
                    Are you sure you want to delete this row?
                </p>
            </div>
            <div class="modal-footer">
                <!-- Toogle to second dialog -->
                <button class="btn btn-outline-danger btn-sm edit"
                        onclick="deleteRow()">{{ ui_t('general.yes_delete', 'Yes Delete') }}</button>
            </div>
        </div>
    </div>
</div>

