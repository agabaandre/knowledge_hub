@extends('admin.layouts.tabular')

@section('styles')
 @include('common.table')
@endsection

@section('content')
<!-- PAGE-HEADER -->
<div class="page-header">
    <h1 class="page-title">Authors</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Dropdown Lists</a></li>
            <li class="breadcrumb-item active" aria-current="page">Authors</li>
        </ol>
    </div>
</div>
<!-- PAGE-HEADER END -->
<div class="row">
	<div class="card col-lg-12">
	

		<div class="card-body text-left">
		<?php echo $uitable; ?>
		</div>

	</div>


    @endsection