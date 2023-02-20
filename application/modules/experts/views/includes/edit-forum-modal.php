<div class="modal fade" id="edit-forum-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Forum</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
            <?php echo form_open_multipart(base_url('forums/save'), array('id' => 'edit-forum', 'class' => 'edit-forum')); ?>
                    <div class="form-group">
                        <label for="title">Forum Title</label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="Forum Title">
                    </div>
                    <div class="form-group">
                        <label for="description">Forum Description</label>
                        <textarea class="form-control" id="description" name="description" placeholder="Forum Description"></textarea>
                    </div>
                    <input type="hidden" name="id" id="id" value="">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>