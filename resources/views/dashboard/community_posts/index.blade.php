@extends('layouts.dashboard')
@section('title', 'Community Posts')
@section('breadcrumb')
    <li class="breadcrumb-item active">Community Posts</li>
@endsection
@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th>Title</th>
            <th>User</th>
            <th>Content</th>
            <th>Images</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($posts as $post)
            <tr>
                <td>{{ $post->id }}</td>
                <td>{{ $post->title }}</td>
                <td>{{ $post->user->name }}</td>
                <td>{{ Str::limit($post->content, 50) }}</td>
                <td>
                    @foreach($post->images as $image)
                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="" height="50" width="50" class="me-1">
                    @endforeach
                </td>
                <td>
                    <form action="{{ route('community_posts.destroy', $post->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $posts->links() }}
@endsection
