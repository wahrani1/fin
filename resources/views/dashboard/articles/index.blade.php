@extends('layouts.dashboard')
@section('title', 'Articles08', 'Articles')
@section('breadcrumb')
    <li class="breadcrumb-item active">Articles</li>
@endsection
@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="mb-4">
        <a href="{{ route('articles.create') }}" class="btn btn-sm btn-outline-success">Create New Article</a>
    </div>

    @if($articles->count())
        <table class="table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Description</th>
                <th>Category</th>
                <th>Era</th>
                <th>Governorate</th>
                <th>Images</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($articles as $article)
                <tr>
                    <td>{{ $article->id }}</td>
                    <td>{{ $article->name }}</td>
                    <td>{{ Str::limit($article->description, 50) }}</td>
                    <td>{{ $article->category }}</td>
                    <td>{{ $article->era->name ?? 'N/A' }}</td>
                    <td>{{ $article->governorate->name ?? 'N/A' }}</td>
                    <td>
                        @foreach($article->images as $image)
                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="" height="50" width="50" class="me-1">
                        @endforeach
                    </td>
                    <td>
                        <a href="{{ route('articles.edit', $article->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('articles.destroy', $article->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $articles->links() }}
    @else
        <div class="alert alert-info">No articles available.</div>
    @endif
@endsection
