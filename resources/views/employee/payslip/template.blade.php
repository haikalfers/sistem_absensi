<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Slip Gaji - {{ $detail->employee->name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            color: #1a1a2e;
            background: #ffffff;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .page {
            padding: 8px;
        }

        /* ===== HEADER ===== */
        .header {
            background: #0a2219;
            color: #ffffff;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 14px;
            width: 100%;
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
            font-size: 15px;
            font-weight: bold;
            color: #d4af37;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .company-subtitle {
            font-size: 7.5px;
            color: #a0b8a4;
            margin-top: 2px;
        }

        .doc-title {
            font-size: 11px;
            font-weight: bold;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: right;
            padding-right: 8px;
        }

        .doc-period {
            font-size: 8.5px;
            color: #d4af37;
            margin-top: 3px;
            text-align: right;
            padding-right: 8px;
        }

        .header-divider {
            border: none;
            border-top: 1px solid #1e4030;
            margin: 10px 0 8px 0;
        }

        .header-info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-info-table td {
            padding: 0;
            vertical-align: top;
            width: 50%;
        }

        .info-label {
            font-size: 7px;
            color: #6b9e7e;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 9px;
            font-weight: bold;
            color: #ffffff;
            margin-top: 1px;
        }

        /* ===== EMPLOYEE CARD ===== */
        .employee-card {
            background: #f4f8f6;
            border: 1px solid #d0e4d8;
            border-radius: 5px;
            padding: 12px 16px;
            margin-bottom: 12px;
            width: 100%;
        }

        .employee-card-title {
            font-size: 7px;
            font-weight: bold;
            color: #4a7a5e;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 7px;
            border-bottom: 1px solid #d0e4d8;
            padding-bottom: 5px;
        }

        .employee-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .employee-grid td {
            width: 50%;
            vertical-align: top;
            padding: 0;
        }

        .emp-field {
            margin-bottom: 6px;
        }

        .emp-label {
            font-size: 7px;
            color: #7a9e8e;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .emp-value {
            font-size: 10px;
            font-weight: bold;
            color: #0a2219;
        }

        /* ===== ATTENDANCE SUMMARY ===== */
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #d0e4d8;
            margin-bottom: 12px;
            table-layout: fixed;
        }

        .attendance-table td {
            width: 33.33%;
            padding: 9px 8px;
            text-align: center;
            border-right: 1px solid #d0e4d8;
            vertical-align: middle;
        }

        .attendance-table td:last-child {
            border-right: none;
        }

        .att-number {
            font-size: 18px;
            font-weight: bold;
            color: #0a2219;
            display: block;
        }

        .att-number.late {
            color: #d97706;
        }

        .att-number.absent {
            color: #c0392b;
        }

        .att-label {
            font-size: 7px;
            color: #7a9e8e;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-top: 2px;
        }

        /* ===== SECTION HEADER ===== */
        .section-header {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 6px 10px;
            border-radius: 4px 4px 0 0;
        }

        .section-header.earnings {
            background: #0a2219;
            color: #d4af37;
        }

        .section-header.deductions {
            background: #7f1d1d;
            color: #fca5a5;
        }

        /* ===== SALARY TABLE ===== */
        .table-wrapper {
            border: 1px solid #d0e4d8;
            border-top: none;
            margin-bottom: 12px;
        }

        .table-wrapper.deduct {
            border-color: #f5b8b8;
        }

        .salary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .salary-table td.amount {
            padding-right: 12px !important;
        }

        .salary-table tr.item td {
            padding: 5.5px 10px;
            border-bottom: 1px solid #f0f4f2;
            font-size: 9px;
            color: #374151;
        }

        .salary-table tr.item:last-child td {
            border-bottom: none;
        }

        .salary-table tr.item td.amount {
            text-align: right;
            font-weight: bold;
            color: #1a3a2a;
            width: 40%;
        }

        .salary-table tr.item td.amount.deduct {
            color: #c0392b;
        }

        .salary-table tr.subtotal td {
            padding: 6.5px 10px;
            font-size: 9.5px;
            font-weight: bold;
            background: #f0f4f2;
            border-top: 1.5px solid #c8dcd2;
        }

        .salary-table tr.subtotal td.amount {
            text-align: right;
            color: #0a2219;
            width: 40%;
        }

        .salary-table tr.subtotal.deduct-total td {
            background: #fff1f2;
            border-top: 1.5px solid #f5b8b8;
        }

        .salary-table tr.subtotal.deduct-total td.amount {
            color: #b91c1c;
        }

        /* ===== NET SALARY BOX ===== */
        .net-box {
            background: #0a2219;
            border-radius: 6px;
            padding: 12px;
            width: 100%;
            margin-bottom: 14px;
        }

        .net-box-table {
            width: 100%;
            table-layout: fixed;
        }

        .net-box-table td:first-child {
            width: 60%;
        }

        .net-box-table td:last-child {
            width: 40%;
        }

        .net-box-table td {
            padding: 0;
            vertical-align: middle;
        }

        .net-label {
            font-size: 8px;
            font-weight: bold;
            color: #6b9e7e;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            display: block;
        }

        .net-sublabel {
            font-size: 7px;
            color: #4a6e54;
            margin-top: 1px;
            display: block;
        }

        .net-amount {
            font-size: 18px;
            font-weight: bold;
            color: #d4af37;
            text-align: right;
            padding-right: 8px;
            white-space: nowrap;
        }

        /* ===== SIGNATURE ===== */
        .signature-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .signature-table td {
            width: 50%;
            padding: 0;
            vertical-align: top;
        }

        .sig-box {
            border: 1px dashed #c8dcd2;
            border-radius: 5px;
            padding: 9px 9px 5px;
            text-align: center;
        }

        .sig-title {
            font-size: 7.5px;
            color: #4a7a5e;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 45px;
        }

        .sig-line {
            border-top: 1px solid #9ca3af;
            margin-bottom: 3px;
        }

        .sig-name {
            font-size: 8px;
            font-weight: bold;
            color: #0a2219;
        }

        .sig-pos {
            font-size: 7px;
            color: #6b7280;
        }

        /* ===== CONFIDENTIAL & FOOTER ===== */
        .confidential {
            text-align: center;
            margin-bottom: 8px;
        }

        .confidential-badge {
            border: 1.5px solid #e5e7eb;
            border-radius: 20px;
            padding: 2px 12px;
            font-size: 7px;
            color: #9ca3af;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            font-size: 7px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
            line-height: 1.7;
        }

        .footer strong {
            color: #6b7280;
        }
    </style>
