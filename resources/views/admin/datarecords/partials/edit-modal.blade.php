<!-- Edit Modal -->
<div class="modal fade" id="edit-record-modal" tabindex="-1" role="dialog" aria-labelledby="edit-record-modal-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit-record-modal-label">Edit Record</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ url('#') }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <!-- Title Field -->
                    <div class="form-group">
                        <label for="edit-title">Title</label>
                        <input type="text" class="form-control" id="edit-title" name="edit-title" required>
                    </div>

                    <!-- Description Field -->
                    <div class="form-group">
                        <label for="edit-description">Description</label>
                        <textarea class="form-control" id="edit-description" name="edit-description" rows="3" required></textarea>
                    </div>

                    <!-- Country Field -->
                    <div class="form-group">
                        <label for="edit-country">Country</label>
                        <select class="form-control" id="edit-country" name="edit-country" required>
                            <!-- Populate with country options -->
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Category Field -->
                    <div class="form-group">
                        <label for="edit-category">Category</label>
                        <select class="form-control" id="edit-category" name="edit-category" required>
                            <!-- Populate with category options -->
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Subcategory Field - Hidden by Default -->
                    <div class="form-group" id="edit-subcategory-field" style="display:none;">
                        <label for="edit-subcategory">Subcategory</label>
                        <select class="form-control" id="edit-subcategory" name="edit-subcategory">
                            <!-- Will be populated dynamically based on the selected category -->
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#edit-category').on('change', function() {
                var category_id = $(this).val();
                if(category_id) {
                    $.ajax({
                        url: '{{ route("get-subcategories") }}',
                        type: "GET",
                        data: {
                            category_id: category_id
                        },
                        dataType: "json",
                        success:function(data) {
                            $('#edit-subcategory-field').show();
                            $('#edit-subcategory').empty();
                            $.each(data, function(key, value) {
                                $('#edit-subcategory').append('<option value="'+ value.id +'">'+ value.sub_category_name +'</option>');
                            });
                        }
                    });
                } else {
                    $('#edit-subcategory-field').hide();
                }
            });
        });
    </script>
@endpush
