@extends('admin.layouts.main')
@section('content')

@include('admin.permissions.partials.add_role_modal')

<!-- PAGE-HEADER -->
<div class="page-header">
            <h1 class="page-title">{{ __('auth.roles') }} {{ __('general.setup') }}</h1>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('auth.roles') }} {{ __('general.setup') }}</li>
                </ol>
            </div>
        </div>
        <!-- PAGE-HEADER END -->

<!-- Highlighted tabs -->
    <div class="row bg-white py-4 rounded">
 
   
        <div class="col-md-12">
             
                <div class="row">
                    <div class="col-md-9">
                        <h3 class="card-title mb-0">{{ __('auth.roles') }}</h3>
                    </div>
                    <div class="col-md-3">

                        <a class="modal-effect btn btn-outline-primary d-block d-grid mb-3 float-right" data-effect="effect-rotate-bottom" data-toggle="modal" href="#addRole"><i class="fa fa-plus-circle"></i> {{__('general.add')}} {{__('auth.role')}}</a>
                        </div>

                    </div>

                    @if(count($roles)>0)
                        <table class="table datatable-basic table-striped">
                            <thead>
                                <tr class="text-bold">
                                    <th>{{ __('auth.role') }}</th>
                                    <th class="text-center">
                                        ...
                                    </th>
                                </tr>
                            </thead>
                            <tbody>

                            @foreach($roles as $role)

                            @php
                               $rolePerms = [];
                               $perms = $role->permissions()->get();
                               foreach($perms as $p):
                                  array_push($rolePerms,$p->id);
                                endforeach;
                            @endphp
                                <tr>
                                    <td>{{ strtoupper($role->name) }}</td>
                                    <td class="text-center">

                                          <a href="#role{{$role->id}}0" data-toggle="modal" class="mr-2"><i class="fa fa-pencil text-info"></i></a>
                                          <a  href="#perms{{$role->id}}0" class=" text-success" data-toggle="modal"><i class="fa fa-shield"></i> {{ __('auth.permissions') }} </a>
                                    </td>
                                </tr>
                                
                                @include('admin.permissions.partials.role_edit_form_modal')
                                @include('admin.permissions.partials.role_permissions_modal')
                                       
                                @endforeach
                            </tbody>
                        </table> 
                        {{ $roles->links() }}
                        @else
                            <div class="text-center"><br><br>No data found</div>
                        @endif

        </div>
    </div>

 
    <!-- /highlighted tabs -->

@endsection
    <!-- /List