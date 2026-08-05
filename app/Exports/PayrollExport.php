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
            'Gaji Pokok',
            'Tunjangan Makan',
            'Lembur',
            'Bonus KPI',
            'Gross Salary',
            'PPh 21',
            'BPJS TK (2%)',
            'BPJS Kes (1%)',
            'Potongan Lain',
            'Total Potongan',
            'Gaji Bersih (Net)',
            'Hari Hadir',
            'Status',
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
            $detail->base_salary,
            $detail->meal_allowance,
            $detail->overtime_amount,
            $detail->kpi_bonus,
            $detail->gross_salary,
            $detail->pph21_deduction,
            $detail->bpjs_tk_deduction,
            $detail->bpjs_kes_deduction,
            $detail->other_deduction,
            $detail->total_deduction,
            $detail->net_salary,
            $detail->working_days,
            ucfirst($detail->status ?? 'generated'),
        ];
    }

    public function columnFormats(): array
    {
        // Kolom F sampai P = angka mata uang IDR
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