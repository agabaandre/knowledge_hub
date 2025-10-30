@extends('admin.layouts.main')
@section('content')

 @include('common.table')
  <!-- PAGE-HEADER -->
  <div class="page-header">
            <h1 class="page-title">Permissions</h1>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Permissions</li>
                </ol>
            </div>
        </div>
   <!-- PAGE-HEADER END -->

<!-- Highlighted tabs -->
    <div class="row bg-white bg-white py-4 rounded">

        
    @include('admin.permissions.partials.add_permission_modal')

        <div class="col-md-12">
          
                <div class="row">
                <div class="col-md-9">
                    <h3 class="card-title mb-0">{{ __('auth.permissions') }}</h3>
                </div>
                <div class="col-md-3">

                    <a class="modal-effect btn btn-outline-primary d-block d-grid mb-3 float-right" data-effect="effect-rotate-bottom" data-toggle="modal" href="#addPermission"><i class="fa fa-plus-circle"></i> Add Permission</a>
                    </div>

                </div>


                    @if(count($permissions)>0)
                        @php
                            $grouped = [];
                            foreach ($permissions as $p) {
                                $parts = explode('.', $p->name);
                                $group = strtoupper($parts[0] ?? 'GENERAL');
                                $grouped[$group][] = $p;
                            }
                        @endphp

                        <div class="mb-3 d-flex justify-content-end">
                            <div class="input-group" style="max-width:320px;">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div>
                                <input type="text" id="perm-search" class="form-control" placeholder="Search permissions...">
                            </div>
                        </div>

                        <div id="perm-accordion">
                        @foreach($grouped as $group => $items)
                            <div class="card mb-3">
                                <div class="card-header d-flex justify-content-between align-items-center" data-toggle="collapse" data-target="#group-{{ Str::slug($group) }}" style="cursor:pointer;">
                                    <strong>{{ $group }}</strong>
                                    <i class="fa fa-chevron-down"></i>
                                </div>
                                <div id="group-{{ Str::slug($group) }}" class="collapse show" data-parent="#perm-accordion">
                                    <div class="card-body p-0">
                                        <table class="table table-striped table-bordered align-middle mb-0 perm-table">
                                            <thead>
                                                <tr>
                                                    <th style="width:6%">#</th>
                                                    <th>{{ __('auth.permission') }} {{ __('general.name') }}</th>
                                                    <th>Description</th>
                                                    <th class="text-center" style="width:120px">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($items as $idx => $perm)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td class="perm-name">{{ $perm->name }}</td>
                                                    <td class="perm-desc">{{ strtoupper($perm->description) }}</td>
                                                    <td class="text-center">
                                                        <div class="g-2">
                                                            <a class="btn text-success btn-sm" data-toggle="tooltip" data-original-title="Audit"><span class="fa fa-bar-chart fs-14"></span></a>
                                                            <a href="#perm{{$perm->id}}0" class="btn text-info btn-sm" data-toggle="modal">
                                                                <span data-toggle="tooltip" data-original-title="Edit" class="fe fe-edit fs-14"></span>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @include('admin.permissions.partials.permission_edit_form_modal')
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        </div>

                        {{ $permissions->links() }}
                        @else
                            <div class="text-center"><br><br>No data found</div><
                        @endif

                </div>
        </div>
    </div>

 
    <!-- /highlighted tabs -->

@endsection

@section('scripts')
<script>
    $(function(){
        $('#perm-search').on('keyup', function(){
            var term = $(this).val().toLowerCase();
            $('.perm-table tbody tr').each(function(){
                var nameTxt = $(this).find('.perm-name').text().toLowerCase();
                var descTxt = $(this).find('.perm-desc').text().toLowerCase();
                $(this).toggle(nameTxt.indexOf(term) > -1 || descTxt.indexOf(term) > -1);
            });
        });
    });
</script>
@endsection


    