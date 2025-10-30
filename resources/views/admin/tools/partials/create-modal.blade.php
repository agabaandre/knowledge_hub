<div class="modal fade" id="toolModal" tabindex="-1" role="dialog" aria-labelledby="toolModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="toolModalLabel">Save Tool</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="toolForm" method="post" action="{{ url('admin/tools/store') }}">
        @csrf
        <div class="modal-body">
            <input type="hidden" name="id" id="id">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Tool Name</label>
                    <input type="text" class="form-control" name="tool_name" id="tool_name" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Category</label>
                    <select class="form-control" name="tool_category_id" id="tool_category_id" required>
                        <option value="">Select</option>
                        @foreach($categories as $c)
                        <option value="{{ $c->id }}">{{ $c->category_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea class="form-control summernote-sm" name="tool_desc" id="tool_desc"></textarea>
            </div>
            <div class="form-group">
                <label>Tool URL</label>
                <input type="url" class="form-control" name="tool_url" id="tool_url">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!--  Extra Large modal example -->
<div class="modal" id="create-modal">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myExtraLargeModalLabel">Create Access Group </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <form action="{{ url('admin/commsofpractice/save') }}" method="post" id='filetypes' class='filetypes'>
        @csrf
      <div class="modal-body">
        <input type="hidden" name="id" id="id" class="newform">
        <div class="row">
          <div class="col-md-12">
            <div class="mb-3">
              <label class="form-label" for="community_name">Community Name</label>
              <input type="text" placeholder="Enter Community Name" class="form-control newform" id="community_name" name="community_name" required>
            </div>
          </div>

          <div class="col-md-12">
            <div class="mb-3">
              <label class="form-label" for="group_description">Description</label>
              <textarea placeholder="Enter Description" class="form-control newform" id="description" name="description" ></textarea>
            </div>
          </div>

          <!-- <div  class="col-md-4"> -->
          <!-- </div> -->
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
