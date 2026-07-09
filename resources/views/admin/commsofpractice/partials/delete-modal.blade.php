<div class="modal fade" id="cop-delete-modal" tabindex="-1" role="dialog" aria-labelledby="copDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="copDeleteModalLabel">Delete community</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pt-2">
                <div class="d-flex align-items-start">
                    <div class="mr-3 text-danger" style="font-size:1.5rem;line-height:1;">
                        <i class="fa fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <p class="mb-2">Are you sure you want to delete this community of practice?</p>
                        <p class="mb-2 font-weight-bold" id="copDeleteCommunityName"></p>
                        <p class="text-muted small mb-0">This will remove memberships, invitations, linked publications/forums, and comments. This action cannot be undone.</p>
                    </div>
                </div>
                <div id="copDeleteError" class="alert alert-danger mt-3 mb-0 d-none" role="alert"></div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger btn-sm" id="copConfirmDeleteBtn">
                    <span class="cop-delete-btn-label"><i class="fa fa-trash mr-1"></i>Yes, delete</span>
                    <span class="cop-delete-btn-loading d-none"><i class="fa fa-spinner fa-spin mr-1"></i>Deleting...</span>
                </button>
            </div>
        </div>
    </div>
</div>
