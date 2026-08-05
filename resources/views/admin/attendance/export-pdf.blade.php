<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi - PDF</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>Laporan Absensi Karyawan</h2>
    <p style="text-align: center; margin-bottom: 20px;">
        Periode: {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}
    </p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Karyawan</th>
                <th>Departemen</th>
                <th>Jam Masuk</th>
                <th>Jam Keluar</th>
                <th>Status</th>
                <th>Sumber</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $index => $attendance)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') }}</td>
                    <td>{{ $attendance->employee->name ?? '-' }}</td>
                    <td>{{ $attendance->employee->department ?? '-' }}</td>
                    <td>{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '-' }}</td>
                    <td>{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '-' }}</td>
                    <td>
                        @if($attendance->status === 'on_time') Tepat Waktu
                        @elseif($attendance->status === 'late') Terlambat
                        @elseif($attendance->status === 'absent') Alpha
                        @elseif($attendance->status === 'leave') Cuti
                        @else {{ ucfirst($attendance->status) }}
                        @endif
                    </td>
                    <td>{{ $attendance->source === 'fingerprint' ? 'Fingerprint' : 'PWA/GPS' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">Tidak ada data absensi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
