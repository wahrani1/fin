@extends('layouts.dashboard')

@section('title', 'Create Era')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('eras.index') }}">Eras</a></li>
    <li class="breadcrumb-item active" aria-current="page">Create</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Create New Era</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('eras.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Era Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Create Era</button>
                <a href="{{ route('eras.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
