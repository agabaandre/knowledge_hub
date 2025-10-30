<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Delete</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="get" action="{{ url('admin/tools/delete') }}">
        <div class="modal-body">
            <input type="hidden" id="deleteId" name="id">
            Are you sure you want to delete this tool?
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger">Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- First modal dialog -->
<div class="modal" id="delete-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalToggleLabel">Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
            </div>
            <div class="modal-body">
                <p>
                    Are you sure you want to delete this row?
                </p>
            </div>
            <div class="modal-footer">
                <!-- Toogle to second dialog -->
                <button class="btn btn-outline-danger btn-sm edit"
                        onclick="deleteRow()">Yes Delete</button>
            </div>
        </div>
    </div>
</div>

    <script>
        
        var toDeleteRow = '';

        function deleteRow () {
            let url = `{{ url('admin/commsofpractice/delete')}}?id=${toDeleteRow}`;

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