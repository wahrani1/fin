@extends('layouts.dashboard')

@section('title', 'Heritage Management Reports')

@section('breadcrumb')
    <li class="breadcrumb-item active">Heritage Reports</li>
@endsection

@section('content')
    <style>
        :root {
            --heritage-primary: #2c5282;
            --heritage-gold: #b7791f;
            --heritage-green: #38a169;
            --heritage-orange: #ed8936;
        }

        .heritage-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }

        .heritage-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }

        .stat-card {
            background: linear-gradient(135deg, var(--heritage-primary) 0%, #3182ce 100%);
            color: white;
            border-radius: 12px;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            transition: transform 0.5s ease;
        }

        .stat-card:hover::before {
            transform: scale(1.2);
        }

        .stat-card.gold { background: linear-gradient(135deg, var(--heritage-gold) 0%, #d69e2e 100%); }
        .stat-card.green { background: linear-gradient(135deg, var(--heritage-green) 0%, #48bb78 100%); }
        .stat-card.orange { background: linear-gradient(135deg, var(--heritage-orange) 0%, #fd7f28 100%); }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 2;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
            position: relative;
            z-index: 2;
        }

        .stat-icon {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 2rem;
            opacity: 0.3;
            z-index: 1;
        }

        .chart-container {
            position: relative;
            height: 350px;
            padding: 1rem;
        }

        .dashboard-header {
            background: linear-gradient(135deg, var(--heritage-primary) 0%, #1a365d 100%);
            color: white;
            padding: 2rem;
            margin: -20px -15px 2rem -15px;
            border-radius: 0 0 20px 20px;
        }

        .progress-custom {
            height: 8px;
            border-radius: 10px;
            background-color: #e2e8f0;
        }

        .progress-bar-custom {
            border-radius: 10px;
            transition: width 0.6s ease;
        }

        .metric-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .heritage-badge {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            background: rgba(46, 82, 130, 0.1);
            color: var(--heritage-primary);
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            margin: 0.2rem;
        }

        .top-performer {
            background: linear-gradient(45deg, #f7fafc, #edf2f7);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid var(--heritage-gold);
        }

        @media (max-width: 768px) {
            .stat-number { font-size: 2rem; }
            .chart-container { height: 250px; }
            .dashboard-header { padding: 1.5rem; margin: -15px -10px 1.5rem -10px; }
        }
    </style>

    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-2">
                        <i class="fas fa-landmark me-3"></i>Egyptian Heritage Management Dashboard
                    </h1>
                    <p class="mb-0 opacity-75">Comprehensive analytics for Egypt's cultural heritage preservation platform</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="d-flex flex-column align-items-md-end">
                    <span class="badge bg-light text-dark px-3 py-2 mb-2">
                        <i class="fas fa-calendar me-2"></i>{{ date('F j, Y') }}
                    </span>
                        <span class="badge bg-success px-3 py-2">
                        <i class="fas fa-check-circle me-2"></i>System Healthy
                    </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Executive Summary Cards -->
    <div class="metric-grid">
        <div class="stat-card">
            <i class="fas fa-monument stat-icon"></i>
            <div class="stat-number">{{ $dashboard_data['executive_summary']['total_heritage_sites'] }}</div>
            <div class="stat-label">Heritage Sites Documented</div>
        </div>

        <div class="stat-card gold">
            <i class="fas fa-map-marked-alt stat-icon"></i>
            <div class="stat-number">{{ $dashboard_data['executive_summary']['governorates_documented'] }}/{{ $dashboard_data['executive_summary']['governorates_documented'] + ($dashboard_data['executive_summary']['total_heritage_sites'] > 0 ? 27 - $dashboard_data['executive_summary']['governorates_documented'] : 27) }}</div>
            <div class="stat-label">Governorates Covered ({{ $dashboard_data['executive_summary']['coverage_percentage'] }}%)</div>
        </div>

        <div class="stat-card green">
            <i class="fas fa-users stat-icon"></i>
            <div class="stat-number">{{ $dashboard_data['executive_summary']['active_researchers'] }}</div>
            <div class="stat-label">Certified Researchers</div>
        </div>

        <div class="stat-card orange">
            <i class="fas fa-comments stat-icon"></i>
            <div class="stat-number">{{ number_format($dashboard_data['executive_summary']['total_user_interactions']) }}</div>
            <div class="stat-label">User Interactions</div>
        </div>
    </div>

    <!-- Platform Health Score -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="heritage-card">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="mb-0"><i class="fas fa-heartbeat me-2 text-success"></i>Platform Health Score</h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <h2 class="text-success mb-0">{{ $dashboard_data['executive_summary']['platform_health_score'] }}/100</h2>
                            <small class="text-muted">Overall System Health</small>
                        </div>
                        <div class="col-md-9">
                            <div class="progress progress-custom mb-2">
                                <div class="progress-bar progress-bar-custom bg-success"
                                     style="width: {{ $dashboard_data['executive_summary']['platform_health_score'] }}%"></div>
                            </div>
                            <small class="text-muted">Based on content quality, user engagement, and system performance</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Heritage Categories Chart -->
        <div class="col-lg-6 mb-4">
            <div class="heritage-card">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Heritage Categories Distribution</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="categoriesChart"></canvas>
                    </div>
                    <div class="mt-3">
                        @foreach($dashboard_data['heritage_categories']->take(3) as $category)
                            <span class="heritage-badge">{{ $category['category'] }}: {{ $category['count'] }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Geographic Coverage Chart -->
        <div class="col-lg-6 mb-4">
            <div class="heritage-card">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="fas fa-map me-2"></i>Top Governorates by Heritage Sites</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="governoratesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Quality Metrics -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <div class="heritage-card">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="fas fa-star me-2"></i>Content Quality Metrics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <h4 class="text-warning">{{ number_format($dashboard_data['content_quality']['avg_rating'], 1) }}/5.0</h4>
                                <small class="text-muted">Average Rating</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <h4 class="text-info">{{ $dashboard_data['content_quality']['image_coverage'] }}%</h4>
                                <small class="text-muted">Articles with Images</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <h4 class="text-success">{{ $dashboard_data['content_quality']['moderation_efficiency'] }}%</h4>
                                <small class="text-muted">Moderation Efficiency</small>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Coverage Breakdown:</h6>
                            <div class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <span>Image Coverage</span>
                                    <span>{{ $dashboard_data['content_quality']['image_coverage'] }}%</span>
                                </div>
                                <div class="progress progress-custom">
                                    <div class="progress-bar bg-info" style="width: {{ $dashboard_data['content_quality']['image_coverage'] }}%"></div>
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <span>Rating Coverage</span>
                                    <span>{{ $dashboard_data['content_quality']['rating_coverage'] }}%</span>
                                </div>
                                <div class="progress progress-custom">
                                    <div class="progress-bar bg-warning" style="width: {{ $dashboard_data['content_quality']['rating_coverage'] }}%"></div>
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <span>Comment Coverage</span>
                                    <span>{{ $dashboard_data['content_quality']['comment_coverage'] }}%</span>
                                </div>
                                <div class="progress progress-custom">
                                    <div class="progress-bar bg-success" style="width: {{ $dashboard_data['content_quality']['comment_coverage'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Research Community Stats -->
        <div class="col-lg-4 mb-4">
            <div class="heritage-card">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Research Community</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h3 class="text-primary">{{ $dashboard_data['research_community']['approved_researchers'] }}</h3>
                        <small class="text-muted">Active Researchers</small>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Approval Rate</span>
                            <span class="text-success">{{ $dashboard_data['research_community']['approval_rate'] }}%</span>
                        </div>
                        <div class="progress progress-custom">
                            <div class="progress-bar bg-success" style="width: {{ $dashboard_data['research_community']['approval_rate'] }}%"></div>
                        </div>
                    </div>

                    <div class="text-center">
                        <span class="badge bg-warning">{{ $dashboard_data['research_community']['pending_applications'] }} Pending</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performers -->
    @if(isset($dashboard_data['top_performers']['most_rated_article']))
        <div class="row mb-4">
            <div class="col-12">
                <div class="heritage-card">
                    <div class="card-header bg-transparent border-0">
                        <h5 class="mb-0"><i class="fas fa-trophy me-2 text-warning"></i>Top Performing Content</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="top-performer">
                                    <h6 class="text-primary mb-2">Most Rated Article</h6>
                                    <h5>{{ $dashboard_data['top_performers']['most_rated_article']->name }}</h5>
                                    <p class="mb-1"><strong>{{ $dashboard_data['top_performers']['most_rated_article']->ratings_count }}</strong> ratings</p>
                                    @if($dashboard_data['top_performers']['most_rated_article']->governorate)
                                        <small class="text-muted">{{ $dashboard_data['top_performers']['most_rated_article']->governorate->name }}</small>
                                    @endif
                                </div>
                            </div>

                            @if(isset($dashboard_data['top_performers']['most_visited_governorate']))
                                <div class="col-lg-6">
                                    <div class="top-performer">
                                        <h6 class="text-success mb-2">Most Visited Governorate</h6>
                                        <h5>{{ $dashboard_data['top_performers']['most_visited_governorate']->name }}</h5>
                                        <p class="mb-1"><strong>{{ number_format($dashboard_data['top_performers']['most_visited_governorate']->visit_count) }}</strong> visits</p>
                                        <small class="text-muted">Tourism hotspot</small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-12">
            <div class="heritage-card">
                <div class="card-body text-center">
                    <h5 class="mb-3">Detailed Reports</h5>
                    <div class="btn-group flex-wrap" role="group">
                        <a href="#" class="btn btn-outline-primary">
                            <i class="fas fa-map-marked-alt me-2"></i>Geographic Analysis
                        </a>
                        <a href="#" class="btn btn-outline-info">
                            <i class="fas fa-history me-2"></i>Historical Timeline
                        </a>
                        <a href="#" class="btn btn-outline-success">
                            <i class="fas fa-users me-2"></i>Community Report
                        </a>
                        <a href="#" class="btn btn-outline-warning">
                            <i class="fas fa-download me-2"></i>Export PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Heritage Categories Pie Chart
            const categoriesCtx = document.getElementById('categoriesChart').getContext('2d');
            const categoriesData = @json($dashboard_data['heritage_categories']);

            new Chart(categoriesCtx, {
                type: 'doughnut',
                data: {
                    labels: categoriesData.map(item => item.category),
                    datasets: [{
                        data: categoriesData.map(item => item.count),
                        backgroundColor: [
                            '#2c5282', '#b7791f', '#38a169', '#ed8936',
                            '#e53e3e', '#805ad5', '#3182ce'
                        ],
                        borderWidth: 0,
                        hoverBorderWidth: 2,
                        hoverBorderColor: '#fff'
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

            // Governorates Bar Chart
            const governoratesCtx = document.getElementById('governoratesChart').getContext('2d');
            const governoratesData = @json($dashboard_data['geographic_coverage']->take(8));

            new Chart(governoratesCtx, {
                type: 'bar',
                data: {
                    labels: governoratesData.map(item => item.name),
                    datasets: [{
                        label: 'Heritage Sites',
                        data: governoratesData.map(item => item.articles_count),
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
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        },
                        x: {
                            ticks: {
                                maxRotation: 45
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
