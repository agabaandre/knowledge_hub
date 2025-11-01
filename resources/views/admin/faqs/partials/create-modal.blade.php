<!-- Create/Edit FAQ Modal -->
<div class="modal fade" id="create-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="faqModalLabel">Create FAQ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form action="{{ url('admin/faqs/save') }}" method="post" id="faqForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    
                    <div class="form-group">
                        <label class="form-label" for="question">Question <span class="text-danger">*</span></label>
                        <input type="text" placeholder="Enter Question" class="form-control" id="question" name="question" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="answer">Response/Answer <span class="text-danger">*</span></label>
                        <textarea placeholder="Response/Answer" class="form-control" id="answer" name="answer" required></textarea>
                        <small class="form-text text-muted">Use the rich text editor to format your answer.</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary btn-sm" data-dismiss="modal" type="button">Cancel</button>
                    <button class="btn btn-primary btn-sm" type="submit" id="saveFaqBtn">Save FAQ</button>
                </div>
            </form>
        </div>
    </div>
</div>
