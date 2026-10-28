<!-- First modal dialog -->
<div class="modal" id="reject-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalToggleLabel">{{ ui_t('admin_publications.approval_title', 'Resource Approval') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ ui_t('general.close', 'Close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="{{ $action }}">
                <div class="modal-body text-left">
                    @csrf
                    <p>{{ ui_t('admin_publications.confirm_reject_resource', 'Are you sure you want to reject this resource?') }}</p>

                    <input type="hidden" name="id" value="{{ $record->id }}" />
                    <input type="hidden" name="rejected" value="1" />
                    <input type="hidden" name="is_summary" value="{{ $is_summary ?? 0 }}" />

                    <div class="form-group mt-2">
                        <label for="rejected_reason" class="mb-1">{{ ui_t('admin_publications.reject_reason_label', 'Reason for rejection (shared with the author)') }}</label>
                        <textarea id="rejected_reason" name="rejected_reason" class="form-control" rows="4" placeholder="{{ ui_t('admin_publications.reject_reason_detail_placeholder', 'Provide a clear reason and what to fix') }}" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- Toogle to second dialog -->
                    <button type="submit" class="btn btn-outline-danger btn-sm edit">{{ ui_t('admin_publications.yes_reject', 'Yes, Reject') }}</button>
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ ui_t('general.cancel', 'Cancel') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
