@extends('admin.layouts.main')

@section('content')
    <!-- PAGE-HEADER -->
    <div class="page-header">
        <h1 class="page-title">{{ $dashboard->title }}</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $dashboard->title }}</li>
            </ol>
        </div>
    </div>
    <!-- PAGE-HEADER END -->

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Dashboard</h5>
                        <!-- Display the publication description -->
                        <p>{!! $dashboard->description !!}</p>
                        <!-- Embed the publication link in an iframe -->
                        <iframe src="{{ $dashboard->publication }}"
                            style="width: 100%; height: 500px; border: none;"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection
