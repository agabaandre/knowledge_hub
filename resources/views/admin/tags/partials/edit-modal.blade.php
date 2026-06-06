<div class="modal" id="edit-tag-modal">
    <div class="modal-dialog modal-md modal-dialog-centered">
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
                        <input type="text" class="form-control" id="tag_text" name="tag_text" maxlength="255" value="">
                        <small class="text-muted">Up to 255 characters.</small>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                          <label class="form-label" for="name">Health Topic?</label>
                          <select class="form-control newform" id="is_health_topic" name="is_health_topic" required>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                          </select>
                        </div>
                      </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                          <label class="form-label" for="name">Health Emergency?</label>
                          <select class="form-control newform" id="is_health_emergency" name="is_health_emergency" required>
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                          </select>
                        </div>
                      </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                          <label class="form-label" for="overview">Overview</label>
                          <textarea class="form-control summernote" id="overview" name="overview"></textarea>
                        </div>
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
    $('#edit-tag-modal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var tag_id = button.data('id');
        var tag_text = button.data('tag');
        var is_health_topic = button.data('is_health_topic');
        var is_health_emergency = button.data('is_health_emergency');
        var overview = button.data('overview');
    
        var modal = $(this);
        modal.find('#tag_id').val(tag_id);
        modal.find('#tag_text').val(tag_text);
        modal.find('#is_health_topic').val(is_health_topic);
        modal.find('#is_health_emergency').val(is_health_emergency);
        modal.find('#overview').summernote('code', overview);
    
        function toggleOverviewRequired() {
            if (modal.find('#is_health_topic').val() == '1' || modal.find('#is_health_emergency').val() == '1') {
                modal.find('#overview').attr('required', true);
            } else {
                modal.find('#overview').removeAttr('required');
            }
        }
    
        modal.find('#is_health_topic, #is_health_emergency').on('change', toggleOverviewRequired);
        toggleOverviewRequired();
    });
    </script>
@endpush
