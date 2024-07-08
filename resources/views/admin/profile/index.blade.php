@extends('admin.layouts.main')
@section('content')

  <!-- PAGE-HEADER -->
  <div class="page-header">
            <h1 class="page-title">User Profile</h1>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">User</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Profile</li>
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










    