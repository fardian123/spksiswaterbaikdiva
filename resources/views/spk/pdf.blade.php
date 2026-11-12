<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan SPK Siswa Terbaik</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #000;
        }

        h2,
        h4 {
            text-align: center;
            margin: 0;
        }

        h2 {
            margin-bottom: 4px;
        }

        .sub {
            text-align: center;
            margin-bottom: 14px;
            color: #444;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px 6px;
        }

        th {
            background: #e9ecef;
            text-align: center;
        }

        .c {
            text-align: center;
        }

        .r1 {
            background: #d1e7dd;
            font-weight: bold;
        }

        .ttd {
            margin-top: 40px;
            width: 100%;
        }

        .ttd td {
            border: none;
            text-align: center;
        }
    </style>
</head>

<body>
    <h2>LAPORAN PENENTUAN SISWA TERBAIK</h2>
    <h4>Sistem Pendukung Keputusan Metode SAW</h4>
    <div class="sub">
        @if ($semester) Semester: {{ $semester }} &middot; @endif
        Dicetak: {{ now()->format('d-m-Y') }}
    </div>

    <table>
        <thead>
            <tr>
                <th width="6%">Rank</th>
                <th width="12%">NIS</th>
                <th>Nama</th>
                <th>Akademik</th>
                <th>Umum</th>
                <th>Kejuruan</th>
                <th>Absensi</th>
                <th>Kreatif</th>
                <th>Skor Akhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($hasil as $h)
                <tr class="{{ $h->ranking == 1 ? 'r1' : '' }}">
                    <td class="c">{{ $h->ranking }}</td>
                    <td class="c">{{ $h->siswa->nis }}</td>
                    <td>{{ $h->siswa->nama }}</td>
                    <td class="c">{{ number_format($h->nilai_akademik, 2) }}</td>
                    <td class="c">{{ number_format($h->nilai_umum, 2) }}</td>
                    <td class="c">{{ number_format($h->nilai_kejuruan, 2) }}</td>
                    <td class="c">{{ number_format($h->nilai_absensi, 2) }}</td>
                    <td class="c">{{ number_format($h->nilai_kreatif, 2) }}</td>
                    <td class="c">{{ number_format($h->skor_akhir, 4) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="c">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="ttd">
        <tr>
            <td width="65%"></td>
            <td>
                Mengetahui,<br>Admin<br><br><br><br>
                ( ______________________ )
            </td>
        </tr>
    </table>
</body>

</html>