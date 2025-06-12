@extends('layouts.dashboard')

@section('title', 'Historical Timeline Analysis')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('reports.dashboard') }}">Heritage Reports</a></li>
    <li class="breadcrumb-item active">Historical Timeline</li>
@endsection

@section('content')
    <style>
        .era-timeline {
            position: relative;
            margin: 2rem 0;
        }

        .era-timeline::before {
            content: '';
            position: absolute;
            left: 30px;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(to bottom, #2c5282, #b7791f, #38a169);
            border-radius: 2px;
        }

        .era-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
            margin-left: 60px;
            position: relative;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .era-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }

        .era-card::before {
            content: '';
            position: absolute;
            left: -45px;
            top: 30px;
            width: 15px;
            height: 15px;
            background: #2c5282;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 0 0 3px #2c5282;
        }

        .era-header {
            background: linear-gradient(135deg, #2c5282 0%, #3182ce 100%);
            color: white;
            padding: 1.5rem;
            position: relative;
        }

        .era-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255,255,255,0.2);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
        }

        .heritage-category {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            margin: 0.2rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }

        .heritage-category:hover {
            background: #edf2f7;
            transform: translateY(-1px);
        }

        .category-pyramid { background: #fff5f5; border-color: #fed7d7; color: #c53030; }
        .category-mosque { background: #f0fff4; border-color: #c6f6d5; color: #2f855a; }
        .category-church { background: #f7fafc; border-color: #cbd5e0; color: #2d3748; }
        .category-temple { background: #fffaf0; border-color: #fbd38d; color: #c05621; }
        .category-palace { background: #faf5ff; border-color: #d6bcfa; color: #6b46c1; }
        .category-cemetery { background: #f0f4f8; border-color: #90a0b7; color: #4a5568; }
        .category-antiquity { background: #edf2f7; border-color: #a0aec0; color: #2d3748; }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 1rem;
            margin: 1.5rem 0;
        }

        .stat-item {
            text-align: center;
            padding: 1rem;
            background: #f7fafc;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: bold;
            color: #2c5282;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 0.8rem;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .timeline-summary {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .geographic-spread {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .location-tag {
            background: #e6fffa;
            color: #234e52;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            border: 1px solid #b2f5ea;
        }

        .diversity-meter {
            background: #f7fafc;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
        }

        .diversity-bar {
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 0.5rem;
        }

        .diversity-fill {
            height: 100%;
            background: linear-gradient(90deg, #2c5282, #38a169, #ed8936);
            border-radius: 4px;
            transition: width 0.8s ease;
        }

        @media (max-width: 768px) {
            .era-timeline::before { left: 15px; }
            .era-card { margin-left: 40px; }
            .era-card::before { left: -30px; }
            .era-badge {
                position: static;
                margin-top: 1rem;
                display: inline-block;
            }
        }
    </style>

    <!-- Page Header -->
    <div class="timeline-summary">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-2">
                    <i class="fas fa-history me-3 text-primary"></i>
                    Historical Timeline Analysis
                </h2>
                <p class="text-muted mb-0">
                    Comprehensive analysis of heritage sites across different historical eras in Egypt
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="btn-group">
                    <button class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-download me-2"></i>Export Timeline
                    </button>
                    <button class="btn btn-outline-info btn-sm">
                        <i class="fas fa-chart-line me-2"></i>View Charts
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Overall Statistics -->
    <div class="timeline-summary">
        <h5 class="mb-3">
            <i class="fas fa-chart-bar me-2 text-success"></i>
            Timeline Overview
        </h5>
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number">{{ $timeline_data->sum('total_sites') }}</div>
                <div class="stat-label">Total Sites</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $timeline_data->count() }}</div>
                <div class="stat-label">Historical Eras</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $timeline_data->sum('heritage_variety') }}</div>
                <div class="stat-label">Heritage Types</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ number_format($timeline_data->avg('heritage_density'), 1) }}%</div>
                <div class="stat-label">Avg Representation</div>
            </div>
        </div>
    </div>

    <!-- Era Distribution Chart -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="timeline-summary">
                <h5 class="mb-3">
                    <i class="fas fa-chart-pie me-2 text-warning"></i>
                    Sites by Historical Era
                </h5>
                <div style="height: 300px;">
                    <canvas id="eraDistributionChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="timeline-summary">
                <h5 class="mb-3">
                    <i class="fas fa-layer-group me-2 text-info"></i>
                    Heritage Diversity by Era
                </h5>
                <div style="height: 300px;">
                    <canvas id="diversityChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline -->
    <div class="era-timeline">
        @foreach($timeline_data->sortByDesc('total_sites') as $index => $era)
            <div class="era-card" style="--era-color: {{ $index % 2 == 0 ? '#2c5282' : '#b7791f' }}">
                <div class="era-header">
                    <div class="era-badge">{{ $era['total_sites'] }} Sites</div>
                    <h4 class="mb-1">{{ $era['era_name'] }}</h4>
                    <p class="mb-0 opacity-75">
                        {{ number_format($era['heritage_density'], 1) }}% of total documented heritage
                    </p>
                </div>

                <div class="card-body">
                    <!-- Heritage Categories Breakdown -->
                    <div class="mb-4">
                        <h6 class="mb-3">
                            <i class="fas fa-layer-group me-2"></i>
                            Heritage Categories ({{ $era['heritage_variety'] }} types)
                        </h6>
                        <div class="d-flex flex-wrap">
                            @if(isset($era['category_breakdown']) && is_array($era['category_breakdown']))
                                @foreach($era['category_breakdown'] as $category => $count)
                                    <span class="heritage-category category-{{ strtolower($category) }}">
                                {{ $category }}: {{ $count }}
                            </span>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- Diversity Meter -->
                    <div class="diversity-meter">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Heritage Diversity</span>
                            <span class="text-muted">{{ $era['heritage_variety'] }}/7 categories</span>
                        </div>
                        <div class="diversity-bar">
                            <div class="diversity-fill" style="width: {{ ($era['heritage_variety'] / 7) * 100 }}%"></div>
                        </div>
                    </div>

                    <!-- Geographic Spread -->
                    @if(isset($era['geographic_spread']) && count($era['geographic_spread']) > 0)
                        <div class="mb-3">
                            <h6 class="mb-2">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                Geographic Distribution
                            </h6>
                            <div class="geographic-spread">
                                @foreach($era['geographic_spread'] as $location)
                                    <span class="location-tag">{{ $location }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Era Statistics -->
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="text-center">
                                <h5 class="text-primary mb-1">{{ $era['total_sites'] }}</h5>
                                <small class="text-muted">Total Sites</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h5 class="text-success mb-1">{{ $era['heritage_variety'] }}</h5>
                                <small class="text-muted">Heritage Types</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h5 class="text-warning mb-1">{{ count($era['geographic_spread'] ?? []) }}</h5>
                                <small class="text-muted">Governorates</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Insights and Analysis -->
    <div class="timeline-summary">
        <h5 class="mb-3">
            <i class="fas fa-lightbulb me-2 text-warning"></i>
            Historical Insights & Analysis
        </h5>

        <div class="row">
            <div class="col-lg-4">
                <div class="border-start border-primary border-4 ps-3 mb-3">
                    <h6 class="text-primary">Most Documented Era</h6>
                    @php $topEra = $timeline_data->sortByDesc('total_sites')->first(); @endphp
                    <p class="mb-1"><strong>{{ $topEra['era_name'] }}</strong></p>
                    <small class="text-muted">{{ $topEra['total_sites'] }} heritage sites documented</small>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="border-start border-success border-4 ps-3 mb-3">
                    <h6 class="text-success">Most Diverse Era</h6>
                    @php $diverseEra = $timeline_data->sortByDesc('heritage_variety')->first(); @endphp
                    <p class="mb-1"><strong>{{ $diverseEra['era_name'] }}</strong></p>
                    <small class="text-muted">{{ $diverseEra['heritage_variety'] }} different heritage types</small>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="border-start border-info border-4 ps-3 mb-3">
                    <h6 class="text-info">Geographic Coverage</h6>
                    @php $maxLocations = $timeline_data->max(function($era) { return count($era['geographic_spread'] ?? []); }); @endphp
                    <p class="mb-1"><strong>{{ $maxLocations }} Governorates</strong></p>
                    <small class="text-muted">Maximum geographic spread</small>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <h6 class="mb-3">Key Historical Observations:</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-arrow-right text-primary me-2"></i>
                        <strong>Temporal Distribution:</strong> {{ $timeline_data->count() }} distinct historical eras are represented in the heritage database
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-arrow-right text-success me-2"></i>
                        <strong>Heritage Concentration:</strong> The top 3 eras account for {{ number_format($timeline_data->sortByDesc('total_sites')->take(3)->sum('heritage_density'), 1) }}% of all documented sites
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-arrow-right text-warning me-2"></i>
                        <strong>Category Diversity:</strong> Most eras show representation across multiple heritage categories, indicating rich cultural complexity
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-arrow-right text-info me-2"></i>
                        <strong>Research Opportunities:</strong> Eras with lower site counts may benefit from focused archaeological and historical research
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const timelineData = @json($timeline_data);

            // Era Distribution Pie Chart
            const eraCtx = document.getElementById('eraDistributionChart').getContext('2d');
            new Chart(eraCtx, {
                type: 'doughnut',
                data: {
                    labels: timelineData.map(era => era.era_name),
                    datasets: [{
                        data: timelineData.map(era => era.total_sites),
                        backgroundColor: [
                            '#2c5282', '#b7791f', '#38a169', '#ed8936', '#e53e3e',
                            '#805ad5', '#3182ce', '#d69e2e', '#48bb78'
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
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const era = timelineData[context.dataIndex];
                                    return [
                                        `${era.era_name}: ${era.total_sites} sites`,
                                        `Diversity: ${era.heritage_variety} types`,
                                        `Coverage: ${era.heritage_density.toFixed(1)}%`
                                    ];
                                }
                            }
                        }
                    }
                }
            });

            // Heritage Diversity Bar Chart
            const diversityCtx = document.getElementById('diversityChart').getContext('2d');
            new Chart(diversityCtx, {
                type: 'bar',
                data: {
                    labels: timelineData.map(era => era.era_name),
                    datasets: [{
                        label: 'Heritage Types',
                        data: timelineData.map(era => era.heritage_variety),
                        backgroundColor: 'rgba(56, 161, 105, 0.8)',
                        borderColor: '#38a169',
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
                                    const era = timelineData[context.dataIndex];
                                    return [
                                        `Total Sites: ${era.total_sites}`,
                                        `Density: ${era.heritage_density.toFixed(1)}%`
                                    ];
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 7,
                            ticks: {
                                stepSize: 1,
                                callback: function(value) {
                                    return value + ' types';
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
        });
    </script>
@endsection
