@extends('layouts.dashboard')

@section('title', 'Certified Researchers')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Certified Researchers</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Manage Researcher Certification Requests</h3>
            <div class="card-tools">
                <span class="badge badge-info">Total: {{ $certifications->total() }}</span>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- Status Filter -->
            <div class="mb-3">
                <div class="btn-group" role="group" aria-label="Filter by status">
                    <a href="{{ route('certified_researchers.index') }}" class="btn btn-outline-secondary {{ !request('status') ? 'active' : '' }}">
                        All
                    </a>
                    <a href="{{ route('certified_researchers.index', ['status' => 'pending']) }}" class="btn btn-outline-warning {{ request('status') == 'pending' ? 'active' : '' }}">
                        Pending
                    </a>
                    <a href="{{ route('certified_researchers.index', ['status' => 'accepted']) }}" class="btn btn-outline-success {{ request('status') == 'accepted' ? 'active' : '' }}">
                        Accepted
                    </a>
                    <a href="{{ route('certified_researchers.index', ['status' => 'rejected']) }}" class="btn btn-outline-danger {{ request('status') == 'rejected' ? 'active' : '' }}">
                        Rejected
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                    <tr>
                        <th width="5%">ID</th>
                        <th width="20%">User Details</th>
                        <th width="15%">Major</th>
                        <th width="10%">Status</th>
                        <th width="10%">File</th>
                        <th width="15%">Submitted At</th>
                        <th width="15%">Rejection Reason</th>
                        <th width="10%">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($certifications as $certification)
                        <tr>
                            <td>{{ $certification->id }}</td>
                            <td>
                                <div>
                                    <strong>{{ $certification->user->name ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">{{ $certification->user->email ?? 'N/A' }}</small><br>
                                    <span class="badge badge-{{ $certification->user->type == 'admin' ? 'danger' : ($certification->user->type == 'researcher' ? 'success' : 'secondary') }}">
                                        {{ ucfirst($certification->user->type ?? 'unknown') }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="font-weight-bold">{{ $certification->major }}</span>
                            </td>
                            <td>
                                @switch($certification->status)
                                    @case('pending')
                                        <span class="badge badge-warning">
                                            <i class="fas fa-clock"></i> Pending
                                        </span>
                                        @break
                                    @case('accepted')
                                        <span class="badge badge-success">
                                            <i class="fas fa-check"></i> Accepted
                                        </span>
                                        @break
                                    @case('rejected')
                                        <span class="badge badge-danger">
                                            <i class="fas fa-times"></i> Rejected
                                        </span>
                                        @break
                                    @default
                                        <span class="badge badge-secondary">Unknown</span>
                                @endswitch
                            </td>
                            <td>
                                @if($certification->file)
                                    <a href="{{ Storage::url($certification->file) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-file-pdf"></i> View File
                                    </a>
                                @else
                                    <span class="text-muted">No file</span>
                                @endif
                            </td>
                            <td>
                                <div>
                                    {{ $certification->created_at->format('M d, Y') }}<br>
                                    <small class="text-muted">{{ $certification->created_at->format('H:i:s') }}</small><br>
                                    <small class="text-muted">{{ $certification->created_at->diffForHumans() }}</small>
                                </div>
                            </td>
                            <td>
                                @if($certification->rejection_reason)
                                    <div class="text-danger">
                                        <small>{{ Str::limit($certification->rejection_reason, 50) }}</small>
                                        @if(strlen($certification->rejection_reason) > 50)
                                            <br><small class="text-muted">
                                                <a href="#" data-toggle="tooltip" data-placement="top" title="{{ $certification->rejection_reason }}">
                                                    View full reason
                                                </a>
                                            </small>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group-vertical btn-group-sm" role="group">
                                    <!-- View Button -->
                                    <a href="{{ route('certified_researchers.show', $certification) }}" class="btn btn-primary btn-sm mb-1">
                                        <i class="fas fa-eye"></i> View
                                    </a>

                                    @if($certification->status === 'pending')
                                        <!-- Approve Button -->
                                        <form action="{{ route('certified_researchers.approve', $certification) }}" method="POST" style="display:inline;" class="mb-1">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to approve this user as a researcher?')">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                        </form>

                                        <!-- Reject Button -->
                                        <button type="button" class="btn btn-warning btn-sm mb-1" data-toggle="modal" data-target="#rejectModal{{ $certification->id }}">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    @endif

                                    <!-- Delete Button -->
                                    <form action="{{ route('certified_researchers.destroy', $certification) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this request? This action cannot be undone.')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Reject Modal -->
                        @if($certification->status === 'pending')
                            <div class="modal fade" id="rejectModal{{ $certification->id }}" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel{{ $certification->id }}" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="rejectModalLabel{{ $certification->id }}">Reject Application</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form action="{{ route('certified_researchers.reject', $certification) }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="rejection_reason{{ $certification->id }}">Rejection Reason:</label>
                                                    <textarea class="form-control" id="rejection_reason{{ $certification->id }}" name="rejection_reason" rows="4" required placeholder="Please provide a reason for rejection..."></textarea>
                                                </div>
                                                <div class="alert alert-warning">
                                                    <strong>Warning:</strong> This will reject the application for <strong>{{ $certification->user->name ?? 'Unknown User' }}</strong>.
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Reject Application</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif

                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3"></i><br>
                                No certification requests found.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Showing {{ $certifications->firstItem() ?? 0 }} to {{ $certifications->lastItem() ?? 0 }} of {{ $certifications->total() }} results
                </div>
                <div>
                    {{ $certifications->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Initialize tooltips
                $('[data-toggle="tooltip"]').tooltip();

                // Auto-hide alerts after 5 seconds
                setTimeout(function() {
                    $('.alert').fadeOut('slow');
                }, 5000);
            });
        </script>
    @endpush

    @push('styles')
        <style>
            .btn-group-vertical .btn {
                margin-bottom: 2px;
            }
            .table td {
                vertical-align: middle;
            }
            .badge {
                font-size: 0.8em;
            }
        </style>
    @endpush
@endsection
