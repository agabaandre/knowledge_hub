@extends('admin.layouts.main')
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
             
                <div class="row">
                  
                    @if(count($logs)>0)
                        <table class="table datatable-basic table-striped">
                            <thead>
                                <tr class="text-bold">
                                    <th>Date</th>
                                    <th>IP Adrress</th>
                                    <th>Country</th>
                                    <th>City</th>
                                    <th>Cordinates</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>

                            @foreach($logs as $log)

                                <tr>
                                    
                                    <td>{{ $log->created_at }}</td>
                                    <td>{{ $log->ip_address }}</td>
                                    <td>{{ strtoupper($log->country) }}</td>
                                    <td>{{ strtoupper($log->city) }}</td>
                                    <td>{{ $log->lat.",".$log->long }}</td>
                                    <td class="text-center">
                                        <a href="#role{{$log->id}}0" data-toggle="modal" class="mr-2"><i class="fa fa-pencil text-info"></i></a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table> 
                        {{ $logs->links() }}
                        @else
                            <div class="text-center"><br><br>No data found</div>
                        @endif

        </div>
    </div>

 
    <!-- /highlighted tabs -->

@endsection
    <!-- /List