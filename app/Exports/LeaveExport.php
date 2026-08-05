<?php

namespace App\Exports;

use App\Models\LeaveRequest;
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

class LeaveExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(
        private ?string $startDate = null,
        private ?string $endDate   = null,
        private ?string $status    = null
    ) {}

    public function query()
    {
        return LeaveRequest::with(['employee', 'leaveType', 'reviewer'])
            ->when($this->startDate, fn($q) => $q->whereDate('start_date', '>=', $this->startDate))
            ->when($this->endDate,   fn($q) => $q->whereDate('end_date', '<=', $this->endDate))
            ->when($this->status,    fn($q) => $q->where('status', $this->status))
            ->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Karyawan',
            'Nama Karyawan',
            'Departemen',
            'Jenis Cuti',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Jumlah Hari',
            'Alasan',
            'Status',
            'Disetujui Oleh',
            'Tanggal Pengajuan',
        ];
    }

    private int $rowNumber = 1;

    public function map($leave): array
    {
        $days = Carbon::parse($leave->start_date)->diffInDays(Carbon::parse($leave->end_date)) + 1;

        return [
            $this->rowNumber++,
            $leave->employee->employee_code ?? '-',
            $leave->employee->name ?? '-',
            $leave->employee->department ?? '-',
            $leave->leaveType->name ?? '-',
            Carbon::parse($leave->start_date)->format('d/m/Y'),
            Carbon::parse($leave->end_date)->format('d/m/Y'),
            $days,
            $leave->reason ?? '-',
            $this->translateStatus($leave->status),
            $leave->reviewer->name ?? '-',
            Carbon::parse($leave->created_at)->format('d/m/Y H:i'),
        ];
    }

    private function translateStatus(string $status): string
    {
        return match ($status) {
            'pending'  => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default    => ucfirst($status),
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
        return 'Data Cuti & Izin';
    }
}