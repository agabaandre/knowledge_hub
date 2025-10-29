@extends('layouts.plain')

@section('styles')
@endsection

@section('content')
    <!-- Col -->
    <section class="middle" style="background: #f4f5f7; padding: 2rem 0;">
        <div class="container">

            @include('account.profile-data')

            <!-- /row -->
        </div>
    </section>
@endsection

@section('scripts')
    @include('common.select2')
    @include('common.attachment_js')
@endsection
