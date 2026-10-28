@extends(admin_layout())
@section('content')

  <!-- PAGE-HEADER -->
  <div class="page-header">
            <h1 class="page-title">{{ __('admin_nav.user_profile') }}</h1>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('admin_nav.users') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('admin_nav.profile') }}</li>
                </ol>
            </div>
        </div>
   <!-- PAGE-HEADER END -->

<div class="container">

@include('account.profile-data')
<!-- /row -->
</div>

@endsection

@section('scripts')

  @include('common.select2')
  @include('common.attachment_js')

@endsection










    