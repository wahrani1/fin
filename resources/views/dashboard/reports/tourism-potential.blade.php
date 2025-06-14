@extends('layouts.dashboard')

@section('title', 'Tourism Potential Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('reports.dashboard') }}">Heritage Reports</a></li>
    <li class="breadcrumb-item active">Tourism Potential</li>
@endsection

@section('content')
    <!-- Add Chart.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.3.0/chart.min.js"></script>

    <style>
        .tourism-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }

        .tourism-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }

        .destination-card {
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            position: relative;
            overflow: hidden;
        }

        .destination-high { background: linear-gradient(135deg, #38a169, #48bb78); color: white; }
        .destination-medium { background: linear-gradient(135deg, #ed8936, #fd7f28); color: white; }
        .destination-low { background: linear-gradient(135deg, #e53e3e, #fc8181); color: white; }

        .tourism-score-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255,255,255,0.2);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: bold;
        }

        .metric-item {
            text-align: center;
            padding: 1rem;
            background: #f7fafc;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .potential-meter {
            height: 10px;
            background: #e2e8f0;
            border-radius: 5px;
            overflow: hidden;
            margin-top: 0.5rem;
        }

        .potential-fill {
            height: 100%;
            border-radius: 5px;
            transition: width 0.8s ease;
        }

        .tourism-insight {
            background: #f8f9fa;
            border-left: 4px solid #2c5282;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0 8px 8px 0;
        }
    </style>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="tourism-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2">
                                <i class="fas fa-plane me-3 text-primary"></i>
                                Tourism Potential Analysis
                            </h2>
                            <p class="text-muted mb-0">Assessment of Egyptian governorates' tourism development potential based on heritage sites, ratings, and visitor engagement</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="btn-group">
                                <button class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-download me-2"></i>Export Report
                                </button>
                                <button class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-map me-2"></i>View Map
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tourism Overview -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="metric-item">
                <h3 class="text-primary">{{ $tourism_data->sum('heritage_sites') }}</h3>
                <p class="mb-0 text-muted">Total Heritage Sites</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="metric-item">
                <h3 class="text-success">{{ $tourism_data->where('heritage_sites', '>', 0)->count() }}</h3>
                <p class="mb-0 text-muted">Tourism Destinations</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="metric-item">
                <h3 class="text-warning">{{ number_format($tourism_data->sum('visit_count')) }}</h3>
                <p class="mb-0 text-muted">Total Visits</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="metric-item">
                <h3 class="text-info">{{ number_format($tourism_data->avg('average_rating'), 1) }}</h3>
                <p class="mb-0 text-muted">Average Rating</p>
            </div>
        </div>
    </div>

    <!-- Tourism Potential Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="tourism-card">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2 text-success"></i>
                        Tourism Potential Ranking
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

    <!-- Top Tourism Destinations -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="tourism-card">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-trophy me-2 text-warning"></i>
                        Top Tourism Destinations
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($tourism_data->sortByDesc('tourism_score')->take(12) as $destination)
                            <div class="col-lg-4 col-md-6">
                                <div class="destination-card
                                @if($destination['tourism_score'] >= 70) destination-high
                                @elseif($destination['tourism_score'] >= 40) destination-medium
                                @else destination-low
                                @endif">

                                    <div class="tourism-score-badge">
                                        {{ number_format($destination['tourism_score'], 1) }}
                                    </div>

                                    <h5 class="mb-2">{{ $destination['governorate'] }}</h5>

                                    <div class="row text-center mb-3">
                                        <div class="col-4">
                                            <h6 class="mb-0">{{ $destination['heritage_sites'] }}</h6>
                                            <small class="opacity-75">Sites</small>
                                        </div>
                                        <div class="col-4">
                                            <h6 class="mb-0">{{ number_format($destination['visit_count']) }}</h6>
                                            <small class="opacity-75">Visits</small>
                                        </div>
                                        <div class="col-4">
                                            <h6 class="mb-0">{{ number_format($destination['average_rating'], 1) }}</h6>
                                            <small class="opacity-75">Rating</small>
                                        </div>
                                    </div>

                                    @if($destination['brief'])
                                        <p class="mb-0 small opacity-90">{{ Str::limit($destination['brief'], 100) }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tourism Categories -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="tourism-card">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2 text-info"></i>
                        Potential vs Performance Analysis
                    </h5>
                </div>
                <div class="card-body">
                    <div style="height: 350px;">
                        <canvas id="potentialPerformanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="tourism-card">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-layer-group me-2 text-secondary"></i>
                        Tourism Categories
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $highPotential = $tourism_data->filter(function($item) { return $item['tourism_score'] >= 70; })->count();
                        $mediumPotential = $tourism_data->filter(function($item) { return $item['tourism_score'] >= 40 && $item['tourism_score'] < 70; })->count();
                        $lowPotential = $tourism_data->filter(function($item) { return $item['tourism_score'] < 40; })->count();
                    @endphp

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-success">High Potential</span>
                            <span class="text-success">{{ $highPotential }}</span>
                        </div>
                        <div class="potential-meter">
                            <div class="potential-fill bg-success" style="width: {{ $tourism_data->count() > 0 ? ($highPotential / $tourism_data->count()) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-warning">Medium Potential</span>
                            <span class="text-warning">{{ $mediumPotential }}</span>
                        </div>
                        <div class="potential-meter">
                            <div class="potential-fill bg-warning" style="width: {{ $tourism_data->count() > 0 ? ($mediumPotential / $tourism_data->count()) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-danger">Development Needed</span>
                            <span class="text-danger">{{ $lowPotential }}</span>
                        </div>
                        <div class="potential-meter">
                            <div class="potential-fill bg-danger" style="width: {{ $tourism_data->count() > 0 ? ($lowPotential / $tourism_data->count()) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tourism Insights -->
    <div class="row">
        <div class="col-12">
            <div class="tourism-card">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb me-2 text-warning"></i>
                        Tourism Development Insights
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="tourism-insight">
                                <h6 class="text-primary mb-2">Ready for Tourism Promotion</h6>
                                <p class="mb-2">Governorates with high tourism potential scores (70+) are ready for immediate tourism development and marketing campaigns.</p>
                                <ul class="mb-0">
                                    @foreach($tourism_data->sortByDesc('tourism_score')->take(3) as $top)
                                        <li>{{ $top['governorate'] }} ({{ number_format($top['tourism_score'], 1) }})</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="tourism-insight">
                                <h6 class="text-success mb-2">Development Opportunities</h6>
                                <p class="mb-2">Focus areas for infrastructure development and heritage site enhancement to boost tourism potential.</p>
                                <ul class="mb-0">
                                    <li>Improve heritage site documentation and photography</li>
                                    <li>Enhance visitor experience and accessibility</li>
                                    <li>Develop tourism infrastructure and services</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="mb-2">Strategic Recommendations:</h6>
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="border-start border-primary border-4 ps-3">
                                        <strong class="text-primary">High-Priority Development</strong>
                                        <p class="small text-muted mb-0">Invest in top-performing governorates to maximize tourism revenue and establish Egypt as a premium heritage destination.</p>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="border-start border-warning border-4 ps-3">
                                        <strong class="text-warning">Balanced Growth</strong>
                                        <p class="small text-muted mb-0">Support medium-potential areas with targeted improvements to create diverse tourism circuits across Egypt.</p>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="border-start border-info border-4 ps-3">
                                        <strong class="text-info">Long-term Planning</strong>
                                        <p class="small text-muted mb-0">Develop comprehensive heritage preservation and tourism strategies for sustainable economic growth.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tourismData = @json($tourism_data->values());

            // Tourism Potential Chart
            const potentialCtx = document.getElementById('tourismPotentialChart').getContext('2d');
            const topDestinations = tourismData.slice(0, 15);

            new Chart(potentialCtx, {
                type: 'bar',
                data: {
                    labels: topDestinations.map(item => item.governorate),
                    datasets: [{
                        label: 'Tourism Potential Score',
                        data: topDestinations.map(item => item.tourism_score),
                        backgroundColor: topDestinations.map(item => {
                            if (item.tourism_score >= 70) return 'rgba(56, 161, 105, 0.8)';
                            if (item.tourism_score >= 40) return 'rgba(237, 137, 54, 0.8)';
                            return 'rgba(229, 62, 62, 0.8)';
                        }),
                        borderColor: topDestinations.map(item => {
                            if (item.tourism_score >= 70) return '#38a169';
                            if (item.tourism_score >= 40) return '#ed8936';
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
                                    const data = topDestinations[context.dataIndex];
                                    return [
                                        `Heritage Sites: ${data.heritage_sites}`,
                                        `Visits: ${data.visit_count.toLocaleString()}`,
                                        `Avg Rating: ${data.average_rating.toFixed(1)}`
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
                            ticks: { maxRotation: 45 }
                        }
                    }
                }
            });

            // Potential vs Performance Scatter Chart
            const scatterCtx = document.getElementById('potentialPerformanceChart').getContext('2d');
            const scatterData = tourismData.filter(item => item.heritage_sites > 0);

            new Chart(scatterCtx, {
                type: 'scatter',
                data: {
                    datasets: [{
                        label: 'Governorates',
                        data: scatterData.map(item => ({
                            x: item.heritage_sites,
                            y: item.tourism_score,
                            label: item.governorate
                        })),
                        backgroundColor: 'rgba(44, 82, 130, 0.6)',
                        borderColor: '#2c5282',
                        borderWidth: 2,
                        pointRadius: function(context) {
                            const value = scatterData[context.dataIndex];
                            return Math.max(5, Math.min(15, value.visit_count / 5000));
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
                                    return scatterData[context[0].dataIndex].governorate;
                                },
                                label: function(context) {
                                    const data = scatterData[context.dataIndex];
                                    return [
                                        `Heritage Sites: ${data.heritage_sites}`,
                                        `Tourism Score: ${data.tourism_score.toFixed(1)}%`,
                                        `Total Visits: ${data.visit_count.toLocaleString()}`,
                                        `Avg Rating: ${data.average_rating.toFixed(1)}/5`
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
                                text: 'Tourism Potential Score (%)'
                            },
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });
        });
    </script>
@endsection
