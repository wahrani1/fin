@extends('layouts.dashboard')

@section('title', 'View Certification Request')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('certified_researchers.index') }}">Certified Researchers</a></li>
    <li class="breadcrumb-item active" aria-current="page">View</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Certification Request Details</h3>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <dl class="row">
                <dt class="col-sm-3">ID</dt>
                <dd class="col-sm-9">{{ $certifiedResearcher->id }}</dd>
                <dt class="col-sm-3">User</dt>
                <dd class="col-sm-9">{{ $certifiedResearcher->user->name }} ({{ $certifiedResearcher->user->email }})</dd>
                <dt class="col-sm-3">Major</dt>
                <dd class="col-sm-9">{{ $certifiedResearcher->major }}</dd>
                <dt class="col-sm-3">File</dt>
                <dd class="col-sm-9">
                    <a href="{{ Storage::url($certifiedResearcher->file) }}" target="_blank" class="btn btn-sm btn-info">Download PDF</a>
                </dd>
                <dt class="col-sm-3">Submitted At</dt>
                <dd class="col-sm-9">{{ $certifiedResearcher->created_at->format('Y-m-d H:i:s') }}</dd>
            </dl>
            <div class="mt-3">
                <form action="{{ route('certified_researchers.approve', $certifiedResearcher) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to approve this user as a researcher?')">Approve as Researcher</button>
                </form>
                <form action="{{ route('certified_researchers.destroy', $certifiedResearcher) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this request?')">Delete Request</button>
                </form>
                <a href="{{ route('certified_researchers.index') }}" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    </div>
@endsection
