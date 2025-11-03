<!-- Delete Course Modal -->
<div class="modal" id="delete-course-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Course</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
            </div>
            <div class="modal-body">
                <p>
                    Are you sure you want to delete this course? This action cannot be undone.
                </p>
                <p class="text-muted small mb-0" id="course-to-delete-name"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="deleteCourse()">
                    <i class="fa fa-trash"></i> Delete Course
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    var courseToDelete = null;
    var courseToDeleteName = '';

    function deleteCourse() {
        if (!courseToDelete) {
            alert('No course selected');
            return;
        }

        let url = `{{ url('admin/courses/delete') }}?id=${courseToDelete}`;

        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(res => res.json())
        .then(data => {
            $('#delete-course-modal').modal('hide');
            if (data.status === 'success') {
                // Reload the page to show updated list
                window.location.reload();
            } else {
                alert(data.message || 'Delete failed');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            $('#delete-course-modal').modal('hide');
            alert('An error occurred while deleting the course');
            // Still reload to see if deletion happened
            window.location.reload();
        });
    }

    function openDeleteCourseModal(courseId, courseName) {
        courseToDelete = courseId;
        courseToDeleteName = courseName || 'this course';
        document.getElementById('course-to-delete-name').textContent = courseToDeleteName ? `Course: ${courseToDeleteName}` : '';
        $('#delete-course-modal').modal('show');
    }
</script>