@auth
    @if(auth()->user()->isEmployee() && auth()->user()->employee)
        @php
            $todayAttendance = \App\Models\Attendance::where('employee_id', auth()->user()->employee->id)
                ->whereDate('date', \Carbon\Carbon::today())
                ->first();
            
            $now = \Carbon\Carbon::now('Asia/Jakarta');
            $showCheckInWarning = !$todayAttendance;
            $showCheckOutWarning = $todayAttendance && !$todayAttendance->check_out && ($now->hour > 15 || ($now->hour == 15 && $now->minute >= 45));
        @endphp

        @if($showCheckInWarning)
            <div class="mb-5 p-4 bg-gradient-to-r from-amber-500 via-amber-600 to-red-600 text-white rounded-2xl shadow-lg flex items-center justify-between border border-amber-400/30">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center flex-shrink-0 shadow-inner">
                        <svg class="w-6 h-6 text-amber-100 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-xs tracking-wider uppercase text-amber-100 leading-tight">Jaring Pengaman Absen</h4>
                        <p class="text-xs font-bold text-white leading-tight">Anda belum melakukan presensi masuk hari ini!</p>
                    </div>
                </div>
                <a href="{{ route('employee.attendance.index') }}" class="px-3.5 py-2 bg-white text-red-700 font-extrabold text-xs rounded-xl shadow hover:bg-amber-50 transition transform active:scale-95 flex-shrink-0">
                    Absen &rarr;
                </a>
            </div>
        @elseif($showCheckOutWarning)
            <div class="mb-5 p-4 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 text-white rounded-2xl shadow-lg flex items-center justify-between border border-blue-400/30">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center flex-shrink-0 shadow-inner">
                        <svg class="w-6 h-6 text-blue-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-xs tracking-wider uppercase text-blue-100 leading-tight">Pengingat Jam Pulang</h4>
                        <p class="text-xs font-bold text-white leading-tight">Sudah jam pulang! Lakukan absen keluar sebelum pulang.</p>
                    </div>
                </div>
                <a href="{{ route('employee.attendance.index') }}" class="px-3.5 py-2 bg-white text-indigo-700 font-extrabold text-xs rounded-xl shadow hover:bg-blue-50 transition transform active:scale-95 flex-shrink-0">
                    Absen Keluar &rarr;
                </a>
            </div>
        @endif
    @endif
@endauth
