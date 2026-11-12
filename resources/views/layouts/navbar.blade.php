<nav class="navbar navbar-dark bg-primary px-3">
    <a class="navbar-brand fw-semibold" href="{{ route('dashboard') }}">
        <i class="bi bi-trophy-fill me-2"></i>SPK Siswa Terbaik
    </a>

    <div class="d-flex align-items-center text-white">
        <span class="me-3"><i class="bi bi-person-circle me-1"></i>{{ Auth::user()->name }}</span>
        <form action="{{ route('logout') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="btn btn-sm btn-light">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </div>
</nav>