<!--  Extra Large modal example -->
<div class="modal fade" id="create-modal">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <style>
        /* Keep footer visible by constraining body height */
        #create-modal .modal-body{ max-height:70vh; overflow-y:auto; }
      </style>
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
              <textarea placeholder="Enter Description" class="form-control newform summernote-sm" id="description" name="description"></textarea>
            </div>
          </div>

          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label" for="region_id">Region</label>
              @include('partials.regions.dropdown', [
                  'field' => 'region_id',
                  'class' => 'rcc select2',
                  'selected' => null,
                  'allfield' => 'All Regions',
                  'id' => 'region_id'
              ])
              <small class="text-muted d-block mt-1">Select a region or "All Regions" for global access.</small>
            </div>
          </div>

          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label" for="country_id">Country</label>
              @include('partials.countries.dropdown', [
                  'field' => 'country_id',
                  'class' => 'country select2',
                  'selected' => null,
                  'allfield' => 'All Countries',
                  'id' => 'country_id'
              ])
              <small class="text-muted d-block mt-1">Select a country or "All Countries" within the selected region.</small>
            </div>
          </div>

          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label" for="organisation">Organisation</label>
              <input type="text" placeholder="Enter Organisation Name" class="form-control newform" id="organisation" name="organisation">
            </div>
          </div>

          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label" for="department">Department</label>
              <input type="text" placeholder="Enter Department Name" class="form-control newform" id="department" name="department">
            </div>
          </div>

          <div class="col-md-12">
            <div class="mb-3">
              <label class="form-label" for="tags">Tags</label>
              @include('partials.tags.dropdown', [
                  'field' => 'tags[]',
                  'selected' => [],
                  'allfield' => 'Select Tags'
              ])
              <small class="text-muted d-block mt-1">Add relevant tags to help categorize and find this community.</small>
            </div>
          </div>

          <div class="col-md-12">
            <div class="mb-3">
              <div class="form-check">
                <input type="checkbox" class="form-check-input" id="is_public" name="is_public" value="1" checked>
                <label class="form-check-label" for="is_public">Public Community</label>
                <small class="text-muted d-block">Public communities are visible to all users. Uncheck to make this community private.</small>
              </div>
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
