@extends('admin.layouts.main')
@section('content')

  <!-- PAGE-HEADER -->
  <div class="page-header">
            <h1 class="page-title">User Profile</h1>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">User</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Profile</li>
                </ol>
            </div>
        </div>
   <!-- PAGE-HEADER END -->

<!-- Highlighted tabs -->
    <div class="row bg-white bg-white py-4 rounded align-items-center">

    <div class="col-lg-6">

    <div class="form-group">
        <label class="text-muted">First Name</label>
        <div>{{$user->first_name}}</div>
    </div>

    <div class="form-group">
        <label class="text-muted">Last Name</label>
        <div>{{$user->last_name}}</div>
    </div>

    <div class="form-group">
        <label class="text-muted">Email</label>
        <div>{{$user->email}}</div>
    </div>

    </div>

    <div class="col-lg-3">

        <div class="form-group">
            <label class="text-muted">Phone Number</label>
            <div>{{$user->phone_number}}</div>
        </div>

        <div class="form-group">
            <label class="text-muted">Job Title</label>
            <div>{{$user->job_title ?? 'N/A'}}</div>
        </div>

        <div class="form-group">
            <label class="text-muted">Country</label>
            <div>{{ ($user->country)?$user->country->name:"N/A" }}</div>
        </div>
        
    </div>

    <div class="col-lg-3">

        <div class="form-group">
           
            <img class=" img img-thumbnail" width="170px" src="{{ storage_link('uploads/users/').$user->photo }}">
            
        </div>


        </div>
     
    </div>

 
    <!-- /highlighted tabs -->

@endsection


    