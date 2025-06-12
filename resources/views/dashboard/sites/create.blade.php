@extends('layouts.dashboard')
@section('title', 'Create Article')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('articles.index') }}">Articles</a></li>
    <li class="breadcrumb-item active">Create</li>
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

    <form action="{{ route('articles.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Article Title</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Enter article title">
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" placeholder="Enter description">{{ old('description') }}</textarea>
            @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <select class="form-control @error('category') is-invalid @enderror" id="category" name="category">
                <option value="">Select Category</option>
                @foreach(Article::CATEGORIES as $category)
                    <option value="{{ $category }}" @selected(old('category') == $category)>{{ $category }}</option>
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
                    <option value="{{ $era->id }}" @selected(old('era_id') == $era->id)>{{ $era->name }}</option>
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
                    <option value="{{ $governorate->id }}" @selected(old('governorate_id') == $governorate->id)>{{ $governorate->name }}</option>
                @endforeach
            </select>
            @error('governorate_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="images" class="form-label">Images</label>
            <input type="file" class="form-control @error('images.*') is-invalid @enderror" id="images" name="images[]" multiple>
            @error('images.*')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-sm btn-outline-primary">Create Article</button>
    </form>
@endsection


{{--@php use App\Models\Site; @endphp--}}
{{--@extends('layouts.dashboard')--}}
{{--@section('title','Create Sites')--}}
{{--@section('breadcrumb')--}}
{{--    @parent--}}
{{--    <li class="breadcrumb-item active">Sites</li>--}}
{{--    <li class="breadcrumb-item active">Create</li>--}}
{{--@endsection--}}
{{--@section('content')--}}
{{--    @if ($errors->any())--}}
{{--        <div class="alert alert-danger">--}}
{{--            <ul>--}}
{{--                @foreach ($errors->all() as $error)--}}
{{--                    <li>{{ $error }}</li>--}}
{{--                @endforeach--}}
{{--            </ul>--}}
{{--        </div>--}}
{{--    @endif--}}

{{--    <form class="form-group-lg" action="{{route('sites.store')}}" method="post" enctype="multipart/form-data">--}}
{{--        @csrf--}}
{{--        <div class="form mb-3">--}}
{{--            <label for="">Article title</label>--}}
{{--            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"--}}
{{--                   value="{{old('name')}}" placeholder="Article title"/>--}}
{{--        </div>--}}
{{--        <div class="form mb-3">--}}
{{--            <label for="">Description</label>--}}
{{--            <textarea type="area" class="form-control @error('description') is-invalid @enderror" name="description"--}}
{{--                      placeholder="Text">{{old('description')}}</textarea>--}}
{{--        </div>--}}

{{--        <div class="form">--}}
{{--            <div class="form">--}}
{{--                --}}{{--                @dd(\App\Models\Site::CATEGORIES);--}}

{{--                <label for="">Category</label>--}}

{{--                <select multiple class="form-control @error('category') is-invalid @enderror mb-3" name="category">--}}
{{--                    --}}{{--                @foreach($sites as $site)--}}
{{--                    --}}{{--                    <option value="{{$site->category}}">{{$site->category}}</option>--}}
{{--                    @foreach(Site::CATEGORIES as $category)--}}
{{--                        <option value="{{ $category }}">{{$category}}</option>--}}
{{--                    @endforeach--}}

{{--                    --}}{{--                @endforeach--}}
{{--                </select>--}}
{{--            </div>--}}

{{--            --}}{{--            <label for="">Category</label>--}}
{{--            <div class="form">--}}
{{--                <label for="">Era</label>--}}
{{--                <select multiple class="form-control @error('era_id') is-invalid @enderror mb-3" name="era_id">--}}
{{--                    @foreach($eras as $era)--}}
{{--                        <option value="{{$era->id}}">{{$era->name}}</option>--}}
{{--                    @endforeach--}}
{{--                </select>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <div class="form-group">--}}
{{--            <label for="">Add Images</label>--}}
{{--            <input type="file" name="images" id="">--}}
{{--        </div>--}}
{{--        <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>--}}
{{--    </form>--}}
{{--@endsection--}}
