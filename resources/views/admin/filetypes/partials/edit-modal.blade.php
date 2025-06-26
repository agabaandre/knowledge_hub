<div class="modal" id="edit-filetype-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalToggleLabel">Edit File Type</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('filetypes.store') }}" method="POST">
                @csrf

                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="file_name" name="name" value="">
                    </div>
                    <div class="form-group">
                        <label for="name">Icon</label>
                        <input type="text" class="form-control" id="file_icon" name="icon" value="">
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="downloadable" name="downloadable">
                            <label class="custom-control-label" for="downloadable">Downloadable</label>
                        </div>
                    </div>
                    <input type="hidden" name="id" id="file_id" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('modal-scripts')
    <script>
        // JavaScript for specific modal
        $('#edit-filetype-modal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var name = button.data('name');
            var icon = button.data('icon');
            var downloadable = button.data('downloadable');

            console.log({
                id, name, icon, downloadable
            })

            $('#file_id').val(id);
            $('#file_name').val(name);
            $('#file_icon').val(icon);
            $('#downloadable').prop('checked', downloadable);
        });

        // Convert checkbox state to 1 or 0 before form submission
        $('form').submit(function() {
            var downloadable = $('#downloadable').is(':checked') ? 1 : 0;
            $('#downloadable').val(downloadable);
        });
    </script>
@endpush
