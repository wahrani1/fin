@extends('layouts.dashboard')
@section('title', 'Article Ratings')
@section('breadcrumb')
    <li class="breadcrumb-item active">Ratings</li>
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5>{{ $pendingCount ?? 0 }}</h5>
                    <p class="mb-0">Pending Ratings</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>{{ $approvedCount ?? 0 }}</h5>
                    <p class="mb-0">Approved Ratings</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5>{{ $ratings->total() }}</h5>
                    <p class="mb-0">Total Ratings</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5>{{ $averageRating ?? '0.0' }}</h5>
                    <p class="mb-0">Average Rating</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Rating</label>
                    <select name="rating" class="form-select">
                        <option value="">All Ratings</option>
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                                {{ $i }} Star{{ $i > 1 ? 's' : '' }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Search user or article..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </form>
            <div class="mt-2">
                <a href="{{ route('ratings.index') }}" class="btn btn-outline-secondary btn-sm">Clear Filters</a>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Article Ratings</h5>
            <div class="btn-group">
                <button type="button" class="btn btn-success btn-sm" onclick="bulkAction('approve')" id="bulk-approve-btn" disabled>
                    <i class="fas fa-check"></i> Bulk Approve
                </button>
                <button type="button" class="btn btn-danger btn-sm" onclick="bulkAction('delete')" id="bulk-delete-btn" disabled>
                    <i class="fas fa-trash"></i> Bulk Delete
                </button>
            </div>
        </div>

        <div class="card-body">
            <form id="bulk-form" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th width="50">
                                <input type="checkbox" id="select-all" class="form-check-input">
                            </th>
                            <th>#</th>
                            <th>Article</th>
                            <th>User</th>
                            <th>Rating</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($ratings as $rating)
                            <tr>
                                <td>
                                    <input type="checkbox" name="rating_ids[]" value="{{ $rating->id }}" class="form-check-input rating-checkbox">
                                </td>
                                <td>{{ $rating->id }}</td>
                                <td>
                                    <div class="fw-bold">{{ Str::limit($rating->article->name, 40) }}</div>
                                    <small class="text-muted">ID: {{ $rating->article->id }}</small>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $rating->user->name }}</div>
                                    <small class="text-muted">{{ $rating->user->email ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rating-stars me-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $rating->rating ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                        </div>
                                        <span class="fw-bold">{{ $rating->rating }}/5</span>
                                    </div>
                                </td>
                                <td>
                                    @if($rating->is_approved)
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ $rating->created_at->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ $rating->created_at->format('H:i') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @if(!$rating->is_approved)
                                            <form action="{{ route('ratings.approve', $rating->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success"
                                                        onclick="return confirm('Approve this rating?')"
                                                        title="Approve Rating">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('ratings.destroy', $rating->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Are you sure you want to delete this rating?')"
                                                    title="Delete Rating">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-star fa-3x mb-3"></i>
                                        <p>No ratings found</p>
                                        @if(request()->hasAny(['status', 'rating', 'search']))
                                            <a href="{{ route('ratings.index') }}" class="btn btn-outline-primary">Clear Filters</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
        </div>

        @if($ratings->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Showing {{ $ratings->firstItem() }} to {{ $ratings->lastItem() }} of {{ $ratings->total() }} ratings
                    </div>
                    <div>
                        {{ $ratings->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        // Select All Functionality
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.rating-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkButtons();
        });

        // Individual checkbox change
        document.querySelectorAll('.rating-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkButtons);
        });

        // Update bulk button states
        function updateBulkButtons() {
            const checkedBoxes = document.querySelectorAll('.rating-checkbox:checked');
            const bulkApproveBtn = document.getElementById('bulk-approve-btn');
            const bulkDeleteBtn = document.getElementById('bulk-delete-btn');

            if (checkedBoxes.length > 0) {
                bulkApproveBtn.disabled = false;
                bulkDeleteBtn.disabled = false;
            } else {
                bulkApproveBtn.disabled = true;
                bulkDeleteBtn.disabled = true;
            }
        }

        // Bulk Actions
        function bulkAction(action) {
            const form = document.getElementById('bulk-form');
            const checkedBoxes = document.querySelectorAll('.rating-checkbox:checked');

            if (checkedBoxes.length === 0) {
                alert('Please select at least one rating');
                return;
            }

            const actionText = action === 'approve' ? 'approve' : 'delete';
            if (!confirm(`Are you sure you want to ${actionText} ${checkedBoxes.length} rating(s)?`)) {
                return;
            }

            if (action === 'approve') {
                form.action = '{{ route("ratings.bulk-approve") }}';
            } else {
                form.action = '{{ route("ratings.bulk-delete") }}';
            }

            form.submit();
        }

        // Auto-hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                if (alert.classList.contains('show')) {
                    alert.classList.remove('show');
                    alert.classList.add('fade');
                }
            });
        }, 5000);
    </script>

    <style>
        .rating-stars i {
            font-size: 0.9rem;
        }
        .card {
            box-shadow: 0 0 1rem rgba(0, 0, 0, 0.15);
            border: none;
        }
        .table th {
            font-weight: 600;
            border-top: none;
        }
    </style>
@endsection
