@extends('layouts.admin')

@section('content')
    <h1>Content Requests</h1>
    <a href="{{ route('admin.content-requests.create') }}" class="btn btn-primary">Create New Request</a>

    <table class="table">
        <thead>
            <tr>
                <th>Subject</th>
                <th>Description</th>
                <th>Country</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($contentRequests as $request)
                <tr>
                    <td>{{ $request->subject }}</td>
                    <td>{{ $request->description }}</td>
                    <td>{{ $request->country->name }}</td>
                    <td>{{ $request->email }}</td>
                    <td>
                        <a href="{{ route('admin.content-requests.edit', $request->id) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('admin.content-requests.destroy', $request->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $contentRequests->links() }}
@endsection 