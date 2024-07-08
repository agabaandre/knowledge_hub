<div class="modal" id="edit-tag-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalToggleLabel">Edit Tag</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('tags.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body">
                    <div class="form-group">
                        <label for="tag_text">Tag</label>
                        <input type="text" class="form-control" id="tag_text" name="tag_text" value="">
                    </div>
                    <input type="hidden" name="tag_id" id="tag_id" value="">
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
        $('#edit-tag-modal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var tag_id = button.data('id');
            var tag_text = button.data('tag');

            $('#tag_id').val(tag_id);
            $('#tag_text').val(tag_text);
        });
    </script>
@endpush
