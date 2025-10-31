<!-- Edit User Modal (Global) -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <style>
        #editUserModal .modal-body{max-height:70vh;overflow:auto}
      </style>
      <div class="modal-header">
        <h6 class="modal-title"><i class="fa fa-user-edit text-primary"></i> Edit User</h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <form method="POST" action="{{ route('permissions.saveuser') }}">
        @csrf
        <div class="modal-body">
            <input type="hidden" name="id" id="edit_user_id"/>
            <div class="row">
              <div class="form-group col-md-6 col-sm-12">
                <label><i class="icon-user mr-2"></i>First Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control text-bold" placeholder="First Name"
                    name="first_name" id="edit_first_name" required />
              </div>
              <div class="form-group col-md-6 col-sm-12">
                <label><i class="icon-user mr-2"></i>Last Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control text-bold" placeholder="Last Name"
                    name="last_name" id="edit_last_name" required />
              </div>
              <div class="form-group col-md-6 col-sm-12">
                <label><i class="icon-envelope mr-2"></i>Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control text-bold" placeholder="Email" name="email" id="edit_email" required />
              </div>
              <div class="form-group col-md-6 col-sm-12">
                <label><i class="icon-phone mr-2"></i>Mobile <span class="text-danger">*</span></label>
                <input type="text" class="form-control text-bold" placeholder="Mobile" name="mobile" id="edit_phone" required />
              </div>

              <div class="form-group col-md-6 col-sm-12">
                <label class="text-bold">
                  <i class="icon-collaboration mr-2"></i>
                  {{ __('auth.user') }} {{ __('auth.role') }} <span class="text-danger">*</span>
                </label>
                <select class="form-control form-control-select2 select" name="role_id" id="edit_role_id" data-fouc required>
                  <option value="" disabled>Choose Role</option>
                  @foreach ($roles as $role)
                    <option value="{{ $role->id }}">{{ strtoupper($role->name) }}</option>
                  @endforeach
                </select>
              </div>

              @if (states_enabled())
              <div class="form-group col-md-6 col-sm-12">
                <label class="text-bold">
                  <i class="icon-collaboration mr-2"></i>
                  Access Level
                </label>
                <select class="form-control form-control-select2 select" name="level_id" id="edit_level_id" data-fouc>
                  <option value="">Choose Level</option>
                  @foreach ($levels as $level)
                    <option value="{{ $level->id }}">{{ strtoupper($level->level_name) }}</option>
                  @endforeach
                </select>
              </div>
              @endif

              @if (states_enabled())
              <div class="form-group col-md-6 col-sm-12">
                <label class="text-bold">
                  <i class="icon-collaboration mr-2"></i>
                  Member State
                </label>
                @include('partials.countries.dropdown', ['field' => 'country_id', 'selected' => '', 'class' => 'select2', 'id' => 'edit_country_id'])
              </div>
              @else 
              <div class="form-group col-md-6 col-sm-12">
                <label class="text-bold">
                  <i class="icon-collaboration mr-2"></i>
                  Administrative Unit
                </label>
                @include('partials.adminunits.dropdown', ['field' => 'administrative_unit_id', 'selected' => '', 'class' => 'select2', 'id' => 'edit_administrative_unit_id'])
              </div>
              @endif

              <div class="form-group col-md-6 col-sm-12">
                <label class="text-bold">
                  <i class="icon-collaboration mr-2"></i>
                  Associated Corporate Source/Member State
                </label>
                @include('partials.authors.dropdown', ['field' => 'author_id', 'allfield' => 'None', 'selected' => '', 'class' => 'select2', 'id' => 'edit_author_id'])
              </div>

              <div class="form-group col-md-6 col-sm-12">
                <label><i class="icon-lock mr-2"></i>Password</label>
                <input type="password" class="form-control text-bold"
                    placeholder="Leave blank to keep current password" name="pass" id="edit_pass" />
                <small class="text-muted">Leave blank to keep current password</small>
              </div>

              <div class="form-group col-md-6 col-sm-12">
                <label><i class="icon-info mr-2"></i>Status</label>
                <select class="form-control text-bold" name="status" id="edit_status">
                  <option value="1">Active</option>
                  <option value="0">InActive</option>
                  <option value="2">Restricted</option>
                  <option value="3">Reset</option>
                </select>
              </div>

              <div class="form-group col-md-6 col-sm-12">
                <div class="form-check mt-4">
                  <input type="checkbox" class="form-check-input" name="is_verified" value="1" id="edit_verified">
                  <label class="form-check-label" for="edit_verified">
                    <i class="icon-checkmark-circle mr-2"></i>Verified
                  </label>
                </div>
              </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              <i class="icon-cross3 mr-2"></i>Close
            </button>
            <button type="submit" class="btn btn-success">
              <i class="fa fa-save mr-2"></i>Update User
            </button>
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


