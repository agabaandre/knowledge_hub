@extends(admin_layout())

@section('content')

@include('common.table')

<div class="page-header">
    <h1 class="page-title">Manage Events</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Events</li>
        </ol>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Events</h4>
        <a href="{{ route('admin.events.create') }}" class="btn btn-primary btn-sm"><i class="fa fa-plus mr-1"></i>New Event</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th style="width:6%">#</th>
                        <th>Title</th>
                        <th>Dates</th>
                        <th>Venue</th>
                        <th>Status</th>
                        <th style="width:220px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($events as $i => $ev)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ Str::limit($ev->title, 80) }}</td>
                        <td>
                            @php
                                $start = $ev->startdate ? \Carbon\Carbon::parse($ev->startdate)->format('M d, Y') : '';
                                $end   = $ev->enddate ? \Carbon\Carbon::parse($ev->enddate)->format('M d, Y') : '';
                            @endphp
                            {{ $start }} @if($end) - {{ $end }} @endif
                        </td>
                        <td>{{ $ev->venue ?? ($ev->is_online ? 'Online' : '-') }}</td>
                        <td>
                            @php
                                $statusClass = 'badge-secondary';
                                if ($ev->status === 'active') {
                                    $statusClass = 'badge-success';
                                } elseif ($ev->status === 'cancelled') {
                                    $statusClass = 'badge-danger';
                                } elseif ($ev->status === 'pending') {
                                    $statusClass = 'badge-warning';
                                }
                            @endphp
                            <span class="badge {{ $statusClass }}">{{ ucfirst($ev->status ?? 'pending') }}</span>
                        </td>
                        <td>
                            <a href="{{ route('admin.events.edit', $ev->id) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa fa-edit"></i></a>
                            <a href="{{ route('admin.events.show', $ev->id) }}" class="btn btn-sm btn-outline-secondary" title="Preview (Admin)"><i class="fa fa-eye"></i></a>
                            <a href="{{ route('public.events.show', $ev->id) }}" target="_blank" class="btn btn-sm btn-outline-info" title="Web Preview"><i class="fa fa-external-link mr-1"></i>Web Preview</a>
                            <form action="{{ route('admin.events.destroy', $ev->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this event?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

