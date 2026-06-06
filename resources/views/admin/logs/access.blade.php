@extends(admin_layout())
@section('content')

<!-- PAGE-HEADER -->
<div class="page-header">
            <h1 class="page-title">Site Access Logs</h1>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Logs</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Site Access Logs</li>
                </ol>
            </div>
        </div>
        <!-- PAGE-HEADER END -->

<!-- Highlighted tabs -->
    <div class="row bg-white py-4 rounded">

        <div class="col-md-12">
            @include('common.table')

            <div class="row mb-3">
                <div class="col-md-8">
                    <form method="get" class="form-inline d-flex" action="{{ url('admin/logs/access') }}">
                        <input type="text" name="term" value="{{ request('term') }}" class="form-control mr-2" placeholder="Search IP, Country, City">
                        <select name="user_id" class="form-control mr-2">
                            <option value="">All Users</option>
                            @foreach(\App\Models\User::orderBy('name')->get() as $u)
                                <option value="{{ $u->id }}" {{ request('user_id')==$u->id?'selected':'' }}>{{ $u->name }}</option>
                            @endforeach
                        </select>
                        <select name="rows" class="form-control mr-2">
                            @foreach([24,50,100,200] as $r)
                                <option value="{{ $r }}" {{ request('rows')==$r?'selected':'' }}>{{ $r }}/page</option>
                            @endforeach
                        </select>
                        <button class="btn btn-outline-primary" type="submit"><i class="fa fa-search"></i></button>
                    </form>
                </div>
            </div>

            @if(count($logs)>0)
                <table class="table table-striped table-bordered align-middle">
                    <thead>
                        <tr class="text-bold">
                            <th style="width:6%">#</th>
                            <th>Date</th>
                            <th>IP Address</th>
                            <th>Country</th>
                            <th>City</th>
                            <th>Coordinates</th>
                            <th>User</th>
                            <th>Model</th>
                            <th>Resource</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $idx => $log)
                        <tr>
                            <td>{{ $logs->firstItem() + $idx }}</td>
                            <td>{{ time_ago($log->created_at) }}</td>
                            <td>{{ $log->ip_address }}</td>
                            <td>{{ strtoupper($log->country) }}</td>
                            <td>{{ strtoupper($log->city) }}</td>
                            <td>{{ @$log->lat }},{{ @$log->long }}</td>
                            <td>{{ $log->user->name ?? '-' }}</td>
                            <td>{{ $log->model_name }}</td>
                            <td>
                                @if(!empty($log->publication_id))
                                    <a href="{{ publication_url($log->publication_id) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Open</a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $logs->appends(request()->all())->links() }}
            @else
                <div class="text-center"><br><br>No data found</div>
            @endif

        </div>
    </div>

 
    <!-- /highlighted tabs -->

@endsection
    <!-- /List