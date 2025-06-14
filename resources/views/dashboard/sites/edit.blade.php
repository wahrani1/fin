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

    <form action="{{ route('articles.update', $article->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Article Title</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $article->name) }}" placeholder="Enter article title">
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" placeholder="Enter description">{{ old('description', $article->description) }}</textarea>
            @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <select class="form-control @error('category') is-invalid @enderror" id="category" name="category">
                <option value="">Select Category</option>
                @foreach(Article::CATEGORIES as $category)
                    <option value="{{ $category }}" @selected(old('category', $article->category) == $category)>{{ $category }}</option>
                @endforeach
            </select>
            @error('category')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="era_id" class="form-label">Era</label>
            <select class="form-control @error('era_id') is-invalid @enderror" id="era_id" name="era_id">
                <option value="">Select Era</option>
                @foreach($eras as $era)
                    <option value="{{ $era->id }}" @selected(old('era_id', $article->era_id) == $era->id)>{{ $era->name }}</option>
                @endforeach
            </select>
            @error('era_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="governorate_id" class="form-label">Governorate (Optional)</label>
            <select class="form-control @error('governorate_id') is-invalid @enderror" id="governorate_id" name="governorate_id">
                <option value="">Select Governorate</option>
                @foreach($governorates as $governorate)
                    <option value="{{ $governorate->id }}" @selected(old('governorate_id', $article->governorate_id) == $governorate->id)>{{ $governorate->name }}</option>
                @endforeach
            </select>
            @error('governorate_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Current Images</label>
            <div class="d-flex flex-wrap">
                @foreach($article->images as $image)
                    <div class="m-2 position-relative">
                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="" height="100" class="img-thumbnail">
                        <form action="{{ route('articles.images.destroy', $image->id) }}" method="POST" class="position-absolute top-0 end-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">X</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mb-3">
            <label for="images" class="form-label">Add New Images</label>
            <input type="file" class="form-control @error('images.*') is-invalid @enderror" id="images" name="images[]" multiple>
            @error('images.*')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-sm btn-outline-primary">Update Article</button>
    </form>
@endsection


{{--@php use App\Models\Site; @endphp--}}
{{--@extends('layouts.dashboard')--}}
{{--@section('title','Edit Site')--}}
{{--@section('breadcrumb')--}}
{{--    @parent--}}
{{--    <li class="breadcrumb-item active">Sites</li>--}}
{{--    <li class="breadcrumb-item active">Edit</li>--}}
{{--@endsection--}}
{{--@section('content')--}}
{{--    <form class="form-group-lg" action="{{route('sites.update',[$site->id])}}" method="post"--}}
{{--          enctype="multipart/form-data">--}}
{{--        @csrf--}}
{{--        @method('put')--}}
{{--        <div class="form mb-3">--}}
{{--            <label for="">Article title</label>--}}
{{--            <input type="text" class="form-control" name="name" placeholder="Article title" value="{{$site->name}}"/>--}}
{{--        </div>--}}
{{--        <div class="form mb-3">--}}
{{--            <label for="">Description</label>--}}
{{--            <textarea type="area" class="form-control" name="description"--}}
{{--                      placeholder="Text">{{$site->description}}</textarea>--}}
{{--        </div>--}}

{{--        <div class="form">--}}
{{--            <div>--}}
{{--                <label for="">Category</label>--}}
{{--                <select multiple class="form-control mb-3" name="category" @selected("Church")>--}}
{{--                    --}}{{--                @foreach($sites as $site)--}}
{{--                    --}}{{--                    <option value="{{$site->category}}">{{$site->category}}</option>--}}
{{--                    @foreach(Site::CATEGORIES as $category)--}}
{{--                        <option value="{{ $category }} " @selected($site->category == $category)>{{$category}}</option>--}}
{{--                    @endforeach--}}
{{--                    --}}{{--                    <option value="{{'Pyramid'}}" @selected($site->category == 'Pyramid') >Pyramid</option>--}}
{{--                    --}}{{--                    <option value="{{'Church'}}" @selected($site->category == 'Church')>Church</option>--}}
{{--                    --}}{{--                    <option value="{{'Mosque'}}" @selected($site->category == 'Mosque')>Mosque</option>--}}
{{--                    --}}{{--                    <option value="{{'Temple'}}" @selected($site->category == 'Temple')>Temple</option>--}}
{{--                    --}}{{--                    <option value="{{'antiquity'}}" @selected($site->category == 'antiquity')>Antiquity</option>--}}
{{--                    --}}{{--                    <option value="{{'Cemeteries'}}" @selected($site->category == 'Cemeteries')>Cemeteries</option>--}}
{{--                    --}}{{--                    <option value="{{'Palaces'}}" @selected($site->category == 'Palaces')>Palaces</option>--}}
{{--                    --}}{{--                @endforeach--}}
{{--                </select>--}}
{{--                --}}{{--                {{$site->category}}--}}
{{--            </div>--}}

{{--            --}}{{--            <label for="">Category</label>--}}
{{--            <div>--}}
{{--                <label for="">Era</label>--}}
{{--                <select multiple class="form-control mb-3" name="era_id">--}}
{{--                    @foreach($eras as $era)--}}
{{--                        <option--}}
{{--                            value="{{$era->id}}" @selected($site['era']['name'] == $era->name )>{{$era->name}}</option>--}}
{{--                    @endforeach--}}
{{--                </select>--}}

{{--            </div>--}}
{{--        </div>--}}
{{--        <div class="form-group">--}}
{{--            <label for="">Add Images</label>--}}
{{--            <input type="file" name="images" class="form-control">--}}
{{--        </div>--}}
{{--        <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>--}}
{{--    </form>--}}

{{--@endsection--}}
