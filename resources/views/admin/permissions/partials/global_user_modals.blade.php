<!-- Edit User Modal (Global) -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <style>
        #editUserModal .modal-body{max-height:70vh;overflow:auto}
      </style>
      <div class="modal-header">
        <h6 class="modal-title">Edit User</h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <form method="POST" action="{{ route('permissions.saveuser') }}">
        @csrf
        <div class="modal-body">
            <input type="hidden" name="id" id="edit_user_id"/>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" name="first_name" id="edit_name"/>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" id="edit_email"/>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                    <label>Mobile</label>
                    <input type="text" class="form-control" name="mobile" id="edit_phone"/>
                 </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" name="status" id="edit_status">
                        <option value="1">Active</option>
                        <option value="0">InActive</option>
                        <option value="2">Restricted</option>
                        <option value="3">Reset</option>
                    </select>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                    <label><input type="checkbox" name="is_verified" value="1" id="edit_verified"> Verified</label>
                </div>
              </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-success">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Reset Modal (Global) -->
<div class="modal fade" id="resetUserModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">Reset Password</h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <form method="POST" action="{{ route('permissions.reset') }}">
        @csrf
        <div class="modal-body">
            <input type="hidden" name="id" id="reset_user_id"/>
            <p>Reset this user's password to default?</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-warning">Reset</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Modal (Global) -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">Delete User</h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <form method="POST" action="{{ route('permissions.delete') }}">
        @csrf
        <div class="modal-body">
            <input type="hidden" name="id" id="delete_user_id"/>
            <p>Are you sure you want to delete this user?</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger">Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>


