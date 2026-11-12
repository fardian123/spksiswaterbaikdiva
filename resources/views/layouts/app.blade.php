<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - SPK Siswa Terbaik</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .app-body {
            flex: 1;
            display: flex;
        }

        .app-sidebar {
            width: 240px;
            flex-shrink: 0;
        }

        .app-main {
            flex: 1;
            min-width: 0;
        }

        .app-sidebar .nav-link {
            color: #333;
            border-radius: .375rem;
        }

        .app-sidebar .nav-link.active {
            background: #0d6efd;
            color: #fff;
        }

        .app-sidebar .nav-link:hover:not(.active) {
            background: #e9ecef;
        }
    </style>
</head>

<body class="bg-light">

    @auth
        @include('layouts.navbar')
    @endauth

    <div class="app-body">
        @auth
            @include('layouts.sidebar')
        @endauth

        <main class="app-main p-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @auth
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            @endauth

            @yield('content')
        </main>
    </div>

    @auth
        @include('layouts.footer')
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>