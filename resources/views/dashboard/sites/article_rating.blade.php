@extends('layouts.dashboard')
@section('title', 'Article Ratings')
@section('breadcrumb')
    <li class="breadcrumb-item active">Ratings</li>
@endsection
@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th>Article</th>
            <th>User</th>
            <th>Rating</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($ratings as $rating)
            <tr>
                <td>{{ $rating->id }}</td>
                <td>{{ $rating->article->name }}</td>
                <td>{{ $rating->user->name }}</td>
                <td>{{ $rating->rating }}/5</td>
                <td>{{ $rating->is_approved ? 'Approved' : 'Pending' }}</td>
                <td>
                    @if(!$rating->is_approved)
                        <form action="{{ route('ratings.approve', $rating->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-success">Approve</button>
                        </form>
                    @endif
                    <form action="{{ route('ratings.destroy', $rating->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $ratings->links() }}
@endsection

