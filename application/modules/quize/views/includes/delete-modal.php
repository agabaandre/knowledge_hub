<!-- First modal dialog -->
<div class="modal" id="delete-quize-modal">
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
                <button class="btn btn-outline-danger btn-sm edit" onclick="deleteRow()">Yes Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
    var toDeleteRow = '';

    function deleteRow() {
        let url = '<?php echo base_url(); ?>quize/delete/' + toDeleteRow;

        fetch(url)
            .then(res => res.text())
            .then(res => {
                console.log(res)
                $('#delete-quize-modal').modal('hide');
                window.location.reload();
            })
    }

    function openDeleteModal(row = 0) {
        toDeleteRow = row;
        $('#delete-quize-modal').modal('show');
    }
</script>