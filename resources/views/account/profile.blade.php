@extends('layouts.plain')

@section('styles')

@endsection

@section('content')
<!-- Col -->
<section class="middle gray">
<div class="container">
<div class="row">
    <div class="col-md-5 col-lg-5 col-xl-5 col-xs-12 col-md-pull-2">
        
        <div class="card">
            <div class="card-header pb-0 border-bottom">
                <div class="item-user pro-user">
                    <h4 class="pro-user-username tx-15 pt-2 mt-4 mb-1">
                        {{ $profile->name }}
                    </h4>
                    <h6 class="pro-user-desc tx-13 mb-3 font-weight-normal text-muted">
                        {{-- $profile->group_name --}}
                    </h6>
                </div>
            </div>
            <div class="card-body">
                
               <h4 class=" pb-1">Change Password</h4>
                <div class="form-group">
                    <label class="form-label">Old Password</label>
                    <input type="password" class="form-control" placeholder="Current Password">
                </div>
                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="new_password" class="form-control" placeholder="New Password">
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <input type="new_password" class="form-control" placeholder=" Confirm New Password">
                </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-success">Update</button>
            </div>
        </div>
    </div>

    <!-- Col -->
    <div class="col-md-7 col-lg-7 col-xl-7">
        <div class="card">
            <div class="card-body">
                <div class="mb-4 ft-md"><h4>Personal Information</h4></div>
                <form class="form-horizontal">
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
                                <input type="text" class="form-control" name="firstname" placeholder="First Name"
                                    value="{{ $profile->firstname }}" >
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label"> Last Name</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" name="lastname" class="form-control" placeholder="Last Name"
                                    value="{{ $profile->lastname }}">
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
                                    value="{{ $profile->email }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Role</label>
                            </div>
                            <div class="col-md-9">
                                <p>
                                    {{-- $profile->group_name --}}
                                </p>
                                N/A

                            </div>
                        </div>
                    </div>

                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Your Interests</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="form-control" placeholder="Interests"
                                    value="{{ $profile->interests }}">
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-success waves-effect waves-light">Update Profile</button>
            </div>
        </div>
    </div>
    <!-- /Col -->
</div>
<!-- /row -->
</div>
</section>
@endsection