<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <?php echo form_open('privacypolicy/save', [
                    'id' => 'form-privacy-policy',
                    'method' => 'post']); ?>
                <div class="card-header">
                    <h3 class="card-title">Privacy Policy</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <textarea name="privacy_policy" id="privacy_policy" cols="30" rows="10" class="form-control"><?php echo $privacy_policy; ?></textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>