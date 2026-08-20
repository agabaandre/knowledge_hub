<div class="modal" id="create-modal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create Admin Unit</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="{{ url('admin/adminunits/save') }}" enctype="multipart/form-data" method="post" id="create-adminunit-form" class="filetypes">
        <div class="modal-body">
          @include('admin.adminunits.partials.form_fields', ['row' => null])
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-dismiss="modal" type="button">Cancel</button>
          <button class="btn btn-primary" type="submit">Save Record</button>
        </div>
      </form>
    </div>
  </div>
</div>
