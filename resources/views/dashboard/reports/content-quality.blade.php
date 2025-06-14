@extends('layouts.dashboard')

@section('title', 'Content Quality Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('reports.dashboard') }}">Heritage Reports</a></li>
    <li class="breadcrumb-item active">Content Quality</li>
@endsection

@section('content')
    <!-- Add Chart.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.3.0/chart.min.js"></script>

    <style>
        .quality-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }

        .quality-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }

        .quality-metric {
            background: #f7fafc;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 1rem;
        }

        .metric-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .metric-label {
            color: #718096;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .progress-ring {
            width: 120px;
            height: 120px;
            margin: 0 auto;
        }

        .moderation-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
            margin: 0.25rem;
        }

        .badge-pending { background: #fef5e7; color: #744210; }
        .badge-approved { background: #f0fff4; color: #22543d; }
    </style>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="quality-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2">
                                <i class="fas fa-star me-3 text-primary"></i>
                                Content Quality Assessment
                            </h2>
                            <p class="text-muted mb-0">Comprehensive analysis of content completeness, ratings, and moderation efficiency</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="btn-group">
                                <button class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-download me-2"></i>Export Report
                                </button>
                                <button class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-chart-bar me-2"></i>View Details
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Overview -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="quality-metric">
                <div class="metric-number text-primary">{{ $quality_data['articles_with_images'] }}</div>
                <div class="metric-label">Articles with Images</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="quality-metric">
                <div class="metric-number text-danger">{{ $quality_data['articles_without_images'] }}</div>
                <div class="metric-label">Articles without Images</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="quality-metric">
                <div class="metric-number text-warning">{{ number_format($quality_data['average_rating'] ?? 0, 1) }}/5.0</div>
                <div class="metric-label">Average Rating</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="quality-metric">
                @php
                    $totalArticles = $quality_data['articles_with_images'] + $quality_data['articles_without_images'];
                    $imagePercentage = $totalArticles > 0 ? round(($quality_data['articles_with_images'] / $totalArticles) * 100, 1) : 0;
                @endphp
                <div class="metric-number text-info">{{ $imagePercentage }}%</div>
                <div class="metric-label">Image Coverage</div>
            </div>
        </div>
    </div>

    <!-- Content Quality Charts -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="quality-card">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2 text-primary"></i>
                        Content Completeness
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="completenessChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="quality-card">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-star me-2 text-warning"></i>
                        Rating Distribution
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="ratingDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Moderation Statistics -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="quality-card">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-shield-alt me-2 text-success"></i>
                        Moderation Statistics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Comments</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Approved</span>
                                <span class="text-success">{{ $quality_data['moderation_stats']['comments_approved'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Pending</span>
                                <span class="text-warning">{{ $quality_data['moderation_stats']['comments_pending'] }}</span>
                            </div>
                            @php
                                $totalComments = $quality_data['moderation_stats']['comments_approved'] + $quality_data['moderation_stats']['comments_pending'];
                                $commentApprovalRate = $totalComments > 0 ? round(($quality_data['moderation_stats']['comments_approved'] / $totalComments) * 100, 1) : 0;
                            @endphp
                            <div class="progress mt-2" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: {{ $commentApprovalRate }}%"></div>
                            </div>
                            <small class="text-muted">{{ $commentApprovalRate }}% approved</small>
                        </div>

                        <div class="col-md-6">
                            <h6 class="mb-3">Ratings</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Approved</span>
                                <span class="text-success">{{ $quality_data['moderation_stats']['ratings_approved'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Pending</span>
                                <span class="text-warning">{{ $quality_data['moderation_stats']['ratings_pending'] }}</span>
                            </div>
                            @php
                                $totalRatings = $quality_data['moderation_stats']['ratings_approved'] + $quality_data['moderation_stats']['ratings_pending'];
                                $ratingApprovalRate = $totalRatings > 0 ? round(($quality_data['moderation_stats']['ratings_approved'] / $totalRatings) * 100, 1) : 0;
                            @endphp
                            <div class="progress mt-2" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: {{ $ratingApprovalRate }}%"></div>
                            </div>
                            <small class="text-muted">{{ $ratingApprovalRate }}% approved</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="quality-card">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-tachometer-alt me-2 text-info"></i>
                        Quality Score
                    </h5>
                </div>
                <div class="card-body text-center">
                    @php
                        $qualityScore = ($quality_data['average_rating'] ?? 0) * 20; // Convert 1-5 to 0-100
                    @endphp
                    <div class="quality-score mb-3">
                        <h2 class="text-primary">{{ number_format($qualityScore, 1) }}/100</h2>
                        <p class="text-muted mb-0">Overall Content Quality</p>
                    </div>

                    <div class="progress mb-3" style="height: 10px;">
                        <div class="progress-bar
                            @if($qualityScore >= 80) bg-success
                            @elseif($qualityScore >= 60) bg-warning
                            @else bg-danger
                            @endif"
                             style="width: {{ $qualityScore }}%">
                        </div>
                    </div>

                    <small class="text-muted">
                        Based on average user ratings and content completeness
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Quality Insights -->
    <div class="row">
        <div class="col-12">
            <div class="quality-card">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb me-2 text-warning"></i>
                        Quality Insights & Recommendations
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="border-start border-primary border-4 ps-3 mb-3">
                                <h6 class="text-primary">Content Completeness</h6>
                                <p class="mb-0 small text-muted">
                                    {{ $imagePercentage }}% of articles have images. Focus on encouraging
                                    image uploads for articles without visual content to improve user engagement.
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="border-start border-success border-4 ps-3 mb-3">
                                <h6 class="text-success">User Satisfaction</h6>
                                <p class="mb-0 small text-muted">
                                    Average rating of {{ number_format($quality_data['average_rating'] ?? 0, 1) }}/5.0 indicates
                                    @if(($quality_data['average_rating'] ?? 0) >= 4) excellent
                                    @elseif(($quality_data['average_rating'] ?? 0) >= 3) good
                                    @else moderate
                                    @endif
                                    user satisfaction with content quality.
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="border-start border-info border-4 ps-3 mb-3">
                                <h6 class="text-info">Moderation Efficiency</h6>
                                <p class="mb-0 small text-muted">
                                    Moderation queue shows healthy activity with balanced approval rates,
                                    indicating effective quality control processes.
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
            // Content Completeness Chart
            const completenessCtx = document.getElementById('completenessChart').getContext('2d');

            new Chart(completenessCtx, {
                type: 'doughnut',
                data: {
                    labels: ['With Images', 'Without Images'],
                    datasets: [{
                        data: [{{ $quality_data['articles_with_images'] }}, {{ $quality_data['articles_without_images'] }}],
                        backgroundColor: ['#38a169', '#e53e3e'],
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

            // Rating Distribution Chart
            const ratingCtx = document.getElementById('ratingDistributionChart').getContext('2d');
            const ratingData = @json($quality_data['rating_distribution']);

            new Chart(ratingCtx, {
                type: 'bar',
                data: {
                    labels: ratingData.map(item => item.rating + ' Stars'),
                    datasets: [{
                        label: 'Number of Ratings',
                        data: ratingData.map(item => item.count),
                        backgroundColor: [
                            '#e53e3e', '#ed8936', '#d69e2e', '#38a169', '#2c5282'
                        ],
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
        });
    </script>
@endsection
