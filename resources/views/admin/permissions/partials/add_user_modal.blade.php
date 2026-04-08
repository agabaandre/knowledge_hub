<div class="modal fade" id="addUser">
    <div class="modal-dialog modal-dialog-centered  modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title text-dark"> <i class="fa fa-user-o text-danger"></i> {{ __('general.add') }}
                    {{ __('auth.user') }}</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('permissions.saveuser') }}">

                <div class="modal-body">
                    @csrf
                    <div class="row bg-white">

                        <div class="form-group col-md-6  col-sm-12 ">
                            <label><i class="icon-user mr-2"></i>First name</label>
                            <input type="text" class="form-control text-bold" placeholder="First Name"
                                name="first_name" value="{{ old('first_name') }}" required />
                        </div>
                        <div class="form-group col-md-6  col-sm-12 ">
                            <label><i class="icon-user mr-2"></i> Last Name</label>
                            <input type="text" class="form-control text-bold" placeholder="Last name"
                                name="last_name" value="{{ old('last_name') }}" required />
                        </div>
                        <div class="form-group col-md-6  col-sm-12 ">
                            <label><i class="icon-envelope mr-2"></i> Email</label>
                            <input type="text" class="form-control text-bold" placeholder="Email" name="email"
                                value="{{ old('email') }}" required />
                        </div>
                        <div class="form-group col-md-6  col-sm-12 ">
                            <label><i class="icon-phone mr-2"></i> Mobile</label>
                            <input type="text" class="form-control text-bold" placeholder="Mobile" name="mobile"
                                value="{{ old('mobile') }}" required />
                        </div>

                        @if (states_enabled())
                            <div class="form-group col-md-6  col-sm-12">
                                <label class="text-bold">
                                    <i class="icon-collaboration mr-2"></i>
                                    Access Level
                                </label>
                                <select class="form-control form-control-select2 select js-access-level-select" id="add_level_id" name="level_id" data-fouc
                                    readonly>
                                    <option selected disabled>Choose Level</option>
                                    @foreach ($levels as $level)
                                        <option value="{{ $level->id }}" data-level-name="{{ $level->level_name }}"
                                            {{ $level->id == @$user->access_level_id ? 'selected' : '' }}>
                                            {{ strtoupper($level->level_name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="form-group col-md-6  col-sm-12 js-user-role-group" id="add_user_role_group">
                            <label class="text-bold">
                                <i class="icon-collaboration mr-2"></i>
                                {{ __('auth.user') }} {{ __('auth.role') }}
                            </label>
                            <select class="form-control form-control-select2 select js-user-role-select" name="role_id" id="add_role_id" data-fouc readonly>
                                @if (empty(old('role_id')))
                                    <option value="" selected>Choose Role</option>
                                @endif
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}"
                                        {{ $role->id == old('role_id') ? 'selected' : '' }}>
                                        {{ strtoupper($role->name) }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if (states_enabled())
                            <div class="form-group col-md-6  col-sm-12">
                                <label class="text-bold">
                                    <i class="icon-collaboration mr-2"></i>
                                    Member State
                                </label>
                                @include('partials.countries.dropdown')
                            </div>
                        @else
                            <div class="form-group col-md-6  col-sm-12">
                                <label class="text-bold">
                                    <i class="icon-collaboration mr-2"></i>
                                    Administrative Unit
                                </label>
                                @include('partials.adminunits.dropdown')
                            </div>
                        @endif

                        <div class="form-group col-md-6  col-sm-12">
                            <label class="text-bold">
                                <i class="icon-collaboration mr-2"></i>
                                Associated Corporate Source/Member State
                            </label>
                            @include('partials.authors.dropdown', ['allfield' => 'None'])
                        </div>

                        <div class="form-group col-md-6  col-sm-12 ">
                            <label><i class="icon-lock mr-2"></i>Password</label>
                            <input type="password" class="form-control text-bold"
                                placeholder="Password (Leave blank to generate)" name="pass"
                                value="{{ old('pass') }}" />
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-secondary reset">
                        <i class="icon-cross3 mr-2"></i>
                        {{ __('general.reset') }}
                    </button>

                    <button type="submit" class="btn btn-success ">
                        <i class="fa fa-save mr-2"></i>
                        {{ __('general.save') }} {{ __('auth.user') }}
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
