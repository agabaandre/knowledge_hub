<div class="modal" id="create-area-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalToggleLabel">Create Georgraphical Area</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
            </div>

            <form id="createAreaForm" action="{{ route('areas.store')}}" method="POST">
                @csrf
                <div class="modal-body">

                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" name="area_name" id="area_name" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <!-- Toogle to second dialog -->
                    <button class="btn btn-outline-danger btn-sm submit">Save Area</button>
                </div>
        </form>
        </div>
    </div>
</div>
