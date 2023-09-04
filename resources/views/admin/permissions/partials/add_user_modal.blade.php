<div class="modal fade" id="addUser">
            <div class="modal-dialog modal-dialog-centered  modal-lg" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title text-dark"> <i class="fa fa-user-o text-danger"></i>   {{ __('general.add') }} {{ __('auth.user') }}</h6>
                        <button aria-label="Close" class="btn-close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                    </div>
          <form method="POST" action="{{ route('permissions.saveuser') }}">
                
            <div class="modal-body">
                 @csrf
                <div class="row bg-white pb-3">
                    
                    <div class="form-group col-md-6  col-sm-12 ">
                        <label><i class="icon-user mr-2"></i>First name</label>
                        <input type="text"  class="form-control text-bold" placeholder="First Name" name="first_name" value="{{old('first_name')}}" required/>
                    </div>
                    <div class="form-group col-md-6  col-sm-12 ">
                        <label><i class="icon-user mr-2"></i> Last Name</label>
                        <input type="text"  class="form-control text-bold" placeholder="Last name" name="last_name" value="{{old('last_name')}}" required/>
                    </div>
                    <div class="form-group col-md-6  col-sm-12 ">
                        <label><i class="icon-envelope mr-2"></i> Email</label>
                        <input type="text"  class="form-control text-bold" placeholder="Email" name="email" value="{{old('email')}}" required/>
                    </div>
                    <div class="form-group col-md-6  col-sm-12 ">
                        <label><i class="icon-phone mr-2"></i> Mobile</label>
                        <input type="text"  class="form-control text-bold" placeholder="Mobile" name="mobile" value="{{old('mobile')}}" required/>
                    </div>
                    
                    <div class="form-group col-md-6  col-sm-12">
                        @csrf
                        <label class="text-bold">
                            <i class="icon-collaboration mr-2"></i>
                            {{ __('auth.user') }} {{ __('auth.role') }}
                        </label>
                        <select class="form-control form-control-select2 select" name="role_id" data-fouc readonly>
                            @if(empty(old('role_id')))
                            <option selected disabled>Choose Role</option>
                            @endif
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ ($role->id == old('role_id'))?'selected':'' }}>{{ strtoupper($role->name) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-6  col-sm-12">
                        <label class="text-bold">
                            <i class="icon-collaboration mr-2"></i>
                            Access Level
                        </label>
                        <select class="form-control form-control-select2 select" name="level_id" data-fouc readonly>
                            <option selected disabled>Choose Level</option>
                            @foreach($levels as $level)
                            <option value="{{ $level->id }}" {{ ($level->id == @$user->access_level_id)?'selected':'' }}>{{ strtoupper($level->level_name) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-6  col-sm-12 ">
                        <label><i class="icon-lock mr-2"></i>Password</label>
                        <input type="password"  class="form-control text-bold" placeholder="Password (Leave blank to generate)" name="pass" value="{{old('pass')}}"/>
                    </div>

                </div>
                    </div>
                    <div class="modal-footer">
                       
                    <div class="col-md-4">
                            <button type="reset" class="btn btn-secondary reset">
                                <i class="icon-cross3 mr-2"></i>
                                {{ __('general.reset') }}
                            </button>
                            
                            <button type="submit"  class="btn btn-success ">
                                <i class="fa fa-save mr-2"></i>
                                {{ __('general.save') }} {{ __('auth.user') }}
                            </button>
                    </div>
                    </div>
                    
                 </form>

                </div>
            </div>
        </div>
