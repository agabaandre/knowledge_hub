@extends('admin.layouts.main')

@section('styles')
    @include('common.table')
    <style>
        .card { border: 1px solid #e2e8f0; border-radius: 0; margin-bottom: 1.5rem; }
        .card-header { background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 1rem 1.5rem; }
        .card-body { padding: 1.5rem; }
    </style>
@endsection

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

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">{{ __('auth.roles') }}</h3>
                    <div>
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addRole">
                            <i class="fa fa-plus"></i> {{__('general.add')}} {{__('auth.role')}}
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if(session('alert-success'))
                    <div class="alert alert-success">{{ session('alert-success') }}</div>
                @endif
                @if(session('alert-danger'))
                    <div class="alert alert-danger">{{ session('alert-danger') }}</div>
                @endif

                @if(count($roles) > 0)
                    <div class="table-responsive">
                        <table id="roles-table" class="table table-striped table-bordered table-hover" style="border-radius: 0;">
                            <thead>
                                <tr>
                                    <th width="60px">#</th>
                                    <th>{{ __('auth.role') }}</th>
                                    <th width="200px" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roles as $index => $role)
                                    @php
                                       $rolePerms = [];
                                       $perms = $role->permissions()->get();
                                       foreach($perms as $p):
                                          array_push($rolePerms,$p->id);
                                        endforeach;
                                    @endphp
                                    <tr>
                                        <td><span class="text-muted">{{ $roles->firstItem() + $index }}</span></td>
                                        <td><strong>{{ strtoupper($role->name) }}</strong></td>
                                        <td class="text-center">
                                            <a href="#role{{$role->id}}0" data-toggle="modal" class="btn btn-sm btn-outline-primary mr-1" title="Edit Role">
                                                <i class="fa fa-pencil mr-1"></i> Edit
                                            </a>
                                            <a href="#perms{{$role->id}}0" class="btn btn-sm btn-outline-success" data-toggle="modal" title="Manage Permissions">
                                                <i class="fa fa-shield"></i> {{ __('auth.permissions') }}
                                            </a>
                                        </td>
                                    </tr>
                                    
                                    @include('admin.permissions.partials.role_edit_form_modal')
                                    @include('admin.permissions.partials.role_permissions_modal')
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $roles->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <p class="text-muted">No roles found</p>
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addRole">
                            <i class="fa fa-plus"></i> {{__('general.add')}} {{__('auth.role')}}
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
