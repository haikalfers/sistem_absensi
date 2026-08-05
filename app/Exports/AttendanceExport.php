<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class AttendanceExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(
        private ?string $startDate = null,
        private ?string $endDate   = null,
        private ?int    $employeeId = null
    ) {}

    public function query()
    {
        return Attendance::with('employee')
            ->when($this->startDate, fn($q) => $q->whereDate('date', '>=', $this->startDate))
            ->when($this->endDate,   fn($q) => $q->whereDate('date', '<=', $this->endDate))
            ->when($this->employeeId, fn($q) => $q->where('employee_id', $this->employeeId))
            ->orderBy('date', 'desc')
            ->orderBy('employee_id');
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Karyawan',
            'Nama Karyawan',
            'Departemen',
            'Tanggal',
            'Hari',
            'Jam Masuk',
            'Jam Keluar',
            'Durasi Kerja',
            'Status',
            'Sumber',
            'Keterangan',
        ];
    }

    private int $rowNumber = 1;

    public function map($attendance): array
    {
        $duration = null;
        if ($attendance->check_in && $attendance->check_out) {
            $minutes  = Carbon::parse($attendance->check_in)->diffInMinutes(Carbon::parse($attendance->check_out));
            $hours    = intdiv($minutes, 60);
            $mins     = $minutes % 60;
            $duration = "{$hours}j {$mins}m";
        }

        return [
            $this->rowNumber++,
            $attendance->employee->employee_code ?? '-',
            $attendance->employee->name ?? '-',
            $attendance->employee->department ?? '-',
            Carbon::parse($attendance->date)->format('d/m/Y'),
            Carbon::parse($attendance->date)->locale('id')->dayName,
            $attendance->check_in  ? Carbon::parse($attendance->check_in)->format('H:i')  : '-',
            $attendance->check_out ? Carbon::parse($attendance->check_out)->format('H:i') : '-',
            $duration ?? '-',
            $this->translateStatus($attendance->status),
            $attendance->source === 'fingerprint' ? 'Fingerprint' : 'PWA GPS',
            $attendance->notes ?? '',
        ];
    }

    private function translateStatus(string $status): string
    {
        return match ($status) {
            'on_time' => 'Tepat Waktu',
            'late'    => 'Terlambat',
            'absent'  => 'Tidak Hadir',
            'leave'   => 'Cuti',
            default   => ucfirst($status),
        };
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A5F']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function title(): string
    {
        return 'Data Absensi';
    }
}