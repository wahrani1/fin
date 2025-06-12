@extends('layouts.dashboard')

@section('title', 'Governorates')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Governorates</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Manage Governorates</h3>
            <a href="{{ route('governorates.create') }}" class="btn btn-primary float-end">Create New Governorate</a>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Image</th>
                    <th>Brief</th>
                    <th>Visit Count</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($governorates as $governorate)
                    <tr>
                        <td>{{ $governorate->id }}</td>
                        <td>{{ $governorate->name }}</td>
                        <td>
                            <img src="{{ Storage::url($governorate->image) }}" alt="{{ $governorate->name }}" width="100">
                        </td>
                        <td>{{ Str::limit($governorate->brief, 50) }}</td>
                        <td>{{ $governorate->visit_count }}</td>
                        <td>
                            <a href="{{ route('governorates.edit', $governorate) }}" class="btn btn-sm btn-primary">Edit</a>
                            <form action="{{ route('governorates.destroy', $governorate) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this governorate?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $governorates->links() }}
        </div>
    </div>
@endsection
