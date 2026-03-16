@extends(admin_layout())

@section('content')
<div class="page-header">
    <h1 class="page-title">Create Event</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.events.index') }}">Events</a></li>
            <li class="breadcrumb-item active">Create</li>
        </ol>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.events.store') }}">
            @include('admin.events.form')
        </form>
    </div>
 </div>
@include('partials.general.summernote')
@endsection


