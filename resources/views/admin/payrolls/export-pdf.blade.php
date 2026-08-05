<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Payroll - {{ $payroll->period_name }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 8px;
            color: #1a1a2e;
            background: #ffffff;
            line-height: 1.4;
            width: 100%;
        }

        .page {
            width: auto;
            padding: 0;
        }

        /* ===== HEADER ===== */
        .header {
            background: #0a2219;
            color: #ffffff;
            padding: 12px 18px;
            border-radius: 5px;
            margin-bottom: 12px;
            width: auto;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: middle;
            padding: 0;
        }

        .company-name {
            font-size: 14px;
            font-weight: bold;
            color: #d4af37;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .company-subtitle {
            font-size: 7px;
            color: #a0b8a4;
            margin-top: 1px;
        }

        .doc-title {
            font-size: 11px;
            font-weight: bold;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-align: right;
        }

        .doc-period {
            font-size: 8px;
            color: #d4af37;
            margin-top: 2px;
            text-align: right;
        }

        .doc-meta {
            font-size: 7px;
            color: #6b9e7e;
            text-align: right;
            margin-top: 1px;
        }

        /* ===== SUMMARY CARDS ===== */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .summary-table td {
            width: 25%;
            vertical-align: top;
            padding: 9px 11px;
            border-radius: 5px;
        }

        .card-default {
            background: #f4f8f6;
            border: 1px solid #d0e4d8;
        }

        .card-gold {
            background: #fffbeb;
            border: 1px solid #fde68a;
        }

        .card-red {
            background: #fff5f5;
            border: 1px solid #fca5a5;
        }

        .card-dark {
            background: #0a2219;
            border: 1px solid #0a2219;
        }

        .sum-label {
            font-size: 6.5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
            color: #6b9e7e;
        }

        .sum-label-light { color: #6b9e7e; }
        .sum-label-gold  { color: #92400e; }
        .sum-label-red   { color: #991b1b; }

        .sum-value {
            font-size: 9.5px;
            font-weight: bold;
        }

        .sum-value-default { color: #0a2219; }
        .sum-value-gold    { color: #b45309; }
        .sum-value-red     { color: #b91c1c; }
        .sum-value-white   { color: #d4af37; }

        /* ===== INFO ROW ===== */
        .info-outer {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .info-outer td {
            width: 50%;
            vertical-align: top;
        }

        .info-box {
            background: #f4f8f6;
            border: 1px solid #d0e4d8;
            border-radius: 5px;
            padding: 9px 12px;
        }

        .info-box-title {
            font-size: 6.5px;
            font-weight: bold;
            color: #4a7a5e;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
            border-bottom: 1px solid #d0e4d8;
            padding-bottom: 4px;
        }

        .info-inner {
            width: 100%;
            border-collapse: collapse;
        }

        .info-inner td {
            padding: 2px 0;
            vertical-align: top;
        }

        .info-key {
            width: 45%;
            font-size: 7px;
            color: #6b7280;
        }

        .info-val {
            font-size: 7.5px;
            font-weight: bold;
            color: #0a2219;
        }

        .status-badge {
            border-radius: 8px;
            padding: 1px 7px;
            font-size: 6.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .status-finalized { background: #d1fae5; color: #065f46; }
        .status-approved  { background: #dbeafe; color: #1e40af; }
        .status-draft     { background: #f3f4f6; color: #6b7280; }

        /* ===== MAIN TABLE ===== */
        .section-title {
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #d4af37;
            background: #0a2219;
            padding: 6px 10px;
            border-radius: 4px 4px 0 0;
        }

        .table-wrapper {
            border: 1px solid #c8dcd2;
            border-top: none;
            border-radius: 0 0 5px 5px;
            overflow: hidden;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
        }

        .main-table thead tr {
            background: #0a2219;
            color: #ffffff;
        }

        .main-table th {
            padding: 7px 6px;
            border: none;
            text-align: left;
            font-weight: bold;
            font-size: 7px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #d4af37;
        }

        .main-table tbody tr td {
            padding: 6px 6px;
            border: none;
            vertical-align: middle;
            line-height: 1.3;
        }

        .main-table tbody tr:nth-child(even) td {
            background: #f9fbfa;
        }

        .tr { text-align: right; }
        .tc { text-align: center; }
        .bold { font-weight: bold; }
        .green { color: #16653a; font-weight: bold; }
        .red   { color: #c0392b; }
        .gold  { color: #b45309; font-weight: bold; }

        .name-cell {
            font-weight: bold;
            color: #0a2219;
            font-size: 7.5px;
        }

        .code-badge {
            background: #e7f0ec;
            color: #4a7a5e;
            border-radius: 3px;
            padding: 1px 4px;
            font-size: 6.5px;
            font-weight: bold;
        }

        /* ===== TOTALS ROW ===== */
        .totals-row td {
            background: #0a2219 !important;
            color: #ffffff !important;
            font-weight: bold !important;
            font-size: 7.5px !important;
            padding: 7px 6px !important;
            border: none !important;
        }

        /* ===== FOOTER ===== */
        .footer {
            text-align: center;
            font-size: 7px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
            line-height: 1.7;
            margin-top: 8px;
        }

        .footer strong { color: #6b7280; }
    </style>
</head>
<body>
<div class="page">

    {{-- ===== HEADER ===== --}}
    <div class="header">
        <table class="header-table">
            <tr>
                <td style="width:60%;">
                    <div class="company-name">PT. Triliun Anugrah Nusantara</div>
                    <div class="company-subtitle">Human Resources &amp; Payroll Department</div>
                </td>
                <td style="width:40%;">
                    <div class="doc-title">Laporan Payroll</div>
                    <div class="doc-period">{{ $payroll->period_name }}</div>
                    <div class="doc-meta">Diterbitkan: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ===== SUMMARY CARDS ===== --}}
    @php
        $totalGross = $payroll->details->sum(function($d) {
            return ($d->base_salary ?? 0) + ($d->meal_allowance ?? 0) + ($d->overtime_total ?? 0) + ($d->kpi_bonus ?? 0);
        });
        $totalDeductions = $payroll->details->sum(function($d) {
            return ($d->bpjs_tk_deduction ?? 0) + ($d->bpjs_kes_deduction ?? 0) + ($d->pph21_deduction ?? 0) + ($d->other_deduction ?? 0);
        });
        $totalNet = $payroll->details->sum('net_salary');
        $totalEmployees = $payroll->details->count();
    @endphp

    <table class="summary-table">
        <tr>
            <td class="card-default">
                <div class="sum-label sum-label-light">Total Karyawan</div>
                <div class="sum-value sum-value-default">{{ $totalEmployees }} Orang</div>
            </td>
            <td class="card-gold">
                <div class="sum-label sum-label-gold">Total Gaji Bruto</div>
                <div class="sum-value sum-value-gold">Rp {{ number_format($totalGross, 0, ',', '.') }}</div>
            </td>
            <td class="card-red">
                <div class="sum-label sum-label-red">Total Potongan</div>
                <div class="sum-value sum-value-red">Rp {{ number_format($totalDeductions, 0, ',', '.') }}</div>
            </td>
            <td class="card-dark">
                <div class="sum-label sum-label-light">Total Gaji Bersih (THP)</div>
                <div class="sum-value sum-value-white">Rp {{ number_format($totalNet, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    {{-- ===== INFO ROW ===== --}}
    <table class="info-outer">
        <tr>
            <td style="padding-right: 6px;">
                <div class="info-box">
                    <div class="info-box-title">Detail Periode Payroll</div>
                    <table class="info-inner">
                        <tr>
                            <td class="info-key">Nama Periode</td>
                            <td class="info-val">{{ $payroll->period_name }}</td>
                        </tr>
                        <tr>
                            <td class="info-key">Tanggal Mulai</td>
                            <td class="info-val">{{ \Carbon\Carbon::parse($payroll->period_start)->translatedFormat('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="info-key">Tanggal Selesai</td>
                            <td class="info-val">{{ \Carbon\Carbon::parse($payroll->period_end)->translatedFormat('d F Y') }}</td>
                        </tr>
                    </table>
                </div>
            </td>
            <td style="padding-left: 6px;">
                <div class="info-box">
                    <div class="info-box-title">Status &amp; Informasi</div>
                    <table class="info-inner">
                        <tr>
                            <td class="info-key">Status</td>
                            <td class="info-val">
                                @php $status = $payroll->status ?? 'draft'; @endphp
                                <span class="status-badge status-{{ $status }}">{{ strtoupper($status) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="info-key">Dibuat Oleh</td>
                            <td class="info-val">{{ $payroll->createdBy->name ?? 'Administrator' }}</td>
                        </tr>
                        <tr>
                            <td class="info-key">Tanggal Export</td>
                            <td class="info-val">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    {{-- ===== MAIN TABLE ===== --}}
    <div class="section-title">Rincian Gaji Seluruh Karyawan</div>
    <div class="table-wrapper">
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width:3%">#</th>
                    <th style="width:14%">Karyawan</th>
                    <th style="width:8%">Jabatan</th>
                    <th class="tr" style="width:10%">Gaji Pokok</th>
                    <th class="tr" style="width:9%">Tunjangan Makan</th>
                    <th class="tr" style="width:8%">Lembur</th>
                    <th class="tr" style="width:7%">Bonus KPI</th>
                    <th class="tr" style="width:10%">Total Bruto</th>
                    <th class="tr" style="width:10%">Total Potongan</th>
                    <th class="tr" style="width:10%">Gaji Bersih</th>
                    <th class="tc" style="width:4%">Hadir</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payroll->details as $i => $detail)
                @php
                    $gross  = ($detail->base_salary ?? 0) + ($detail->meal_allowance ?? 0) + ($detail->overtime_total ?? 0) + ($detail->kpi_bonus ?? 0);
                    $deduct = ($detail->bpjs_tk_deduction ?? 0) + ($detail->bpjs_kes_deduction ?? 0) + ($detail->pph21_deduction ?? 0) + ($detail->other_deduction ?? 0);
                @endphp
                <tr>
                    <td class="tc">{{ $i + 1 }}</td>
                    <td>
                        <div class="name-cell">{{ $detail->employee->name }}</div>
                        <span class="code-badge">{{ $detail->employee->employee_code }}</span>
                    </td>
                    <td>{{ $detail->employee->position ?? '-' }}</td>
                    <td class="tr">Rp {{ number_format($detail->base_salary ?? 0, 0, ',', '.') }}</td>
                    <td class="tr">Rp {{ number_format($detail->meal_allowance ?? 0, 0, ',', '.') }}</td>
                    <td class="tr">Rp {{ number_format($detail->overtime_total ?? 0, 0, ',', '.') }}</td>
                    <td class="tr">Rp {{ number_format($detail->kpi_bonus ?? 0, 0, ',', '.') }}</td>
                    <td class="tr gold">Rp {{ number_format($gross, 0, ',', '.') }}</td>
                    <td class="tr red">- Rp {{ number_format($deduct, 0, ',', '.') }}</td>
                    <td class="tr green">Rp {{ number_format($detail->net_salary ?? 0, 0, ',', '.') }}</td>
                    <td class="tc">{{ $detail->attendance_days ?? 0 }} hr</td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="tc" style="padding: 16px; color: #9ca3af;">
                        Tidak ada data karyawan untuk periode ini.
                    </td>
                </tr>
                @endforelse

                @if($payroll->details->count() > 0)
                <tr class="totals-row">
                    <td colspan="3" style="color:#d4af37 !important;">TOTAL KESELURUHAN</td>
                    <td class="tr">Rp {{ number_format($payroll->details->sum('base_salary'), 0, ',', '.') }}</td>
                    <td class="tr">Rp {{ number_format($payroll->details->sum('meal_allowance'), 0, ',', '.') }}</td>
                    <td class="tr">Rp {{ number_format($payroll->details->sum('overtime_total'), 0, ',', '.') }}</td>
                    <td class="tr">Rp {{ number_format($payroll->details->sum('kpi_bonus'), 0, ',', '.') }}</td>
                    <td class="tr" style="color:#d4af37 !important;">Rp {{ number_format($totalGross, 0, ',', '.') }}</td>
                    <td class="tr" style="color:#fca5a5 !important;">Rp {{ number_format($totalDeductions, 0, ',', '.') }}</td>
                    <td class="tr" style="color:#d4af37 !important;">Rp {{ number_format($totalNet, 0, ',', '.') }}</td>
                    <td class="tc">{{ $payroll->details->sum('attendance_days') }} hr</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    {{-- ===== FOOTER ===== --}}
    <div class="footer">
        <strong>PT. Triliun Anugrah Nusantara</strong> &mdash; Dokumen ini diterbitkan secara otomatis oleh Sistem Absensi Karyawan.<br>
        Laporan payroll ini bersifat <strong>RAHASIA</strong> dan hanya untuk keperluan internal perusahaan. &nbsp;|&nbsp;
        Periode: <strong>{{ $payroll->period_name }}</strong> &nbsp;|&nbsp;
        Total: <strong>{{ $totalEmployees }}</strong> karyawan
    </div>

</div>
</body>
</html>