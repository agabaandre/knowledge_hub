<!--  Extra Large modal example -->
<div class="modal" id="create-modal">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myExtraLargeModalLabel">Create Admin Unit </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <form action="{{ url('admin/adminunits/save') }}" enctype="multipart/form-data" method="post" id='filetypes' class='filetypes'>
        @csrf
      <div class="modal-body">
        <input type="hidden" name="id" id="id" class="newform">
        <div class="row">
          <div class="col-md-12">
            <div class="mb-3">
              <label class="form-label" for="unit_name">Admin Unit Name</label>
              <input type="text" placeholder="Enter Unit Name" class="form-control newform" id="unit_name" name="unit_name" required>
            </div>
          </div>

          <div class="col-md-12">
            <div class="mb-3">
              <label class="form-label" for="code">Identifier Code</label>
              <input type="text" placeholder="Enter Code" class="form-control newform" id="code" name="code">
            </div>
          </div>

          <div class="col-md-12">
            <div class="mb-3">
              <label class="form-label" for="alt_code">Alternate Identifier Code</label>
              <input type="text" placeholder="Enter Alternate Code" class="form-control newform" id="alt_code" name="alt_code">
            </div>
          </div>

          <div class="col-md-12">
            <div class="mb-3">
              <label class="form-label" for="description">Description</label>
              <textarea placeholder="Enter Description" class="form-control newform"  id="description" name="description" ></textarea>
            </div>
          </div>

          <div class="col-md-12">
            <div class="mb-3">
              <label class="form-label" for="parent_id">Parent Admin Unit</label>
              <select class="form-control newform"  id="parent_id" name="parent_id" >
                <option value="">None</option>
                @foreach($adminunits as $unit)
                <option value="{{$unit->id}}">{{$unit->name}}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="col-md-12 mt-3">
                <label> Logo</label>
                <div class="form-group">
                    <input type="file" name="logo" id="attachments">
                    <div class="preview" style="max-width: 150px;"></div>
                </div>
            </div>
        </div>

      </div>
      <div class="modal-footer">
        <button class="btn btn-danger" data-dismiss="modal" type="button">Cancel</button>
        <button class="btn btn-primary" type="submit">Save Record</button>
      </div>

      </form>

    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>
