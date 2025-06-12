@extends('layouts.dashboard')

@section('title', 'Eras')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Eras</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Manage Eras</h3>
            <a href="{{ route('eras.create') }}" class="btn btn-primary float-end">Create New Era</a>
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
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($eras as $era)
                    <tr>
                        <td>{{ $era->id }}</td>
                        <td>{{ $era->name }}</td>
                        <td>{{ $era->created_at->format('Y-m-d H:i:s') }}</td>
                        <td>
                            <a href="{{ route('eras.edit', $era) }}" class="btn btn-sm btn-primary">Edit</a>
                            <form action="{{ route('eras.destroy', $era) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this era?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $eras->links() }}
        </div>
    </div>
@endsection
