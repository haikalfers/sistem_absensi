@extends('layouts.employee')

@section('title', 'Pengajuan Lembur')

@section('content')
    <div class="mb-5 flex items-center justify-between border-b border-gray-100 pb-4">
        <div>
            <h2 class="text-base font-extrabold text-[#0a2219] uppercase tracking-wider">Pengajuan Lembur</h2>
            <p class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider">Kelola & pantau status pengajuan lembur Anda</p>
        </div>
        <a href="{{ route('employee.overtime.create') }}" 
           class="bg-[#0a2219] hover:bg-[#123b2c] text-[#d4af37] px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition shadow-sm border border-[#1d523e]">
            + Ajukan Lembur
        </a>
    </div>

    @if ($overtimes->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center">
            <div class="w-12 h-12 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-3 text-xl font-bold">⏰</div>
            <h3 class="text-sm font-bold text-gray-700">Belum Ada Pengajuan Lembur</h3>
            <p class="text-xs text-gray-400 mt-1 max-w-xs mx-auto">Anda belum pernah membuat pengajuan lembur. Klik tombol di atas untuk mengajukan lembur baru.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($overtimes as $ot)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs font-extrabold text-gray-800">
                                🗓 {{ \Carbon\Carbon::parse($ot->date)->translatedFormat('l, d F Y') }}
                            </span>
                            <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-0.5">
                                Tipe: {{ match($ot->type) {
                                    'office' => 'Lembur Kantor (Max 1 jam)',
                                    'admin_production' => 'Admin Produksi (Max 2 jam)',
                                    'production_aka' => 'Produksi AKA (Max 3 jam)',
                                    'production_export' => 'Produksi Ekspor (Bonus /kg)',
                                    default => $ot->type
                                } }}
                            </span>
                        </div>

                        {{-- Status Badge --}}
                        @if ($ot->status === 'pending')
                            <span class="px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wider rounded-lg bg-amber-50 text-amber-700 border border-amber-200">
                                ⏳ Menunggu HRD
                            </span>
                        @elseif ($ot->status === 'approved')
                            <span class="px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wider rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                                ✓ Disetujui HRD
                            </span>
                        @else
                            <span class="px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wider rounded-lg bg-red-50 text-red-700 border border-red-200">
                                ✗ Ditolak HRD
                            </span>
                        @endif
                    </div>

                    <div class="bg-gray-50 rounded-xl p-3 grid grid-cols-2 gap-3 text-xs">
                        <div>
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider block">Jam Lembur</span>
                            <span class="font-extrabold text-gray-700">
                                {{ \Carbon\Carbon::parse($ot->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($ot->end_time)->format('H:i') }}
                                ({{ $ot->hours }} Jam)
                            </span>
                        </div>
                        <div>
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider block">Nominal Lembur</span>
                            <span class="font-extrabold text-emerald-700">
                                {{ $ot->overtime_amount > 0 ? 'Rp ' . number_format($ot->overtime_amount, 0, ',', '.') : '-' }}
                            </span>
                        </div>
                    </div>

                    @if ($ot->reason)
                        <div class="text-xs text-gray-600 border-t border-gray-100 pt-2">
                            <span class="font-bold text-gray-500">Alasan Pekerjaan:</span> {{ $ot->reason }}
                        </div>
                    @endif

                    @if ($ot->status === 'rejected' && $ot->rejection_reason)
                        <div class="bg-red-50 border border-red-100 p-2.5 rounded-xl text-xs text-red-700 font-semibold">
                            ⚠️ Catatan HRD: {{ $ot->rejection_reason }}
                        </div>
                    @endif

                    @if ($ot->status === 'pending')
                        <div class="border-t border-gray-100 pt-3 text-right">
                            <form action="{{ route('employee.overtime.destroy', $ot->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan lembur ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-bold text-red-600 hover:text-red-800 uppercase tracking-wider">
                                    🗑 Batalkan Pengajuan
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach

            <div class="mt-4">
                {{ $overtimes->links() }}
            </div>
        </div>
    @endif
@endsection
