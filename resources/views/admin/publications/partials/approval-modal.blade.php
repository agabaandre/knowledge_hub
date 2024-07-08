<!-- First modal dialog -->
<div class="modal" id="approval-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalToggleLabel">Resource Approval</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
            </div>
            <form method="post" action="{{ $action }}">
            <div class="modal-body text-left">
                @csrf
                <p>
                    Are you sure you want to approve this resource?
                </p>

                <input type="hidden" name="id" value="{{$record->id}}" />
                <input type="hidden" name="approved" value="1" />
            </div>
            <div class="modal-footer">
                <!-- Toogle to second dialog -->
                <button type="submit" class="btn btn-outline-success btn-sm edit">Yes Approve</button>
                <button type="button" class="btn btn-outline-danger btn-sm edit">Cancel</button>
            </div>
            </form>
        </div>
    </div>
</div>
