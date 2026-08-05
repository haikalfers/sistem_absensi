@extends('layouts.admin')

@section('title', 'Pengajuan Presensi Ulang')
@section('page-title', 'Pengajuan Presensi Ulang')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-gray-800 uppercase tracking-wider">Daftar Pengajuan Presensi Ulang</h3>
            <p class="text-xs text-gray-500 font-semibold mt-1">Total: {{ $revisions->total() }} pengajuan terdaftar</p>
        </div>
        {{-- Pending Badge --}}
        @php $pendingCount = $revisions->getCollection()->where('status', 'pending')->count(); @endphp
        @if($pendingCount > 0)
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 border border-amber-200 text-amber-700 text-xs font-extrabold rounded-xl uppercase tracking-wider">
                <span class="w-2 h-2 bg-amber-400 rounded-full animate-pulse"></span>
                {{ $pendingCount }} menunggu review
            </span>
        @endif
    </div>

    {{-- Filter Form --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('admin.attendance-revisions.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Filter Status</label>
                    <select name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] bg-white transition">
                        <option value="">-- Semua Status --</option>
                        <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Filter Karyawan</label>
                    <select name="employee_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] bg-white transition">
                        <option value="">-- Semua Karyawan --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->name }} ({{ $emp->employee_code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-[#0a2219] hover:bg-[#123b2c] text-white px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition border border-transparent focus:ring-2 focus:ring-[#d4af37] shadow-sm">
                        Filter
                    </button>
                    @if(request()->hasAny(['status', 'employee_id']))
                        <a href="{{ route('admin.attendance-revisions.index') }}" class="px-4 py-3 border border-gray-300 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-50 transition">
                            Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] border-collapse">
                <thead>
                    <tr class="bg-[#f0f4f2] border-b border-gray-100">
                        <th class="px-6 py-4 text-left text-xs font-bold text-[#0a2219] uppercase tracking-wider">Karyawan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-[#0a2219] uppercase tracking-wider">Tgl Absensi</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-[#0a2219] uppercase tracking-wider">Jam Diminta</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-[#0a2219] uppercase tracking-wider">Alasan</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-[#0a2219] uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-[#0a2219] uppercase tracking-wider">Diajukan</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-[#0a2219] uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($revisions as $rev)
                        <tr class="hover:bg-[#fcfdfc] transition duration-150">
                            {{-- Karyawan --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 bg-[#e7f0ec] text-[#0a2219] rounded-xl flex items-center justify-center font-bold text-sm border border-[#d2dfd8] flex-shrink-0">
                                        {{ substr($rev->employee->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-800">{{ $rev->employee->name }}</p>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $rev->employee->employee_code }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Tanggal Absensi --}}
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-gray-800">
                                    {{ \Carbon\Carbon::parse($rev->revision_date)->translatedFormat('d M Y') }}
                                </span>
                                <p class="text-[10px] text-gray-400 mt-0.5">
                                    {{ \Carbon\Carbon::parse($rev->revision_date)->translatedFormat('l') }}
                                </p>
                            </td>

                            {{-- Jam Diminta --}}
                            <td class="px-6 py-4 text-center">
                                <div class="text-xs font-bold text-gray-700 space-y-0.5">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <span class="text-[9px] text-gray-400 uppercase tracking-wider w-12 text-right">Masuk</span>
                                        <span class="bg-gray-50 border border-gray-200 px-2 py-0.5 rounded-lg font-extrabold text-[#0a2219]">
                                            {{ $rev->requested_check_in ? \Carbon\Carbon::parse($rev->requested_check_in)->format('H:i') : '--:--' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-center gap-1.5">
                                        <span class="text-[9px] text-gray-400 uppercase tracking-wider w-12 text-right">Keluar</span>
                                        <span class="bg-gray-50 border border-gray-200 px-2 py-0.5 rounded-lg font-extrabold text-[#0a2219]">
                                            {{ $rev->requested_check_out ? \Carbon\Carbon::parse($rev->requested_check_out)->format('H:i') : '--:--' }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            {{-- Alasan --}}
                            <td class="px-6 py-4 max-w-xs">
                                <p class="text-xs text-gray-600 line-clamp-2 leading-relaxed">{{ $rev->reason }}</p>
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4 text-center">
                                @if($rev->status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-50 text-amber-700 text-[10px] font-extrabold rounded-lg border border-amber-200 uppercase tracking-wider">
                                        <span class="w-1.5 h-1.5 bg-amber-400 rounded-full animate-pulse"></span>
                                        Pending
                                    </span>
                                @elseif($rev->status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-extrabold rounded-lg border border-emerald-200 uppercase tracking-wider">
                                        Disetujui
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 bg-red-50 text-red-700 text-[10px] font-extrabold rounded-lg border border-red-200 uppercase tracking-wider">
                                        Ditolak
                                    </span>
                                @endif
                            </td>

                            {{-- Diajukan --}}
                            <td class="px-6 py-4 text-xs text-gray-500 font-medium">
                                {{ $rev->created_at->format('d M Y') }}
                                <p class="text-[10px] text-gray-400">{{ $rev->created_at->format('H:i') }}</p>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-3">
                                    <a href="{{ route('admin.attendance-revisions.show', $rev->id) }}"
                                       class="inline-flex items-center text-xs font-bold text-[#0a2219] hover:text-[#d4af37] transition" title="Lihat Detail">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Detail
                                    </a>

                                    @if($rev->isPending())
                                        <button type="button" onclick="approveRevision({{ $rev->id }})"
                                                class="inline-flex items-center text-xs font-bold text-emerald-600 hover:text-emerald-800 transition" title="Setujui">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Setujui
                                        </button>
                                        <button type="button" onclick="openRejectModal({{ $rev->id }})"
                                                class="inline-flex items-center text-xs font-bold text-red-500 hover:text-red-700 transition" title="Tolak">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Tolak
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500 font-medium">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 003.7 3.7 48.657 48.657 0 007.324 0 4.006 4.006 0 003.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3l-3 3" />
                                    </svg>
                                    <span>Tidak ada pengajuan presensi ulang.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $revisions->links() }}
    </div>

    {{-- Hidden Forms --}}
    <form id="approveForm" method="POST" style="display:none;">
        @csrf
        @method('PUT')
    </form>

    {{-- Reject Modal --}}
    <div id="rejectModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md border border-gray-200">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Tolak Pengajuan</h3>
                <p class="text-xs text-gray-500 mt-1">Berikan alasan penolakan yang jelas untuk karyawan.</p>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                @method('PUT')
                <div class="p-6">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">
                        Alasan Penolakan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="review_notes" id="rejectNotes" rows="4"
                              placeholder="Contoh: Data absensi fingerprint menunjukkan karyawan tidak hadir pada tanggal tersebut..."
                              class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-400 resize-none transition"
                              required minlength="5" maxlength="300"></textarea>
                </div>
                <div class="px-6 pb-6 flex gap-3">
                    <button type="button" onclick="closeRejectModal()"
                            class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-bold text-gray-600 hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-bold transition">
                        Tolak Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
<script>
    function approveRevision(id) {
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Setujui pengajuan presensi ulang ini? Data absensi akan diperbarui otomatis.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0a2219',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Setujui',
            cancelButtonText: 'Batal',
            borderRadius: '1rem'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('approveForm');
                form.action = `/admin/attendance-revisions/${id}/approve`;
                form.submit();
            }
        });
    }

    function openRejectModal(id) {
        const modal = document.getElementById('rejectModal');
        const form  = document.getElementById('rejectForm');
        form.action = `/admin/attendance-revisions/${id}/reject`;
        document.getElementById('rejectNotes').value = '';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeRejectModal() {
        const modal = document.getElementById('rejectModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Close modal on backdrop click
    document.getElementById('rejectModal').addEventListener('click', function(e) {
        if (e.target === this) closeRejectModal();
    });
</script>
@endsection
