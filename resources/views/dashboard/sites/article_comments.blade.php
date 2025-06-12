@extends('layouts.dashboard')
@section('title', 'Article Comments')
@section('breadcrumb')
    <li class="breadcrumb-item active">Comments</li>
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
            <th>Content</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($comments as $comment)
            <tr>
                <td>{{ $comment->id }}</td>
                <td>{{ $comment->article->name }}</td>
                <td>{{ $comment->user->name }}</td>
                <td>{{ Str::limit($comment->content, 50) }}</td>
                <td>{{ $comment->is_approved ? 'Approved' : 'Pending' }}</td>
                <td>
                    @if(!$comment->is_approved)
                        <form action="{{ route('comments.approve', $comment->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-success">Approve</button>
                        </form>
                    @endif
                    <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $comments->links() }}
@endsection
