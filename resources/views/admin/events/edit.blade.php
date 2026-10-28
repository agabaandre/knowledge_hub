@extends(admin_layout())

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ __('admin_nav.edit_event') }}</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.events.index') }}">{{ __('admin_nav.events') }}</a></li>
            <li class="breadcrumb-item active">{{ __('general.edit') }}</li>
        </ol>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.events.update', $event->id) }}">
            @method('PUT')
            @include('admin.events.form', ['event'=>$event])
        </form>
    </div>
 </div>
@include('partials.general.summernote')
@endsection


