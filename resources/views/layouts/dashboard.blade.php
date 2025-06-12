<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand {
            font-weight: bold;
        }
        .navbar-nav .nav-link {
            padding: 10px 15px;
            transition: background-color 0.2s;
        }
        .navbar-nav .nav-link:hover {
            background-color: #e9ecef;
            border-radius: 5px;
        }
        .navbar-nav .btn-link {
            color: #dc3545;
        }
        .content-container {
            padding: 20px 15px;
        }
        .breadcrumb {
            background-color: #ffffff;
            padding: 10px 15px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        @media (max-width: 991px) {
            .navbar-nav {
                padding: 10px 0;
            }
            .navbar-nav .nav-item {
                margin: 5px 10px;
            }
            .navbar-nav .nav-link {
                padding: 8px 10px;
            }
            .navbar-collapse {
                background-color: #ffffff;
                border-top: 1px solid #dee2e6;
                max-height: 80vh;
                overflow-y: auto;
            }
            .content-container {
                padding: 15px 10px;
            }
        }
        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1.2rem;
            }
            .navbar-toggler {
                padding: 8px;
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
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('dashboard') }}">Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('articles.index') }}">Articles</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('comments.index') }}">Comments</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('ratings.index') }}">Ratings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('community_posts.index') }}">Community Posts</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('community_comments.index') }}">Community Comments</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('users.index') }}">Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('certified_researchers.index') }}">Certified Researchers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('eras.index') }}">Eras</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('governorates.index') }}">Governorates</a>
                </li>
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="nav-link btn btn-link">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="content-container container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            @yield('breadcrumb')
        </ol>
    </nav>
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
