<!--  Course Edit Modal -->
<div class="modal" id="edit-course-modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editCourseModalLabel">Edit Course</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="edit-course-form" method="post">
        @csrf
        @method('PUT')
        <input type="hidden" id="edit_id" name="id">
        <div class="modal-body">
          <div class="row">
            <div class="form-group col-md-6">
              <label for="edit_fullname">Full Name</label>
              <input type="text" class="form-control" id="edit_fullname" name="fullname" required>
            </div>
            <div class="form-group col-md-6">
              <label for="edit_shortname">Short Name</label>
              <input type="text" class="form-control" id="edit_shortname" name="shortname" required>
            </div>
            <div class="form-group col-md-6">
              <label for="edit_cover_image">Cover Image (URL or upload)</label>
              <input type="text" class="form-control" id="edit_cover_image" name="cover_image">
            </div>
            <div class="form-group col-md-6">
              <label for="edit_category_id">Category</label>
              <select class="form-control" id="edit_category_id" name="category_id">
                <option value="">Select Category</option>
                @foreach($categories as $category)
                  <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group col-md-12">
              <label for="edit_summary">Summary</label>
              <textarea class="form-control" id="edit_summary" name="summary"></textarea>
            </div>
            <div class="form-group col-md-6">
              <label for="edit_provider">Provider</label>
              <input type="text" class="form-control" id="edit_provider" name="provider">
            </div>
            <div class="form-group col-md-6">
              <label for="edit_course_url">Course URL</label>
              <input type="text" class="form-control" id="edit_course_url" name="course_url" required>
            </div>
            <div class="form-group col-md-12">
              <label for="edit_content">Content</label>
              <textarea class="form-control" id="edit_content" name="content"></textarea>
            </div>
            <div class="form-group col-md-3">
              <div class="form-check mt-4">
                <input class="form-check-input" type="checkbox" id="edit_is_moodle" name="is_moodle" value="1">
                <label class="form-check-label" for="edit_is_moodle">Is Moodle</label>
              </div>
            </div>
            <div class="form-group col-md-3">
              <div class="form-check mt-4">
                <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1">
                <label class="form-check-label" for="edit_is_active">Active</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-danger" data-dismiss="modal" type="button">Cancel</button>
          <button class="btn btn-primary" type="submit">Update Course</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
// Fill modal with course data on edit button click
$(document).on('click', '.edit-course-btn', function() {
    var course = $(this).data('course');
    $('#edit_id').val(course.id);
    $('#edit_fullname').val(course.fullname);
    $('#edit_shortname').val(course.shortname);
    $('#edit_cover_image').val(course.cover_image);
    $('#edit_category_id').val(course.category_id);
    $('#edit_summary').val(course.summary);
    $('#edit_provider').val(course.provider);
    $('#edit_course_url').val(course.course_url);
    $('#edit_content').val(course.content);
    $('#edit_is_moodle').prop('checked', course.is_moodle == 1);
    $('#edit_is_active').prop('checked', course.is_active == 1);
    // Set form action
    $('#edit-course-form').attr('action', '/admin/courses/update/' + course.id);
});
</script> 