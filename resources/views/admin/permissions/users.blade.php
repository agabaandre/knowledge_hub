@extends('admin.layouts.main')
@section('content')

  <?php 
  
  $session = current_user();

  ?>

<!-- PAGE-HEADER -->
<div class="page-header">
    <h1 class="page-title">Users</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Users</li>
        </ol>
    </div>
</div>
<!-- PAGE-HEADER END -->

@include('admin.permissions.partials.add_user_modal')

<!-- Highlighted tabs -->

        <div class="col-md-12 bg-white py-4 rounded">
            <div class="row">
                <div class="col-md-9">
                    <h3 class="card-title mb-0">{{__('auth.users')}}</h3>
                </div>
                <div class="col-md-3">
                    <a class="modal-effect btn btn-outline-primary d-block d-grid mb-3 float-right" data-effect="effect-rotate-bottom" data-toggle="modal" href="#addUser"><i class="fa fa-plus-circle"></i> {{__('general.add')}} {{__('auth.user')}}</a>
                </div>
            </div>

                        <form action="{{ route('permissions.filerusers') }}" method="GET">
                            @csrf
                            <div class="row bg-white pb-3">
                                

                                <div class="form-group col-md-12">
                                    <label>Search</label>
                                    <input type="text" name="term"  value="{{@$search->term}}" class="form-control" placeholder="Search by Name,Email,Phone Number etc">
                                </div>

                                <div class="col-md-3 mt-4">
                                    <button type="submit" class="btn btn-dark"><i class="icon-filter4"></i> 
                                    {{ __('general.search') }} {{ __('general.users') }}</button>
                                </div>
                            </div>
                        </form>

                <hr>

                    @if(count($users)>0)
                        <table class="table table-striped">
                            <thead>
                                <tr class="text-bold">
                                    <th>{{ __('auth.user') }}</th>
                                    <th>{{ __('general.email') }}</th>
                                    <th>{{ __('general.phone') }}</th>
                                    <th>{{ __('general.status') }}</th>
                                    <th>{{ __('Country') }}</th>
                                    <th>{{ __('auth.role') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>

                            @foreach($users as $user)

                                @php
                                  $userRole = get_role($user->id);

                                  $statuses = array(
                                  "0"=>"InActive",
                                  "2"=>"Restricted",
                                  "3"=>"Reset",
                                  "1"=>"Active");

                                @endphp

                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone_number ?? '' }}</td>
                                    <td><b class="badge badge-dark">{{ $statuses[$user->status] }}</b></td>
                                    <td>{{ $user->country_name }}</td>
                                    <td>{{ ($userRole)?(strtoupper((@$userRole->name)?$userRole->name:'N/A')):'' }}</td>
                                    <td class="text-center">
                                             @include('admin.permissions.partials.user_row_dropdown')
                                             @include('admin.permissions.partials.user_edit_form_modal')
                                             @include('admin.permissions.partials.reset_modal')
                                             @include('admin.permissions.partials.delete_user_modal')
                                    </td>
                                </tr>

                                @endforeach
                            </tbody>
                        </table> 
                         {{ $users->links() }}
                        @else
                            <div class="text-center"><br><br>No data found</div>
                        @endif

        </div>
  
    <!-- /highlighted tabs -->

@endsection
    <!-- /List