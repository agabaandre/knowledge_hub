<!-- Create Modal -->
<div class="modal fade" id="create-modal" tabindex="-1" role="dialog" aria-labelledby="create-modal-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="create-modal-label">Add New Record</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ url('/admin/datarecords/create') }}">
                @csrf
                <div class="modal-body">
                    <!-- Title Field -->
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>

                    <!-- Description Field -->
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>

                    <!-- Country Field -->
                    <div class="form-group">
                        <label for="country">Country</label>
                        <select class="form-control" id="country" name="country" required>
                            <!-- Populate with country options -->
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Category Field -->
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select class="form-control" id="category" name="category" required>
                            <!-- Populate with category options -->
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Subcategory Field - Hidden by Default -->
                    <div class="form-group" id="subcategory-field" style="display:none;">
                        <label for="subcategory">Subcategory</label>
                        <select class="form-control" id="subcategory" name="subcategory">
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
            $('#category').on('change', function() {
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
                            $('#subcategory-field').show();
                            $('#subcategory').empty();
                            $.each(data, function(key, value) {
                                $('#subcategory').append('<option value="'+ value.id +'">'+ value.sub_category_name +'</option>');
                            });
                        }
                    });
                } else {
                    $('#subcategory-field').hide();
                }
            });
        });
    </script>
@endpush
