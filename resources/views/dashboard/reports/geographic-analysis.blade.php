@extends('layouts.dashboard')

@section('title', 'Geographic Analysis')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('reports.dashboard') }}">Heritage Reports</a></li>
    <li class="breadcrumb-item active">Geographic Analysis</li>
@endsection

@section('content')
    <style>
        .governorate-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border: none;
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .governorate-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .governorate-header {
            background: linear-gradient(135deg, #2c5282 0%, #3182ce 100%);
            color: white;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .governorate-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20px;
            width: 100px;
            height: 200%;
            background: rgba(255,255,255,0.1);
            transform: rotate(15deg);
        }

        .tourism-score {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255,255,255,0.2);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: bold;
        }

        .score-high { background: linear-gradient(135deg, #38a169, #48bb78); }
        .score-medium { background: linear-gradient(135deg, #ed8936, #fd7f28); }
        .score-low { background: linear-gradient(135deg, #e53e3e, #fc8181); }

        .heritage-metric {
            background: #f7fafc;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 1rem;
        }

        .metric-number {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2c5282;
            margin-bottom: 0.25rem;
        }

        .metric-label {
            font-size: 0.85rem;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .category-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background: #e6fffa;
            color: #234e52;
            border-radius: 15px;
            font-size: 0.8rem;
            margin: 0.2rem;
            border: 1px solid #b2f5ea;
        }

        .summary-stats {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .tourism-score {
                position: static;
                margin-top: 1rem;
                display: inline-block;
            }
            .governorate-header {
                text-align: center;
            }
        }
    </style>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="summary-stats">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="mb-2">
                            <i class="fas fa-map-marked-alt me-3 text-primary"></i>
                            Geographic Distribution Analysis
                        </h2>
                        <p class="text-muted mb-0">Comprehensive analysis of heritage sites across Egyptian governorates</p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="btn-group">
                            <button class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-download me-2"></i>Export Report
                            </button>
                            <button class="btn btn-outline-info btn-sm">
                                <i class="fas fa-chart-bar me-2"></i>View Charts
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="heritage-metric">
                <div class="metric-number">{{ $geographic_data->sum('heritage_sites') }}</div>
                <div class="metric-label">Total Heritage Sites</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="heritage-metric">
                <div class="metric-number">{{ $geographic_data->where('heritage_sites', '>', 0)->count() }}</div>
                <div class="metric-label">Covered Governorates</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="heritage-metric">
                <div class="metric-number">{{ number_format($geographic_data->avg('avg_rating'), 1) }}</div>
                <div class="metric-label">Average Rating</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="heritage-metric">
                <div class="metric-number">{{ number_format($geographic_data->sum('visit_count')) }}</div>
                <div class="metric-label">Total Visits</div>
            </div>
        </div>
    </div>

    <!-- Tourism Potential Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="governorate-card">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2 text-success"></i>
                        Tourism Potential by Governorate
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 400px;">
                        <canvas id="tourismPotentialChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Heritage Sites Distribution -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="governorate-card">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="mb-0">
                        <i class="fas fa-monument me-2 text-warning"></i>
                        Sites per Governorate
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 350px;">
                        <canvas id="sitesDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="governorate-card">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="mb-0">
                        <i class="fas fa-star me-2 text-info"></i>
                        Quality vs Quantity Analysis
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 350px;">
                        <canvas id="qualityQuantityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Governorate Cards -->
    <div class="row">
        @forelse($geographic_data->sortByDesc('tourism_potential')->take(12) as $governorate)
            <div class="col-lg-6 col-xl-4">
                <div class="governorate-card">
                    <div class="governorate-header">
                        <div class="tourism-score
                            @if(($governorate['tourism_potential'] ?? 0) >= 70) score-high
                            @elseif(($governorate['tourism_potential'] ?? 0) >= 40) score-medium
                            @else score-low
                            @endif">
                            {{ number_format($governorate['tourism_potential'] ?? 0, 1) }}
                        </div>
                        <h5 class="mb-1">{{ $governorate['name'] ?? 'Unknown' }}</h5>
                        <small class="opacity-75">Tourism Potential Score</small>
                    </div>

                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="text-center">
                                    <h4 class="text-primary mb-0">{{ $governorate['heritage_sites'] ?? 0 }}</h4>
                                    <small class="text-muted">Heritage Sites</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <h4 class="text-warning mb-0">{{ number_format($governorate['avg_rating'] ?? 0, 1) }}</h4>
                                    <small class="text-muted">Avg Rating</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="text-center">
                                    <h6 class="text-info mb-0">{{ number_format($governorate['visit_count'] ?? 0) }}</h6>
                                    <small class="text-muted">Total Visits</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <h6 class="text-success mb-0">{{ $governorate['heritage_types'] ?? 0 }}</h6>
                                    <small class="text-muted">Heritage Types</small>
                                </div>
                            </div>
                        </div>

                        @if(isset($governorate['dominant_category']) && $governorate['dominant_category'] !== 'N/A')
                            <div class="mb-2">
                                <small class="text-muted">Dominant Category:</small>
                                <span class="category-badge">{{ $governorate['dominant_category'] }}</span>
                            </div>
                        @endif

                        <!-- Progress bar for tourism potential -->
                        <div class="mt-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small class="text-muted">Tourism Development</small>
                                <small class="text-muted">{{ number_format($governorate['tourism_potential'] ?? 0, 1) }}%</small>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar
                                    @if(($governorate['tourism_potential'] ?? 0) >= 70) bg-success
                                    @elseif(($governorate['tourism_potential'] ?? 0) >= 40) bg-warning
                                    @else bg-danger
                                    @endif"
                                     style="width: {{ min($governorate['tourism_potential'] ?? 0, 100) }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No geographic data available. Please ensure you have governorates and articles in your database.
                </div>
            </div>
        @endforelse
    </div>

    <!-- Key Insights & Recommendations -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="governorate-card">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb me-2 text-warning"></i>
                        Key Insights & Recommendations
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="border-start border-success border-4 ps-3 mb-3">
                                <h6 class="text-success">High Potential Areas</h6>
                                <p class="mb-0 small text-muted">
                                    @php
                                        $highPotential = $geographic_data->filter(function($item) {
                                            return ($item['tourism_potential'] ?? 0) >= 70;
                                        });
                                    @endphp
                                    {{ $highPotential->count() }} governorates show excellent tourism potential with
                                    high heritage site density and visitor engagement.
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="border-start border-warning border-4 ps-3 mb-3">
                                <h6 class="text-warning">Development Opportunities</h6>
                                <p class="mb-0 small text-muted">
                                    @php
                                        $mediumPotential = $geographic_data->filter(function($item) {
                                            $potential = $item['tourism_potential'] ?? 0;
                                            return $potential >= 40 && $potential < 70;
                                        });
                                    @endphp
                                    {{ $mediumPotential->count() }} governorates have moderate potential and could
                                    benefit from increased heritage documentation and promotion.
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="border-start border-info border-4 ps-3 mb-3">
                                <h6 class="text-info">Coverage Gaps</h6>
                                <p class="mb-0 small text-muted">
                                    @php
                                        $uncovered = $geographic_data->filter(function($item) {
                                            return ($item['heritage_sites'] ?? 0) == 0;
                                        });
                                    @endphp
                                    {{ $uncovered->count() }} governorates need initial heritage site documentation
                                    to establish baseline coverage.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="mb-2">Actionable Recommendations:</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-arrow-right text-primary me-2"></i>
                                    <strong>Focus on Quality:</strong> Governorates with high site counts but low ratings need content quality improvement
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-arrow-right text-success me-2"></i>
                                    <strong>Tourism Development:</strong> High-rated, well-documented areas are ready for tourism promotion
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-arrow-right text-warning me-2"></i>
                                    <strong>Research Expansion:</strong> Deploy certified researchers to underrepresented governorates
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const geographicData = @json($geographic_data->values());

            // Tourism Potential Chart
            const tourismCtx = document.getElementById('tourismPotentialChart').getContext('2d');
            new Chart(tourismCtx, {
                type: 'bar',
                data: {
                    labels: geographicData.slice(0, 15).map(item => item.name),
                    datasets: [{
                        label: 'Tourism Potential Score',
                        data: geographicData.slice(0, 15).map(item => item.tourism_potential || 0),
                        backgroundColor: geographicData.slice(0, 15).map(item => {
                            const potential = item.tourism_potential || 0;
                            if (potential >= 70) return 'rgba(56, 161, 105, 0.8)';
                            if (potential >= 40) return 'rgba(237, 137, 54, 0.8)';
                            return 'rgba(229, 62, 62, 0.8)';
                        }),
                        borderColor: geographicData.slice(0, 15).map(item => {
                            const potential = item.tourism_potential || 0;
                            if (potential >= 70) return '#38a169';
                            if (potential >= 40) return '#ed8936';
                            return '#e53e3e';
                        }),
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                afterLabel: function(context) {
                                    const data = geographicData[context.dataIndex];
                                    return [
                                        `Heritage Sites: ${data.heritage_sites || 0}`,
                                        `Avg Rating: ${(data.avg_rating || 0).toFixed(1)}`,
                                        `Visits: ${(data.visit_count || 0).toLocaleString()}`
                                    ];
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
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

            // Sites Distribution Chart
            const sitesCtx = document.getElementById('sitesDistributionChart').getContext('2d');
            const sitesData = geographicData.filter(item => (item.heritage_sites || 0) > 0).slice(0, 10);

            new Chart(sitesCtx, {
                type: 'doughnut',
                data: {
                    labels: sitesData.map(item => item.name),
                    datasets: [{
                        data: sitesData.map(item => item.heritage_sites || 0),
                        backgroundColor: [
                            '#2c5282', '#b7791f', '#38a169', '#ed8936', '#e53e3e',
                            '#805ad5', '#3182ce', '#d69e2e', '#48bb78', '#fc8181'
                        ],
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
                                padding: 15,
                                usePointStyle: true,
                                font: { size: 11 }
                            }
                        }
                    }
                }
            });

            // Quality vs Quantity Scatter Chart
            const qualityCtx = document.getElementById('qualityQuantityChart').getContext('2d');
            const qualityData = geographicData.filter(item => (item.heritage_sites || 0) > 0 && (item.avg_rating || 0) > 0);

            new Chart(qualityCtx, {
                type: 'scatter',
                data: {
                    datasets: [{
                        label: 'Governorates',
                        data: qualityData.map(item => ({
                            x: item.heritage_sites || 0,
                            y: item.avg_rating || 0,
                            label: item.name
                        })),
                        backgroundColor: 'rgba(44, 82, 130, 0.6)',
                        borderColor: '#2c5282',
                        borderWidth: 2,
                        pointRadius: function(context) {
                            const value = qualityData[context.dataIndex];
                            return Math.max(5, Math.min(15, (value.visit_count || 0) / 1000));
                        },
                        pointHoverRadius: function(context) {
                            const value = qualityData[context.dataIndex];
                            return Math.max(7, Math.min(20, (value.visit_count || 0) / 800));
                        }
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                title: function(context) {
                                    return qualityData[context[0].dataIndex].name;
                                },
                                label: function(context) {
                                    const data = qualityData[context.dataIndex];
                                    return [
                                        `Heritage Sites: ${data.heritage_sites || 0}`,
                                        `Average Rating: ${(data.avg_rating || 0).toFixed(1)}`,
                                        `Total Visits: ${(data.visit_count || 0).toLocaleString()}`,
                                        `Tourism Score: ${(data.tourism_potential || 0).toFixed(1)}%`
                                    ];
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Number of Heritage Sites'
                            },
                            beginAtZero: true
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Average Rating (1-5)'
                            },
                            min: 0,
                            max: 5
                        }
                    }
                }
            });
        });
    </script>
@endsection
