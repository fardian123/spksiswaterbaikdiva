<aside class="app-sidebar bg-white border-end p-3">
    <ul class="nav nav-pills flex-column gap-1">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>
        </li>

        <li class="nav-item mt-2 px-3 small text-muted text-uppercase">Master Data</li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('siswa.*') ? 'active' : '' }}" href="{{ route('siswa.index') }}">
                <i class="bi bi-people me-2"></i>Data Siswa
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('mapel.*') ? 'active' : '' }}" href="{{ route('mapel.index') }}">
                <i class="bi bi-book me-2"></i>Data Mapel
            </a>
        </li>

        <li class="nav-item mt-2 px-3 small text-muted text-uppercase">Input Nilai</li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('nilai-akademik.*') ? 'active' : '' }}"
                href="{{ route('nilai-akademik.index') }}">
                <i class="bi bi-mortarboard me-2"></i>Akademik
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('nilai.*') ? 'active' : '' }}" href="{{ route('nilai.index') }}">
                <i class="bi bi-journal-text me-2"></i>Nilai Mapel
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('absensi.*') ? 'active' : '' }}"
                href="{{ route('absensi.index') }}">
                <i class="bi bi-calendar-check me-2"></i>Absensi
            </a>
        </li>

        <li class="nav-item mt-2 px-3 small text-muted text-uppercase">SPK</li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('kriteria.*') ? 'active' : '' }}"
                href="{{ route('kriteria.index') }}">
                <i class="bi bi-sliders me-2"></i>Kriteria & Bobot
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('spk.formProses') || request()->routeIs('spk.proses') ? 'active' : '' }}"
                href="{{ route('spk.formProses') }}">
                <i class="bi bi-calculator me-2"></i>Proses SPK
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('spk.hasil') ? 'active' : '' }}" href="{{ route('spk.hasil') }}">
                <i class="bi bi-bar-chart-line me-2"></i>Hasil & Laporan
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('ahp.*') ? 'active' : '' }}" href="{{ route('ahp.index') }}">
                <i class="bi bi-diagram me-2"></i>AHP
            </a>
        </li>
    </ul>
</aside>