<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Heritage Management Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            background: linear-gradient(135deg, #2c5282 0%, #3182ce 100%) !important;
        }
        .navbar-brand {
            font-weight: bold;
            color: white !important;
        }
        .navbar-nav .nav-link {
            padding: 10px 15px;
            transition: all 0.2s;
            color: rgba(255,255,255,0.9) !important;
            border-radius: 6px;
            margin: 0 2px;
        }
        .navbar-nav .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            color: white !important;
            transform: translateY(-1px);
        }
        .navbar-nav .nav-link.active {
            background-color: rgba(255,255,255,0.2);
            color: white !important;
        }
        .navbar-nav .btn-link {
            color: #fbb6ce !important;
        }
        .navbar-nav .btn-link:hover {
            color: #f687b3 !important;
            background-color: rgba(251, 182, 206, 0.1);
        }
        .content-container {
            padding: 20px 15px;
        }
        .breadcrumb {
            background-color: #ffffff;
            padding: 10px 15px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border-left: 4px solid #2c5282;
        }
        .breadcrumb-item a {
            color: #2c5282;
            text-decoration: none;
        }
        .breadcrumb-item a:hover {
            color: #1a365d;
            text-decoration: underline;
        }

        /* Reports Dropdown */
        .dropdown-menu {
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            border: none;
        }
        .dropdown-item {
            padding: 10px 20px;
            transition: all 0.2s;
        }
        .dropdown-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }
        .dropdown-item i {
            width: 20px;
            text-align: center;
        }

        /* Mobile Responsiveness */
        @media (max-width: 991px) {
            .navbar-nav {
                padding: 10px 0;
            }
            .navbar-nav .nav-item {
                margin: 2px 10px;
            }
            .navbar-nav .nav-link {
                padding: 8px 10px;
            }
            .navbar-collapse {
                background-color: rgba(44, 82, 130, 0.95);
                border-top: 1px solid rgba(255,255,255,0.1);
                max-height: 80vh;
                overflow-y: auto;
                margin-top: 10px;
                border-radius: 8px;
            }
            .content-container {
                padding: 15px 10px;
            }
        }
        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1.1rem;
            }
            .navbar-toggler {
                padding: 8px;
                border-color: rgba(255,255,255,0.3);
            }
            .navbar-toggler-icon {
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
            }
            .content-container {
                padding: 10px 5px;
            }
            .breadcrumb {
                font-size: 0.9rem;
                padding: 8px 10px;
            }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <i class="fas fa-landmark me-2"></i>Heritage Dashboard
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <!-- Content Management -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="contentDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-edit me-2"></i>Content
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="contentDropdown">
                        <li><a class="dropdown-item" href="{{ route('articles.index') }}">
                                <i class="fas fa-monument me-2 text-primary"></i>Articles
                            </a></li>
                        <li><a class="dropdown-item" href="{{ route('eras.index') }}">
                                <i class="fas fa-history me-2 text-info"></i>Eras
                            </a></li>
                        <li><a class="dropdown-item" href="{{ route('governorates.index') }}">
                                <i class="fas fa-map-marked-alt me-2 text-success"></i>Governorates
                            </a></li>
                    </ul>
                </li>

                <!-- Community Management -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="communityDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-users me-2"></i>Community
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="communityDropdown">
                        <li><a class="dropdown-item" href="{{ route('users.index') }}">
                                <i class="fas fa-user me-2 text-primary"></i>Users
                            </a></li>
                        <li><a class="dropdown-item" href="{{ route('certified_researchers.index') }}">
                                <i class="fas fa-graduation-cap me-2 text-success"></i>Certified Researchers
                            </a></li>
                        <li><a class="dropdown-item" href="{{ route('community_posts.index') }}">
                                <i class="fas fa-comments me-2 text-info"></i>Community Posts
                            </a></li>
                    </ul>
                </li>

                <!-- Moderation -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="moderationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-shield-alt me-2"></i>Moderation
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="moderationDropdown">
                        <li><a class="dropdown-item" href="{{ route('comments.index') }}">
                                <i class="fas fa-comment me-2 text-warning"></i>Comments
                            </a></li>
                        <li><a class="dropdown-item" href="{{ route('ratings.index') }}">
                                <i class="fas fa-star me-2 text-success"></i>Ratings
                            </a></li>
                        <li><a class="dropdown-item" href="{{ route('community_comments.index') }}">
                                <i class="fas fa-comments me-2 text-info"></i>Community Comments
                            </a></li>
                    </ul>
                </li>

                <!-- 🎯 NEW: Heritage Reports -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="reportsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-chart-line me-2"></i>Reports
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="reportsDropdown">
                        <li><a class="dropdown-item" href="{{ route('reports.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2 text-primary"></i>Heritage Dashboard
                            </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('reports.geographic') }}">
                                <i class="fas fa-map me-2 text-success"></i>Geographic Analysis
                            </a></li>
                        <li><a class="dropdown-item" href="{{ route('reports.timeline') }}">
                                <i class="fas fa-history me-2 text-info"></i>Historical Timeline
                            </a></li>
                        <li><a class="dropdown-item" href="{{ route('reports.research-community') }}">
                                <i class="fas fa-users me-2 text-warning"></i>Research Community
                            </a></li>
                        <li><a class="dropdown-item" href="{{ route('reports.content-quality') }}">
                                <i class="fas fa-star me-2 text-danger"></i>Content Quality
                            </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('reports.tourism-potential') }}">
                                <i class="fas fa-plane me-2 text-secondary"></i>Tourism Potential
                            </a></li>
                    </ul>
                </li>

                <!-- Logout -->
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="nav-link btn btn-link">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="content-container container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">
                    <i class="fas fa-home me-1"></i>Dashboard
                </a>
            </li>
            @yield('breadcrumb')
        </ol>
    </nav>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
