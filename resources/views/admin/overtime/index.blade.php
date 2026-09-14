@extends('layouts.admin')

@section('title', 'Kelola Lembur')
@section('page-title', 'Persetujuan & Validasi Lembur Karyawan')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h3 class="text-base font-bold text-gray-800 uppercase tracking-wider">Daftar Pengajuan Lembur</h3>
        <p class="text-xs text-gray-500 font-semibold mt-1">Review, setujui, atau tolak pengajuan lembur mandiri dari karyawan</p>
    </div>
</div>

{{-- Alert Messages --}}
@if (session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-xl mb-6 shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm font-semibold">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    </div>
@endif

@if ($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl mb-6 shadow-sm">
        <ul class="list-disc list-inside text-sm font-semibold space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Tab Navigation Status -->
<div class="flex border-b border-gray-200 mb-6 space-x-4">
    <a href="{{ route('admin.overtime.index', ['status' => 'pending']) }}" 
       class="pb-3 text-xs font-bold uppercase tracking-wider border-b-2 transition {{ $activeStatus === 'pending' ? 'border-[#0a2219] text-[#0a2219]' : 'border-transparent text-gray-400 hover:text-gray-600' }}">
        ⏳ Menunggu Approval
    </a>
    <a href="{{ route('admin.overtime.index', ['status' => 'approved']) }}" 
       class="pb-3 text-xs font-bold uppercase tracking-wider border-b-2 transition {{ $activeStatus === 'approved' ? 'border-[#0a2219] text-[#0a2219]' : 'border-transparent text-gray-400 hover:text-gray-600' }}">
        ✓ Disetujui (Approved)
    </a>
    <a href="{{ route('admin.overtime.index', ['status' => 'rejected']) }}" 
       class="pb-3 text-xs font-bold uppercase tracking-wider border-b-2 transition {{ $activeStatus === 'rejected' ? 'border-[#0a2219] text-[#0a2219]' : 'border-transparent text-gray-400 hover:text-gray-600' }}">
        ✗ Ditolak (Rejected)
    </a>
    <a href="{{ route('admin.overtime.index', ['status' => 'all']) }}" 
       class="pb-3 text-xs font-bold uppercase tracking-wider border-b-2 transition {{ $activeStatus === 'all' ? 'border-[#0a2219] text-[#0a2219]' : 'border-transparent text-gray-400 hover:text-gray-600' }}">
        Semua Pengajuan
    </a>
</div>

<!-- Table Container -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[900px] border-collapse">
            <thead>
                <tr class="bg-[#f0f4f2] border-b border-gray-100">
                    <th class="px-6 py-4 text-left text-xs font-bold text-[#0a2219] uppercase tracking-wider">Tanggal & Karyawan</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-[#0a2219] uppercase tracking-wider">Tipe Lembur</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-[#0a2219] uppercase tracking-wider">Jam & Pekerjaan</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-[#0a2219] uppercase tracking-wider">Estimasi Bayaran</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-[#0a2219] uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-[#0a2219] uppercase tracking-wider">Aksi HRD</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($overtimes as $overtime)
                    <tr class="hover:bg-[#fcfdfc] transition duration-150">
                        <!-- Date & Employee -->
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-9 h-9 bg-[#0a2219] text-[#d4af37] rounded-xl flex items-center justify-center font-extrabold text-sm shadow-sm border border-[#1d523e]">
                                    {{ substr($overtime->employee->name ?? 'K', 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800">{{ $overtime->employee->name ?? '-' }}</p>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                        {{ \Carbon\Carbon::parse($overtime->date)->translatedFormat('d M Y') }} • {{ $overtime->employee->division ?? 'Umum' }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        
                        <!-- Type -->
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold text-gray-700 capitalize block">
                                {{ str_replace('_', ' ', $overtime->type) }}
                            </span>
                            @if($overtime->kg_amount)
                                <span class="text-[10px] text-amber-700 font-extrabold bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                    {{ $overtime->kg_amount }} KG Ekspor
                                </span>
                            @endif
                        </td>
                        
                        <!-- Time & Reason -->
                        <td class="px-6 py-4">
                            <div class="text-xs font-extrabold text-gray-800">
                                {{ $overtime->start_time ? \Carbon\Carbon::parse($overtime->start_time)->format('H:i') : '-' }} - 
                                {{ $overtime->end_time ? \Carbon\Carbon::parse($overtime->end_time)->format('H:i') : '-' }}
                                ({{ $overtime->hours ?? 0 }} Jam)
                            </div>
                            <p class="text-[11px] text-gray-500 mt-1 max-w-xs truncate" title="{{ $overtime->reason }}">
                                {{ $overtime->reason ?? '-' }}
                            </p>
                        </td>

                        <!-- Nominal Amount -->
                        <td class="px-6 py-4 text-xs font-extrabold text-emerald-700">
                            {{ $overtime->overtime_amount > 0 ? 'Rp ' . number_format($overtime->overtime_amount, 0, ',', '.') : '-' }}
                        </td>
                        
                        <!-- Status Badge -->
                        <td class="px-6 py-4 text-center">
                            @if ($overtime->status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-1 bg-amber-500/10 text-amber-700 text-xs font-extrabold rounded-lg border border-amber-500/20 uppercase tracking-wider">
                                    Pending
                                </span>
                            @elseif ($overtime->status === 'approved')
                                <span class="inline-flex items-center px-2.5 py-1 bg-emerald-500/10 text-emerald-700 text-xs font-extrabold rounded-lg border border-emerald-500/20 uppercase tracking-wider">
                                    Disetujui
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 bg-red-500/10 text-red-700 text-xs font-extrabold rounded-lg border border-red-500/20 uppercase tracking-wider">
                                    Ditolak
                                </span>
                            @endif
                        </td>
                        
                        <!-- Actions -->
                        <td class="px-6 py-4 text-center">
                            @if ($overtime->status === 'pending')
                                <div class="flex items-center justify-center gap-2">
                                    <form action="{{ route('admin.overtime.approve', $overtime->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider transition shadow-sm" title="Setujui Lembur">
                                            ✓ Setujui
                                        </button>
                                    </form>

                                    <button type="button" onclick="openRejectModal({{ $overtime->id }}, '{{ $overtime->employee->name }}')" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider transition shadow-sm" title="Tolak Lembur">
                                        ✗ Tolak
                                    </button>
                                </div>
                            @else
                                <a href="{{ route('admin.overtime.edit', $overtime->id) }}" class="text-xs font-bold text-[#0a2219] hover:underline">
                                    Edit Detail
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 font-medium">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Tidak ada pengajuan lembur dengan status ini.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Reject -->
<div id="modal-reject" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-gray-100">
        <div class="flex items-center justify-between pb-4 border-b">
            <h3 class="text-base font-extrabold text-gray-800">Tolak Pengajuan Lembur</h3>
            <button onclick="toggleModal('modal-reject', false)" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="form-reject-overtime" method="POST" action="" class="mt-4 space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Alasan Penolakan HRD *</label>
                <textarea name="rejection_reason" rows="3" placeholder="Masukkan catatan alasan penolakan..." required class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-3">
                <button type="button" onclick="toggleModal('modal-reject', false)" class="px-4 py-2 text-xs font-bold text-gray-500 uppercase tracking-wider hover:bg-gray-100 rounded-xl">Batal</button>
                <button type="submit" class="px-5 py-2 bg-red-600 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow hover:bg-red-700">Konfirmasi Tolak</button>
            </div>
        </form>
    </div>
</div>

<div class="mt-6">
    {{ $overtimes->links() }}
</div>
@endsection

@section('js')
<script>
    function toggleModal(id, show) {
        const modal = document.getElementById(id);
        if (modal) {
            if (show) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            } else {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }
    }

    function openRejectModal(id, name) {
        const form = document.getElementById('form-reject-overtime');
        form.action = `{{ url('/admin/overtime') }}/${id}/reject`;
        toggleModal('modal-reject', true);
    }
</script>
@endsection
