<div class="row">
    <div class="col-md-5 col-lg-5 col-xl-5 col-xs-12 col-md-pull-2">
        
        <div class="card">
            <div class="card-header pb-0 border-bottom">
                <div class="item-user pro-user">
                    <h4 class="pro-user-username tx-15 pt-2 mt-1 mb-4">
                        Greetings {{ ($user->first_name)?$user->first_name:$user->name }}
                    </h4>
                </div>
            </div>
            <form class="form-horizontal" method="post" action="{{ $password_route ?? route('account.auth_update')}}">
               @csrf
            <div class="card-body">
                
               <h4 class=" pb-1">Change Password</h4>
                <div class="form-group">
                    <label class="form-label">Old Password</label>
                    <input type="password" class="form-control" placeholder="Current Password" name="old_pass">
                </div>
                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" class="form-control" placeholder="New Password" name="password">
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" placeholder=" Confirm New Password" name="password_confirmation">
                </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-success">Update</button>
            </div>
            </form>
        </div>
    </div>

    <!-- Col -->
    <div class="col-md-7 col-lg-7 col-xl-7">
        <div class="card">
         <form class="form-horizontal" method="post" enctype="multipart/form-data" action="{{ $update_route ?? route('account.update')}}" >
               @csrf
            <div class="card-body">
                <div class="mb-4 ft-md"><h4>Personal Information</h4></div>
                   <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Language</label>
                            </div>
                            <div class="col-md-9">
                                @include('common.lang')
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">First Name</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="first_name" placeholder="First Name"
                                    value="{{ $user->first_name }}" >
                                    @error('first_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label"> Last Name</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" name="last_name" class="form-control" placeholder="Last Name"
                                    value="{{ $user->last_name }}">
                                    @error('last_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Email</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="form-control" placeholder="Email"
                                    value="{{ $user->email }}" name="email" required>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Phone Number</label>
                            </div>
                            <div class="col-md-9">
                                <input type="tel" class="form-control" placeholder="Phone Number"
                                    name="phone_number" value="{{ $user->phone_number }}">
                                    @error('phone_number')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Your Interests</label>
                            </div>
                            <div class="col-md-9">
                                    @include('partials.publications.subtheme_dropdown',['field'=>'preferences[]','multiple'=>'multiple','selected'=>$preferences])
                                    @error('preferences')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Photo</label>
                            </div>
                            <div class="col-md-9">
                                    <input type="file" name="photo" id="cover" />
                                    @error('photo')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    @php
                                    if(@$user):
                                        $image_link = asset('public/storage/uploads/users/'.$user->photo);
                                    else:
                                        $image_link = asset('assets/images/user.jpg');
                                    endif;
                                   @endphp
                                   <div class="row justify-content-center">
                                       <div  class="cover_preview py-2" style="min-height:120px; min-width:120px;max-height:120px; max-width:120px; background-image: url({{ $image_link }}); background-size:cover; background-position:center; background-repeat:no-repeat;">
                                         
                                       </div>
                                  </div>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-success waves-effect waves-light">Update Profile</button>
            </div>
            

            </form>
        </div>
    </div>
    <!-- /Col -->
</div>