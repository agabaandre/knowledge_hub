@extends('admin.layouts.main')
@section('content')

<!-- Highlighted tabs -->
    <div class="row bg-white">



        <div class="col-md-12">
        <div class="card">
                <div class="card-header header-elements-inline">
                    <h4 class="card-title"><i class="icon-stack4 mr-2"></i> Audit Trail</h4>
                    <div class="header-elements">
                        <div class="list-icons">
                            <a class="list-icons-item" data-action="collapse"></a>
                        </div>
                    </div>
                </div>
                <div class="card-body services-body">
                    <form class="row bg-white" action="{{ url('/admin/logs/user') }}" method="GET">
                        @csrf    
                    <div class="form-group col-md-2 mt-2">
                        <label><i class="icon-calendar mr-2"></i>Start Date</label>
                            <input type="date"  class="form-control date" placeholder="Start Date" name="start" value="{{ @$search->start }}"/>
                        </div>
                        <div class="form-group col-md-2 mt-2">
                            <label><i class="icon-calendar mr-2"></i>End Date</label>
                            <input type="date"  class="form-control date2" placeholder="End Date" name="end" value="{{ @$search->end  }}"/>
                        </div>
                        <div class="form-group col-md-3">
                        <label><i class="icon-user mr-2"></i>User</label>
                            <select class="form-control select2" name="user">
                                @foreach($users as $user)
                                <option value="{{$user->id}}">{{$user->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                        <label><i class="icon-user mr-2"></i>Action</label>
                        @php
                            $actions = ["Delete","Update","Create","Confirmed","Approved","Rejected"];
                        @endphp
                        <select class="form-control" name="action">
                            <option value="">All</option>
                            @foreach($actions as $key => $value)
                                <option value="{{ $value }}" {{ ($value == @$search->action) ? 'selected' : '' }}> {{ $value }}</option>
                            @endforeach
                        </select>

                        </div>
                        <div class="form-group col-md-3 col-sm-12">

                        <input type="submit" class="btn btn-dark" value="SEARCH" />
                        </div>
                    </form>
                </div>
        </div>
            <div class="card">
               
                <div class="card-body services-body">

                    @if(count($trails)>0)
                        <table class="table datatable-basic table-striped">
                            <thead>
                                <tr class="text-bold">
                                    <th>Date</th>
                                    <th>Action</th>
                                    <th>User</th>
                                </tr>
                            </thead>
                            <tbody>

                            @foreach($trails as $row)
                                <tr>
                                     <td>{{ $row->created_at }}</td>
                                     <td>{{ $row->action }}</td>
                                     <td>{{ $row->user->name }}</td>
                                     <td>
                                         <a data-toggle="modal" href="#trail{{ $row->id }}0">Detail</a>
                                     </td>
                                </tr>
                                    @include('admin.permissions.partials.audit_trail_modal')
                                @endforeach
                            </tbody>
                        </table> 
                        {{ $trails->links() }}
                        @else
                            <center><div><br><br>No data found</div></center>
                        @endif

                </div>
            </div>
        </div>
    </div>

 
    <!-- /highlighted tabs -->

@endsection
    <!-- /List