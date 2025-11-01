<!-- Edit Indicator Modal -->
<div class="modal" id="edit-modal">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Indicator</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ url('admin/kpi/save') }}" method="post" id='edit-indicator-form' class='edit-indicator-form'>
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id" value="">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="edit_name">Indicator Name</label>
                                <input type="text" placeholder="Indicator Name" class="form-control" id="edit_name" name="name" required>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="edit_description">Indicator Description</label>
                                <textarea placeholder="Description" class="form-control" id="edit_description" name="description" required></textarea>
                            </div>
                        </div>

                        <!-- Select2 Subject Area Dropdown -->
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="edit_subject_area">Subject Area</label>
                                <select class="form-control select2" name="subject_area" id="edit_subject_area" required>
                                    <option value="">Select Subject Area</option>
                                    @foreach($subject_areas as $row)
                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="edit_frequency">Frequency</label>
                                <select class="form-control select2" name="frequency" id="edit_frequency" required>
                                    <option value="">Select Frequency</option>
                                    <option value="Monthly">Monthly</option>
                                    <option value="Quarterly">Quarterly</option>
                                    <option value="Yearly">Yearly</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal" type="button">Cancel</button>
                    <button class="btn btn-primary" type="submit">Update Indicator</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var toEditRow = '';

    function openEditModal(id) {
        toEditRow = id;
        
        // Fetch indicator data
        fetch(`{{ url('admin/kpi/get') }}?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.indicator) {
                    const indicator = data.indicator;
                    
                    // Populate form fields
                    document.getElementById('edit_id').value = indicator.id;
                    document.getElementById('edit_name').value = indicator.name || '';
                    document.getElementById('edit_description').value = indicator.description || '';
                    document.getElementById('edit_subject_area').value = indicator.subject_area || '';
                    document.getElementById('edit_frequency').value = indicator.frequency || '';
                    
                    // Reinitialize Select2 for the edit modal
                    $('#edit_subject_area, #edit_frequency').select2({
                        dropdownParent: $('#edit-modal')
                    });
                    
                    // Show modal
                    $('#edit-modal').modal('show');
                } else {
                    alert('Failed to load indicator data.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while loading indicator data.');
            });
    }
</script>