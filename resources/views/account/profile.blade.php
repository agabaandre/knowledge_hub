@extends('layouts.plain')

@section('styles')

@endsection

@section('content')
<!-- Col -->
<section class="middle gray">
<div class="container">

@include('layouts.partials.alerts')

@include('account.profile-data')

<!-- /row -->
</div>
</section>
@endsection

@section('scripts')

	@include('common.select2')
	@include('common.attachment_js')

@endsection