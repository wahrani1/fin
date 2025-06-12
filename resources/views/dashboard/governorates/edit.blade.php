@extends('layouts.dashboard')

@section('title', 'Edit Governorate')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('governorates.index') }}">Governorates</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Edit Governorate</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('governorates.update', $governorate) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                           value="{{ old('name', $governorate->name) }}" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Image</label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image"
                           accept="image/*">
                    @if ($governorate->image)
                        <img src="{{ Storage::url($governorate->image) }}" alt="{{ $governorate->name }}" width="100"
                             class="mt-2">
                    @endif
                    @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="brief" class="form-label">Brief</label>
                    <input type="text" class="form-control @error('brief') is-invalid @enderror" id="brief" name="brief"
                           value="{{ old('brief', $governorate->brief) }}" required>
                    @error('brief')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description"
                              name="description" rows="5"
                              required>{{ old('description', $governorate->description) }}</textarea>
                    @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="visit_count" class="form-label">Visit Count</label>
                    <input type="number" class="form-control @error('visit_count') is-invalid @enderror"
                           id="visit_count" name="visit_count"
                           value="{{ old('visit_count', $governorate->visit_count) }}" min="0" required>
                    @error('visit_count')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Update Governorate</button>
                <a href="{{ route('governorates.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
