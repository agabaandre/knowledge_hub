@extends(admin_layout())

@section('styles')
    @include('errors.partials.error_page_admin_styles')
@endsection

@section('content')
    @include('errors.partials.not_found_content')
@endsection
