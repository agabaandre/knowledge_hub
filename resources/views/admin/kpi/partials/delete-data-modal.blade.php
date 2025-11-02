<!-- Delete Indicator Data Modal -->
<div class="modal" id="delete-data-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Indicator Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ url('admin/kpi/delete_data') }}" method="GET" id="delete-data-form">
                <input type="hidden" name="kpi_id" id="delete_kpi_id">
                <input type="hidden" name="country_id" id="delete_country_id">
                <input type="hidden" name="period" id="delete_period">
                <div class="modal-body">
                    <p>Are you sure you want to delete this indicator data record? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-trash"></i> Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

