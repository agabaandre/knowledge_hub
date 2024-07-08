@extends('admin.layouts.tabular')

@section('styles')
 @include('common.table')
@endsection

@section('content')
<!-- PAGE-HEADER -->
<div class="page-header">
    <h1 class="page-title">Communities of Practice</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Dropdown Lists</a></li>
            <li class="breadcrumb-item active" aria-current="page">Communities of Practice</li>
        </ol>
    </div>
</div>
<!-- PAGE-HEADER END -->
<div class="row">
	<div class="card col-lg-12">
		<div class="card-header text-left">

		</div>
	
		<div class="card-body text-left">

		  {!! $uitable !!}

		</div>

	</div>

    @endsection