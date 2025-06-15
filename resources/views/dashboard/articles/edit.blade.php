@extends('layouts.dashboard')
@section('title', 'Edit Article')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('articles.index') }}">Articles</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection
@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- MOVE IMAGE DELETE FORMS OUTSIDE THE MAIN FORM -->
    <div class="mb-3">
        <label class="form-label">Current Images</label>
        <div class="d-flex flex-wrap">
            @foreach($article->images as $image)
                <div class="m-2 position-relative">
                    <img src="{{ asset('storage/' . $image->image_path) }}" alt="" height="100" class="img-thumbnail" style="width: 300px; height: 300px ">
                    <!-- SEPARATE FORM FOR IMAGE DELETION -->
                    <form action="{{ route('articles.images.destroy', $image->id) }}" method="POST"
                          class="position-absolute top-0 end-0"
                          onsubmit="return confirm('Are you sure you want to delete this image?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" title="Delete Image">×</button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    <!-- MAIN ARTICLE UPDATE FORM -->
    <form action="{{ route('articles.update', $article->id) }}" method="POST" enctype="multipart/form-data"
          id="articleUpdateForm">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Article Title</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                   value="{{ old('name', $article->name) }}" placeholder="Enter article title" required>
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description"
                      name="description" rows="5" placeholder="Enter description"
                      required>{{ old('description', $article->description) }}</textarea>
            @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <select class="form-control @error('category') is-invalid @enderror" id="category" name="category" required>
                <option value="">Select Category</option>
                @foreach(\App\Models\Article::CATEGORIES as $category)
                    <option
                        value="{{ $category }}" @selected(old('category', $article->category) == $category)>{{ $category }}</option>
                @endforeach
            </select>
            @error('category')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="era_id" class="form-label">Era</label>
            <select class="form-control @error('era_id') is-invalid @enderror" id="era_id" name="era_id" required>
                <option value="">Select Era</option>
                @foreach($eras as $era)
                    <option
                        value="{{ $era->id }}" @selected(old('era_id', $article->era_id) == $era->id)>{{ $era->name }}</option>
                @endforeach
            </select>
            @error('era_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="governorate_id" class="form-label">Governorate (Optional)</label>
            <select class="form-control @error('governorate_id') is-invalid @enderror" id="governorate_id"
                    name="governorate_id">
                <option value="">Select Governorate</option>
                @foreach($governorates as $governorate)
                    <option
                        value="{{ $governorate->id }}" @selected(old('governorate_id', $article->governorate_id) == $governorate->id)>{{ $governorate->name }}</option>
                @endforeach
            </select>
            @error('governorate_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="images" class="form-label">Add New Images</label>
            <input type="file" class="form-control @error('images.*') is-invalid @enderror" id="images" name="images[]"
                   multiple accept="image/*">
            <small class="form-text text-muted">You can select multiple images. Supported formats: JPEG, PNG, JPG, GIF
                (Max: 2MB each)</small>
            @error('images.*')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Update Article</button>
            <a href="{{ route('articles.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>

    <!-- Optional: Add some JavaScript for better UX -->
    <script>
        document.getElementById('articleUpdateForm').addEventListener('submit', function (e) {
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.textContent = 'Updating...';
        });
    </script>
@endsection
