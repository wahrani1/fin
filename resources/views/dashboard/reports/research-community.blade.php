@extends('layouts.dashboard')

@section('title', 'Research Community Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('reports.dashboard') }}">Heritage Reports</a></li>
    <li class="breadcrumb-item active">Research Community</li>
@endsection

@section('content')
    <!-- Add Chart.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.3.0/chart.min.js"></script>

    <style>
        .community-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }

        .community-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }

        .user-type-card {
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 1rem;
        }

        .user-type-admin { background: linear-gradient(135deg, #2c5282, #3182ce); color: white; }
        .user-type-researcher { background: linear-gradient(135deg, #38a169, #48bb78); color: white; }
        .user-type-normal { background: linear-gradient(135deg, #ed8936, #fd7f28); color: white; }

        .contributor-item {
            background: #f7fafc;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.5rem;
            border-left: 4px solid #2c5282;
        }

        .stat-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .badge-articles { background: #e6fffa; color: #234e52; }
        .badge-comments { background: #fef5e7; color: #744210; }
        .badge-posts { background: #f0fff4; color: #22543d; }
    </style>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="community-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2">
                                <i class="fas fa-users me-3 text-primary"></i>
                                Research Community Analysis
                            </h2>
                            <p class="text-muted mb-0">Comprehensive analysis of user engagement and research community growth</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="btn-group">
                                <button class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-download me-2"></i>Export Report
                                </button>
                                <button class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-chart-bar me-2"></i>View Analytics
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Distribution -->
    <div class="row mb-4">
        <div class="col-lg-4">
            <div class="community-card">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2 text-primary"></i>
                        User Distribution
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 250px;">
                        <canvas id="userDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="row">
                @foreach($community_data['user_distribution'] as $userType)
                    <div class="col-md-4">
                        <div class="user-type-card user-type-{{ $userType->type }}">
                            <h3 class="mb-1">{{ $userType->count }}</h3>
                            <p class="mb-0">{{ ucfirst($userType->type) }} Users</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Certification Stats -->
            <div class="community-card mt-3">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-graduation-cap me-2 text-success"></i>
                        Certification Program Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <h4 class="text-warning">{{ $community_data['certification_stats']['pending'] }}</h4>
                            <small class="text-muted">Pending</small>
                        </div>
                        <div class="col-md-3">
                            <h4 class="text-success">{{ $community_data['certification_stats']['accepted'] }}</h4>
                            <small class="text-muted">Approved</small>
                        </div>
                        <div class="col-md-3">
                            <h4 class="text-danger">{{ $community_data['certification_stats']['rejected'] }}</h4>
                            <small class="text-muted">Rejected</small>
                        </div>
                        <div class="col-md-3">
                            <h4 class="text-info">
                                @php
                                    $total = $community_data['certification_stats']['accepted'] + $community_data['certification_stats']['rejected'];
                                    $approvalRate = $total > 0 ? round(($community_data['certification_stats']['accepted'] / $total) * 100, 1) : 0;
                                @endphp
                                {{ $approvalRate }}%
                            </h4>
                            <small class="text-muted">Approval Rate</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Contributors -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="community-card">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-trophy me-2 text-warning"></i>
                        Top Contributors
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($community_data['top_contributors']->take(10) as $index => $contributor)
                        <div class="contributor-item">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h6 class="mb-1">
                                        <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                        {{ $contributor->name }}
                                    </h6>
                                    <small class="text-muted">{{ ucfirst($contributor->type) }} • Joined {{ $contributor->created_at->format('M Y') }}</small>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <span class="stat-badge badge-articles">{{ $contributor->articles_count }} Articles</span>
                                    <span class="stat-badge badge-comments">{{ $contributor->article_comments_count }} Comments</span>
                                    @if($contributor->community_posts_count > 0)
                                        <span class="stat-badge badge-posts">{{ $contributor->community_posts_count }} Posts</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Researcher Productivity -->
        <div class="col-lg-4">
            <div class="community-card">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2 text-info"></i>
                        Researcher Productivity
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="researcherProductivityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Popular Research Majors -->
    @if(isset($community_data['certification_stats']['popular_majors']) && $community_data['certification_stats']['popular_majors']->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="community-card">
                    <div class="card-header bg-transparent border-0">
                        <h5 class="mb-0">
                            <i class="fas fa-book me-2 text-secondary"></i>
                            Popular Research Majors
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($community_data['certification_stats']['popular_majors'] as $major)
                                <div class="col-md-4 col-lg-2 mb-3">
                                    <div class="text-center p-3 bg-light rounded">
                                        <h5 class="text-primary">{{ $major->count }}</h5>
                                        <small class="text-muted">{{ $major->major }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Community Insights -->
    <div class="row">
        <div class="col-12">
            <div class="community-card">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb me-2 text-warning"></i>
                        Community Insights & Recommendations
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="border-start border-success border-4 ps-3 mb-3">
                                <h6 class="text-success">Community Growth</h6>
                                <p class="mb-0 small text-muted">
                                    The research community is showing steady growth with
                                    {{ $community_data['certification_stats']['accepted'] }} certified researchers
                                    actively contributing to the platform.
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="border-start border-info border-4 ps-3 mb-3">
                                <h6 class="text-info">Quality Control</h6>
                                <p class="mb-0 small text-muted">
                                    Approval rate of {{ $approvalRate }}% indicates strong quality standards
                                    while maintaining accessibility for qualified researchers.
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="border-start border-warning border-4 ps-3 mb-3">
                                <h6 class="text-warning">Engagement Opportunities</h6>
                                <p class="mb-0 small text-muted">
                                    Focus on encouraging more community participation and
                                    recognizing top contributors to maintain platform vitality.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // User Distribution Chart
            const userDistCtx = document.getElementById('userDistributionChart').getContext('2d');
            const userDistData = @json($community_data['user_distribution']);

            new Chart(userDistCtx, {
                type: 'doughnut',
                data: {
                    labels: userDistData.map(item => item.type.charAt(0).toUpperCase() + item.type.slice(1)),
                    datasets: [{
                        data: userDistData.map(item => item.count),
                        backgroundColor: ['#2c5282', '#38a169', '#ed8936'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });

            // Researcher Productivity Chart
            const prodCtx = document.getElementById('researcherProductivityChart').getContext('2d');
            const prodData = @json($community_data['researcher_productivity']);

            new Chart(prodCtx, {
                type: 'bar',
                data: {
                    labels: prodData.map(item => item.name.substring(0, 10) + (item.name.length > 10 ? '...' : '')),
                    datasets: [{
                        label: 'Articles',
                        data: prodData.map(item => item.articles_count),
                        backgroundColor: 'rgba(56, 161, 105, 0.8)',
                        borderColor: '#38a169',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        },
                        x: {
                            ticks: { maxRotation: 45 }
                        }
                    }
                }
            });
        });
    </script>
@endsection
