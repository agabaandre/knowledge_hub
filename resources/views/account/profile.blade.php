@extends('layouts.plain')

@section('styles')

@endsection

@section('content')
<!-- Col -->
<section class="middle gray">
<div class="container">

@include('layouts.partials.alerts')

<div class="row">
    <div class="col-md-5 col-lg-5 col-xl-5 col-xs-12 col-md-pull-2">
        
        <div class="card">
            <div class="card-header pb-0 border-bottom">
                <div class="item-user pro-user">
                    <h4 class="pro-user-username tx-15 pt-2 mt-1 mb-4">
                        Greetings {{ ($profile->first_name)?$profile->first_name:$profile->name }}
                    </h4>
                </div>
            </div>
            <form class="form-horizontal" method="post" action="{{ route('account.auth_update')}}">
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
         <form class="form-horizontal" method="post" action="{{ route('account.update')}}">
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
                                    value="{{ $profile->first_name }}" >
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
                                    value="{{ $profile->last_name }}">
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
                                    value="{{ $profile->email }}" name="email" required>
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
                                    name="phone_number" value="{{ $profile->phone_number }}">
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
                                <label>Preferences *</label>
                                    @include('partials.tags.dropdown',['field'=>'preferences[]','selected'=>$preferences])

                                    @error('preferences')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
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
<!-- /row -->
</div>
</section>
@endsection

@section('scripts')

	@include('common.select2')

@endsection