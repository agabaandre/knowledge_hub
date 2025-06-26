<!--  Extra Large modal example -->
<div class="modal" id="edit-fact-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myExtraLargeModalLabel">Edit Fact</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ url('admin/facts/save') }}" method="post" id='filetypes' class='filetypes'>
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id" id="fact_id" class="newform">
                    <div class="row">

                        <div class="form-group col-md-12">
                            <label class="form-label" for="name">Title</label>
                            <input type="text" placeholder="Enter Title" class="form-control newform" id="fact_title"
                                name="title" required>
                        </div>

                        <div class="form-group col-md-12">
                            <label class="form-label" for="name">Fact Summary</label>
                            <textarea placeholder="Sumamry" class="form-control summernote-sm" id="fact_summary" name="summary"></textarea>
                        </div>

                        <div class="form-group col-md-12">
                            <label class="form-label" for="name">Fact Details</label>
                            <textarea placeholder="Details" class="form-control summernote" id="fact_description" name="description"></textarea>
                        </div>

                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" data-dismiss="modal" type="button">Cancel</button>
                    <button class="btn btn-primary" type="submit">Save Record</button>
                </div>

            </form>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>



@push('modal-scripts')
    <script>
        // JavaScript for specific modal
        $('#edit-fact-modal').on('show.bs.modal', function(event) {

            var button = $(event.relatedTarget);

            var id = button.data('id'); // assuming you have data-id attribute on the trigger button

            // Assuming you want to populate the form fields with existing data
            var title = button.data('title');
            var summary = button.data('summary');
            var description = button.data('description');

            // Populate the form fields with the retrieved data
            $('#fact_id').val(id);
            $('#fact_title').val(title);
            $('#fact_summary').val(summary);
            $('#fact_description').val(description);
        });

        // Optional: If you want to reset the form fields when the modal is closed
        $('#edit-fact-modal').on('hidden.bs.modal', function() {
            $('#fact_id').val('');
            $('#fact_title').val('');
            $('#fact_summary').val('');
            $('#fact_description').val('');
        });
    </script>
@endpush
