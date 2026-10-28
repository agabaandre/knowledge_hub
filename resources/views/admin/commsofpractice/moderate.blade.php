@extends(admin_layout('tabular'))

@section('styles')
 @include('common.table')
@endsection

@section('content')
<!-- PAGE-HEADER -->
<div class="page-header">
    <h1 class="page-title">{{ __('admin_nav.communities_membership') }}</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('admin_nav.dropdown_lists') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('admin_nav.communities_membership') }}</li>
        </ol>
    </div>
</div>
<!-- PAGE-HEADER END -->
<div class="row">
	<div class="card col-lg-12">
		<div class="card-header text-left">

		</div>
	
		<div class="card-body text-left">



		</div>

	</div>

    @endsection