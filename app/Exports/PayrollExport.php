<?php

namespace App\Exports;

use App\Models\PayrollDetail;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PayrollExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize, WithColumnFormatting
{
    public function __construct(private int $payrollId) {}

    public function query()
    {
        return PayrollDetail::with(['employee', 'payroll'])
            ->where('payroll_id', $this->payrollId)
            ->orderBy('employee_id');
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Karyawan',
            'Nama Karyawan',
            'Jabatan',
            'Departemen',
            // Pendapatan
            'Gaji Pokok',
            'BPJS JHTP (Perusahaan)',
            'BPJS JKK (Perusahaan)',
            'BPJS JKM (Perusahaan)',
            'BPJS JKN P (Perusahaan)',
            'JP Perusahaan',
            'Lembur',
            'Penghasilan Total',
            // Potongan
            'Pot. BPJS JHTP',
            'Pot. BPJS JHTTK',
            'Pot. BPJS JKK',
            'Pot. BPJS JKM',
            'Pot. BPJS JKN P',
            'Pot. JP Perusahaan',
            'Pot. JP Tenaga Kerja',
            'Pot. BPJS',
            'Pot. Pesantren',
            'Pot. Alpa',
            'Pot. Lain',
            'Jumlah Potongan',
            // Net & Hadir
            'Gaji Bersih (Net)',
            'Hari Hadir',
        ];
    }

    private int $rowNumber = 1;

    public function map($detail): array
    {
        return [
            $this->rowNumber++,
            $detail->employee->employee_code ?? '-',
            $detail->employee->name ?? '-',
            $detail->employee->position ?? '-',
            $detail->employee->department ?? '-',
            // Pendapatan
            $detail->base_salary,
            $detail->bpjs_jht_company,
            $detail->bpjs_jkk_income,
            $detail->bpjs_jkm_income,
            $detail->bpjs_jkn_company,
            $detail->jp_company_income,
            $detail->overtime_total,
            $detail->total_income,
            // Potongan
            $detail->bpjs_jht_company_deduct,
            $detail->bpjs_jht_employee,
            $detail->bpjs_jkk_deduct,
            $detail->bpjs_jkm_deduct,
            $detail->bpjs_jkn_company_deduct,
            $detail->jp_company_deduct,
            $detail->jp_employee,
            $detail->pot_bpjs,
            $detail->pot_pesantren,
            $detail->absent_deduction,
            $detail->other_deduction,
            $detail->total_deduction,
            // Net & Hadir
            $detail->net_salary,
            $detail->attendance_days,
        ];
    }

    public function columnFormats(): array
    {
        // Kolom F sampai Z = angka mata uang IDR
        $rupiahFormat = '#,##0';
        return [
            'F' => $rupiahFormat,
            'G' => $rupiahFormat,
            'H' => $rupiahFormat,
            'I' => $rupiahFormat,
            'J' => $rupiahFormat,
            'K' => $rupiahFormat,
            'L' => $rupiahFormat,
            'M' => $rupiahFormat,
            'N' => $rupiahFormat,
            'O' => $rupiahFormat,
            'P' => $rupiahFormat,
            'Q' => $rupiahFormat,
            'R' => $rupiahFormat,
            'S' => $rupiahFormat,
            'T' => $rupiahFormat,
            'U' => $rupiahFormat,
            'V' => $rupiahFormat,
            'W' => $rupiahFormat,
            'X' => $rupiahFormat,
            'Y' => $rupiahFormat,
            'Z' => $rupiahFormat,
        ];
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
        return 'Data Penggajian';
    }
}