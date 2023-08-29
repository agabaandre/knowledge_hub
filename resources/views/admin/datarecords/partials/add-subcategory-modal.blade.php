<!-- First modal dialog -->
<div class="modal" id="add_category">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ url('admin/datarecords/categories/savesub')}}" method="post">
            <div class="modal-header">
                <h5 class="modal-title" id="">Add Sub Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label" for="name">Sub Category</label>
                            <input type="text" placeholder="Sub Category Name"  class="form-control" id="name" 
                            name="name" />
                        </div>
                    </div>

                    @csrf

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label" for="description">Catgeory</label>
                            <select class="form-control" name="category_id">
                                <option disabled> Choose</option>
                                    @foreach($categories as $cat)
                                    <option value="{{$cat->id}}">{{$cat->category_name}}</option>
                                    @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <!-- Toogle to second dialog -->
                <button type="submit" class="btn btn-outline-danger btn-sm">Save Record</button>
            </div>
            </form>
        </div>
    </div>
</div>

    <script>
        var toDeleteRow = '';
        function deleteRow () {
            let url = `{{ url('admin/datarecords/categories/delete')}}?id=${toDeleteRow}`;

                fetch(url)
                .then(res => res.text())
                .then(res => {
                    console.log(res)
                    $('#delete-modal').modal('hide');
                     window.location.reload();
                })
        }

        function openDeleteModal (row = 0) {
            toDeleteRow = row;
            $('#delete-modal').modal('show');
        }
    </script>