</head>

<body>
    <div class="page">

        {{-- ===== HEADER ===== --}}
        <div class="header">
            <table class="header-table">
                <tr>
                    <td style="width:70%;">
                        <div class="company-name">PT. Triliun Anugrah Nusantara</div>
                        <div class="company-subtitle">Human Resources &amp; Payroll Department</div>
                    </td>
                    <td style="width:30%;">
                        <div class="doc-title">Slip Gaji</div>
                        <div class="doc-period">{{ $detail->payroll->period_name }}</div>
                    </td>
                </tr>
            </table>
            <hr class="header-divider">
            <table class="header-info-table">
                <tr>
                    <td>
                        <div class="info-label">Tanggal Diterbitkan</div>
                        <div class="info-value">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
                    </td>
                    <td>
                        <div class="info-label">Periode</div>
                        <div class="info-value">
                            {{ \Carbon\Carbon::parse($detail->payroll->period_start)->translatedFormat('d M Y') }}
                            &ndash;
                            {{ \Carbon\Carbon::parse($detail->payroll->period_end)->translatedFormat('d M Y') }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- ===== EMPLOYEE INFO ===== --}}
        <div class="employee-card">
            <div class="employee-card-title">Informasi Karyawan</div>
            <table class="employee-grid">
                <tr>
                    <td>
                        <div class="emp-field">
                            <div class="emp-label">Nama Lengkap</div>
                            <div class="emp-value">{{ $detail->employee->name }}</div>
                        </div>
                        <div class="emp-field">
                            <div class="emp-label">Kode Karyawan</div>
                            <div class="emp-value">{{ $detail->employee->employee_code }}</div>
                        </div>
                        <div class="emp-field">
                            <div class="emp-label">Jabatan</div>
                            <div class="emp-value">{{ $detail->employee->position ?? '-' }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="emp-field">
                            <div class="emp-label">Divisi</div>
                            <div class="emp-value">{{ $detail->employee->division ?? '-' }}</div>
                        </div>
                        <div class="emp-field">
                            <div class="emp-label">Departemen</div>
                            <div class="emp-value">{{ $detail->employee->department ?? '-' }}</div>
                        </div>
                        <div class="emp-field">
                            <div class="emp-label">Status Payroll</div>
                            <div class="emp-value">{{ strtoupper($detail->payroll->status ?? 'Approved') }}</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- ===== ATTENDANCE ===== --}}
        <table class="attendance-table">
            <tr>
                <td>
                    <span class="att-number">{{ $detail->attendance_days ?? 0 }}</span>
                    <span class="att-label">Hari Hadir</span>
                </td>
                <td>
                    <span class="att-number late">{{ $detail->late_count ?? 0 }}</span>
                    <span class="att-label">Terlambat</span>
                </td>
                <td>
                    <span class="att-number absent">{{ $detail->absent_count ?? 0 }}</span>
                    <span class="att-label">Tidak Hadir</span>
                </td>
            </tr>
        </table>

        {{-- ===== EARNINGS ===== --}}
        @php
            $grossSalary =
                ($detail->base_salary ?? 0) +
                ($detail->meal_allowance ?? 0) +
                ($detail->overtime_total ?? 0) +
                ($detail->kpi_bonus ?? 0);
            $totalDeductions =
                ($detail->bpjs_tk_deduction ?? 0) +
                ($detail->bpjs_kes_deduction ?? 0) +
                ($detail->pph21_deduction ?? 0) +
                ($detail->other_deduction ?? 0);
        @endphp

        <div class="section-header earnings">&#9650; Penerimaan (Earnings)</div>
        <div class="table-wrapper">
            <table class="salary-table">
                <tr class="item">
                    <td>Gaji Pokok</td>
                    <td class="amount">Rp {{ number_format($detail->base_salary ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr class="item">
                    <td>Tunjangan Uang Makan</td>
                    <td class="amount">Rp {{ number_format($detail->meal_allowance ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr class="item">
                    <td>Overtime / Lembur</td>
                    <td class="amount">Rp {{ number_format($detail->overtime_total ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr class="item">
                    <td>Bonus KPI</td>
                    <td class="amount">Rp {{ number_format($detail->kpi_bonus ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr class="subtotal">
                    <td>Total Penghasilan Bruto</td>
                    <td class="amount">Rp {{ number_format($grossSalary, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        {{-- ===== DEDUCTIONS ===== --}}
        <div class="section-header deductions">&#9660; Potongan (Deductions)</div>
        <div class="table-wrapper deduct">
            <table class="salary-table">
                <tr class="item">
                    <td>BPJS Ketenagakerjaan (2%)</td>
                    <td class="amount deduct">- Rp {{ number_format($detail->bpjs_tk_deduction ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
                <tr class="item">
                    <td>BPJS Kesehatan (1%)</td>
                    <td class="amount deduct">- Rp {{ number_format($detail->bpjs_kes_deduction ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
                <tr class="item">
                    <td>Pajak Penghasilan (PPh 21)</td>
                    <td class="amount deduct">- Rp {{ number_format($detail->pph21_deduction ?? 0, 0, ',', '.') }}</td>
                </tr>
                @if (($detail->other_deduction ?? 0) > 0)
                    <tr class="item">
                        <td>Potongan Lainnya</td>
                        <td class="amount deduct">- Rp {{ number_format($detail->other_deduction, 0, ',', '.') }}</td>
                    </tr>
                @endif
                <tr class="subtotal deduct-total">
                    <td>Total Potongan</td>
                    <td class="amount">- Rp {{ number_format($totalDeductions, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        {{-- ===== NET SALARY ===== --}}
        <div class="net-box">
            <table class="net-box-table">
                <tr>
                    <td>
                        <span class="net-label">Gaji Bersih Diterima</span>
                        <span class="net-sublabel">Take Home Pay (THP)</span>
                    </td>
                    <td>
                        <div class="net-amount">Rp {{ number_format($detail->net_salary ?? 0, 0, ',', '.') }}</div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- ===== SIGNATURE ===== --}}
        <table class="signature-table">
            <tr>
                <td>
                    <div class="sig-box">
                        <div class="sig-title">Disetujui Oleh</div>
                        <div class="sig-line"></div>
                        <div class="sig-name">HRD / Payroll Manager</div>
                        <div class="sig-pos">PT. Triliun Anugrah Nusantara</div>
                    </div>
                </td>
                <td>
                    <div class="sig-box">
                        <div class="sig-title">Penerima</div>
                        <div class="sig-line"></div>
                        <div class="sig-name">{{ $detail->employee->name }}</div>
                        <div class="sig-pos">{{ $detail->employee->position ?? 'Karyawan' }}</div>
                    </div>
                </td>
            </tr>
        </table>

        {{-- ===== CONFIDENTIAL ===== --}}
        <div class="confidential">
            <span class="confidential-badge">CONFIDENTIAL</span>
        </div>

        {{-- ===== FOOTER ===== --}}
        <div class="footer">
            <strong>PT. Triliun Anugrah Nusantara</strong> &mdash; Dokumen ini diterbitkan secara otomatis oleh Sistem Absensi
            Karyawan.<br>
            Slip gaji ini sah sebagai bukti pembayaran resmi periode
            <strong>{{ $detail->payroll->period_name }}</strong>.<br>
            Apabila terdapat pertanyaan, silakan hubungi departemen HRD/Payroll.
        </div>

    </div>
</body>

</html>
