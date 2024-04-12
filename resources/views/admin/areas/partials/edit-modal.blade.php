<div class="modal" id="edit-area-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalToggleLabel">Edit Georgraphical Area</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
            </div>

            <form id="editAreaForm" action="{{ route('areas.store')}}" method="POST">
                @csrf
                <div class="modal-body">

                    <input type="hidden" name="area_id" id="area_id">

                    <div class="form-group">
                        <label for="edit_name">Name</label>
                        <input type="text" class="form-control" name="area_name" id="area_name" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <!-- Toogle to second dialog -->
                    <button class="btn btn-outline-danger btn-sm submit">Update</button>
                </div>
        </form>
        </div>
    </div>
</div>

@push('modal-scripts')
    <script>
        // JavaScript for specific modal
        $('#edit-area-modal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var area_id = button.data('id');
            var area_name = button.data('name');

            $('#area_id').val(area_id);
            $('#area_name').val(area_name);
        });
    </script>
@endpush
