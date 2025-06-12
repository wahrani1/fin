@extends('layouts.dashboard')
@section('title', 'Community Post Comments')
@section('breadcrumb')
    <li class="breadcrumb-item active">Community Comments</li>
@endsection
@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th>Post</th>
            <th>User</th>
            <th>Content</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($comments as $comment)
            <tr>
                <td>{{ $comment->id }}</td>
                <td>{{ $comment->post->title }}</td>
                <td>{{ $comment->user->name }}</td>
                <td>{{ Str::limit($comment->content, 50) }}</td>
                <td>
                    <form action="{{ route('community_comments.destroy', $comment->id) }}" method="POST">
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
