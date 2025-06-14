@extends('layouts.dashboard')

@section('title', 'Platform Growth Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('reports.dashboard') }}">Heritage Reports</a></li>
    <li class="breadcrumb-item active">Platform Growth</li>
@endsection

@section('content')
    <!-- Add Chart.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.3.0/chart.min.js"></script>

    <style>
        .growth-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }

        .growth-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
    </style>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="growth-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2">
                                <i class="fas fa-chart-line me-3 text-primary"></i>
                                Platform Growth Analysis
                            </h2>
                            <p class="text-muted mb-0">Track user registration trends, content creation velocity, and engagement patterns over time</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="btn-group">
                                <button class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-download me-2"></i>Export Report
                                </button>
                                <button class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-chart-bar me-2"></i>View Trends
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Growth Charts -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="growth-card">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-user-plus me-2 text-success"></i>
                        User Registration Trends
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="userRegistrationChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="growth-card">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-plus me-2 text-info"></i>
                        Content Creation Trends
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="contentCreationChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Engagement Trends -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="growth-card">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-comments me-2 text-warning"></i>
                        User Engagement Trends
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 350px;">
                        <canvas id="engagementTrendsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Platform Health Metrics -->
    <div class="row">
        <div class="col-12">
            <div class="growth-card">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-heartbeat me-2 text-danger"></i>
                        Platform Health Metrics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <h4 class="text-primary">{{ $growth_data['platform_health']['active_users_ratio'] }}%</h4>
                            <p class="text-muted mb-0">Active Users Ratio</p>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <h4 class="text-success">{{ $growth_data['platform_health']['content_quality_score'] }}</h4>
                            <p class="text-muted mb-0">Content Quality Score</p>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <h4 class="text-warning">{{ $growth_data['platform_health']['moderation_efficiency'] }}%</h4>
                            <p class="text-muted mb-0">Moderation Efficiency</p>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <h4 class="text-info">{{ $growth_data['engagement_trends']['comments_trend']->sum('count') + $growth_data['engagement_trends']['ratings_trend']->sum('count') }}</h4>
                            <p class="text-muted mb-0">Total Interactions</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // User Registration Chart
            const userRegCtx = document.getElementById('userRegistrationChart').getContext('2d');
            const userRegData = @json($growth_data['monthly_user_registration']);

            new Chart(userRegCtx, {
                type: 'line',
                data: {
                    labels: userRegData.map(item => item.month),
                    datasets: [{
                        label: 'New Users',
                        data: userRegData.map(item => item.count),
                        borderColor: '#38a169',
                        backgroundColor: 'rgba(56, 161, 105, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
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
                        }
                    }
                }
            });

            // Content Creation Chart
            const contentCtx = document.getElementById('contentCreationChart').getContext('2d');
            const contentData = @json($growth_data['monthly_content_creation']);

            new Chart(contentCtx, {
                type: 'bar',
                data: {
                    labels: contentData.map(item => item.month),
                    datasets: [{
                        label: 'New Articles',
                        data: contentData.map(item => item.count),
                        backgroundColor: 'rgba(44, 82, 130, 0.8)',
                        borderColor: '#2c5282',
                        borderWidth: 1,
                        borderRadius: 4
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
                        }
                    }
                }
            });

            // Engagement Trends Chart
            const engagementCtx = document.getElementById('engagementTrendsChart').getContext('2d');
            const commentsData = @json($growth_data['engagement_trends']['comments_trend']);
            const ratingsData = @json($growth_data['engagement_trends']['ratings_trend']);

            new Chart(engagementCtx, {
                type: 'line',
                data: {
                    labels: commentsData.map(item => item.month),
                    datasets: [{
                        label: 'Comments',
                        data: commentsData.map(item => item.count),
                        borderColor: '#ed8936',
                        backgroundColor: 'rgba(237, 137, 54, 0.1)',
                        borderWidth: 2,
                        tension: 0.4
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
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        });
    </script>
@endsection
}, {
label: 'Ratings',
data: ratingsData.map(item => item.count),
borderColor: '#2c5282',
backgroundColor: 'rgba(44, 82, 130, 0.1)',
borderWidth: 2,
tension: 0.4
