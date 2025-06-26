<!--  Course Create/Edit Modal -->
<div class="modal" id="create-course-modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="courseModalLabel">Create Course</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ url('admin/courses/store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="row">
            <div class="form-group col-md-6">
              <label for="fullname">Full Name</label>
              <input type="text" class="form-control" id="fullname" name="fullname" required>
            </div>
            <div class="form-group col-md-6">
              <label for="shortname">Short Name</label>
              <input type="text" class="form-control" id="shortname" name="shortname" required>
            </div>
            <div class="form-group col-md-6">
              <label for="cover_image">Cover Image (URL or upload)</label>
              <input type="text" class="form-control" id="cover_image" name="cover_image">
            </div>
            <div class="form-group col-md-6">
              <label for="category_id">Category</label>
              <select class="form-control" id="category_id" name="category_id">
                <option value="">Select Category</option>
                @foreach($categories as $category)
                  <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group col-md-12">
              <label for="summary">Summary</label>
              <textarea class="form-control" id="summary" name="summary"></textarea>
            </div>
            <div class="form-group col-md-6">
              <label for="provider">Provider</label>
              <input type="text" class="form-control" id="provider" name="provider">
            </div>
            <div class="form-group col-md-6">
              <label for="course_url">Course URL</label>
              <input type="text" class="form-control" id="course_url" name="course_url" required>
            </div>
            <div class="form-group col-md-12">
              <label for="content">Content</label>
              <textarea class="form-control" id="content" name="content"></textarea>
            </div>
            <div class="form-group col-md-3">
              <div class="form-check mt-4">
                <input class="form-check-input" type="checkbox" id="is_moodle" name="is_moodle" value="1">
                <label class="form-check-label" for="is_moodle">Is Moodle</label>
              </div>
            </div>
            <div class="form-group col-md-3">
              <div class="form-check mt-4">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                <label class="form-check-label" for="is_active">Active</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-danger" data-dismiss="modal" type="button">Cancel</button>
          <button class="btn btn-primary" type="submit">Save Course</button>
        </div>
      </form>
    </div>
  </div>
</div>
