@extends('admin.layouts.main')
@section('styles')
<style>
/* Match Bootstrap-like pagination used on Authors page */
.dataTables_wrapper .dataTables_paginate { padding-top: 8px; }
.dataTables_wrapper .dataTables_paginate .paginate_button {
    border: 1px solid #e2e8f0 !important;
    background: #fff !important;
    color: #1f2937 !important;
    padding: .25rem .5rem !important;
    margin: 0 .125rem !important;
    border-radius: .25rem !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current,
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: #1f2937 !important;
    color: #fff !important;
    border-color: #1f2937 !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
    color: #9ca3af !important;
    background: #fff !important;
}
</style>
@endsection

@section('content')

@include('common.table')

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

                        <form id="user-filters" action="{{ route('permissions.filerusers') }}" method="GET">
                            @csrf
                            <div class="row bg-white pb-3">
                                

                                <div class="form-group col-md-12">
                                    <label>Search</label>
                                    <input type="text" name="term"  value="{{@$search->term}}" class="form-control" placeholder="Search by Name,Email,Phone Number etc">
                                </div>

                                <div class="form-group col-md-3">
                                    <label>Africa CDC Staff</label>
                                    <select name="is_staff" id="filter_staff" class="form-control">
                                        <option value="">All</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Verified</label>
                                    <select name="verified" id="filter_verified" class="form-control">
                                        <option value="">All</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mt-4">
                                    <button type="submit" class="btn btn-dark"><i class="icon-filter4"></i> {{ __('general.search') }} {{ __('general.users') }}</button>
                                </div>
                            </div>
                        </form>

                <hr>

                    @if(count($users)>0)
                        <table class="table table-striped table-bordered align-middle" id="users-table">
                            <thead>
                                <tr class="text-bold">
                                    <th style="width:5%">#</th>
                                    <th style="width:10%">{{ __('auth.user') }}</th>
                                    <th>Contact</th>
                                    <th>Verified</th>
                                    <th>Status</th>
                                    <th>Type</th>
                                    <th>Last Login</th>
                                    <th>{{ __('auth.role') }}</th>
                                    <th style="width:22%">Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table> 
                        
                        @else
                            <div class="text-center"><br><br>No data found</div>
                        @endif

        </div>
  
    <!-- /highlighted tabs -->

    <!-- Hidden forms to avoid CSRF header mismatches -->
    <form id="sendVerifyForm" method="POST" action="{{ route('permissions.sendverification') }}" style="display:none;">
        @csrf
        <input type="hidden" name="id" id="send_verify_user_id">
    </form>
    <form id="markVerifyForm" method="POST" action="{{ route('permissions.verifyuser') }}" style="display:none;">
        @csrf
        <input type="hidden" name="id" id="mark_verify_user_id">
    </form>

@endsection
    <!-- /List

@section('scripts')
<script>
    $(function(){
            var table = $('#users-table').DataTable({
            processing: true,
            serverSide: false,
            searching: true,
            lengthChange: true,
            pagingType: 'simple_numbers',
            ajax: {
                url: '{{ route('permissions.users') }}',
                data: function(d){
                    d.term = $('input[name=term]').val();
                    d.is_staff = $('#filter_staff').val();
                    d.verified = $('#filter_verified').val();
                },
                dataSrc: 'data'
            },
            order: [],
            deferRender: true,
            autoWidth: false,
            responsive: true,
            columns: [
                { data: 0, orderable: true, width: '5%' },
                { data: 1, orderable: true, width: '12%' },
                { data: 2, orderable: false },
                { data: 3, orderable: false },
                { data: 4, orderable: false },
                { data: 5, orderable: false },
                { data: 6, orderable: true },
                { data: 7, orderable: true },
                { data: 8, orderable: false }
            ],
            columnDefs: [
                { targets: [8], orderable:false, searchable:false },
                { targets: [3,4,5], className: 'text-center' }
            ]
        });

            // Global modals handlers
            $(document).on('click', '.btn-edit-user', function(){
                var id = $(this).data('id');
                var name = $(this).data('name');
                var email = $(this).data('email');
                var phone = $(this).data('phone');
                var verified = $(this).data('verified');
                var status = $(this).data('status');
                $('#edit_user_id').val(id);
                $('#edit_name').val(name);
                $('#edit_email').val(email);
                $('#edit_phone').val(phone);
                $('#edit_verified').prop('checked', !!verified);
                $('#edit_status').val(status);
                $('#editUserModal').modal('show');
            });
            $('#user-filters').on('submit', function(e){ e.preventDefault(); table.ajax.reload(); });
            $(document).on('click', '.btn-reset-user', function(){
                $('#reset_user_id').val($(this).data('id'));
                $('#resetUserModal').modal('show');
            });
            $(document).on('click', '.btn-delete-user', function(){
                $('#delete_user_id').val($(this).data('id'));
                $('#deleteUserModal').modal('show');
            });
            $(document).on('click', '.btn-send-verify', function(){
                var id = $(this).data('id');
                $('#send_verify_user_id').val(id);
                document.getElementById('sendVerifyForm').submit();
            });

            $(document).on('click', '.btn-mark-verify', function(){
                var id = $(this).data('id');
                $('#mark_verify_user_id').val(id);
                document.getElementById('markVerifyForm').submit();
            });
    });
</script>
@include('admin.permissions.partials.global_user_modals')
@endsection