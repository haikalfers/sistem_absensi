<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0a2219">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Triliun Anugrah Nusantara">
    <title>@yield('title', 'Employee Portal') - PT. Triliun Anugrah Nusantara</title>
    
    {{-- PWA Meta Tags --}}
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('css')
</head>
<body class="bg-[#f7f9f7] font-sans antialiased text-gray-800">
    <!-- Top Navigation Header -->
    <div class="bg-white border-b border-gray-100 sticky top-0 z-40 shadow-sm">
        <div class="p-4 flex items-center justify-between max-w-2xl mx-auto">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 bg-gradient-to-br from-[#f3e7c4] to-[#d4af37] rounded-lg flex items-center justify-center shadow border border-white/10 flex-shrink-0">
                    <svg class="w-5 h-5 text-[#0a2219]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l5.25 3.03M12 12.75l9-5.25M12 12.75l-9-5.25m9 5.25v9l9-5.25M12 21.75v-9" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-sm font-extrabold tracking-wide text-[#0a2219] uppercase leading-tight">Triliun Anugrah Nusantara</h1>
                    <p class="text-[9px] font-bold text-[#d4af37] tracking-widest uppercase leading-none">Employee Portal</p>
                </div>
            </div>
            
            <div class="flex items-center space-x-1">
                {{-- Install PWA Button --}}
                <button id="pwa-install-btn"
                    class="hidden p-2 text-[#d4af37] hover:bg-[#faf3e0] rounded-lg transition"
                    title="Install aplikasi">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </button>

                {{-- Notifikasi → Cuti & Izin (tempat user melihat status pending pengajuan) --}}
                <a href="{{ route('employee.leave-requests.index') }}" id="notifBtn" class="relative p-2 text-[#0a2219] hover:bg-gray-100 rounded-lg transition" title="Status Pengajuan Cuti">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span class="absolute top-1.5 right-1.5 w-1.5 h-1.5 bg-[#d4af37] rounded-full"></span>
                </a>
                
                <a href="{{ route('employee.profile.show') }}" class="p-2 text-[#0a2219] hover:bg-gray-100 rounded-lg transition" title="Profil Saya">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </a>
                
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Logout">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </button>
                </form>

            </div>
        </div>

        <!-- Mobile Menu Tabs (Tablet / Desktop fallback) -->
        <div class="border-t border-gray-100 hidden md:block">
            <div class="flex max-w-2xl mx-auto">
                <a href="{{ route('employee.dashboard') }}" 
                   class="flex-1 px-4 py-3 text-center text-xs font-bold uppercase tracking-wider whitespace-nowrap {{ request()->routeIs('employee.dashboard') ? 'text-[#0a2219] border-b-2 border-[#d4af37]' : 'text-gray-400 hover:text-[#0a2219]' }} transition">
                    Dashboard
                </a>
                <a href="{{ route('employee.attendance.index') }}" 
                   class="flex-1 px-4 py-3 text-center text-xs font-bold uppercase tracking-wider whitespace-nowrap {{ request()->routeIs('employee.attendance.*') ? 'text-[#0a2219] border-b-2 border-[#d4af37]' : 'text-gray-400 hover:text-[#0a2219]' }} transition">
                    Absensi
                </a>
                <a href="{{ route('employee.leave-requests.index') }}" 
                   class="flex-1 px-4 py-3 text-center text-xs font-bold uppercase tracking-wider whitespace-nowrap {{ request()->routeIs('employee.leave-requests*') ? 'text-[#0a2219] border-b-2 border-[#d4af37]' : 'text-gray-400 hover:text-[#0a2219]' }} transition">
                    Cuti
                </a>
                <a href="{{ route('employee.attendance-revisions.index') }}" 
                   class="flex-1 px-4 py-3 text-center text-xs font-bold uppercase tracking-wider whitespace-nowrap {{ request()->routeIs('employee.attendance-revisions*') ? 'text-[#0a2219] border-b-2 border-[#d4af37]' : 'text-gray-400 hover:text-[#0a2219]' }} transition">
                    Revisi
                </a>
                <a href="{{ route('employee.payslip.index') }}" 
                   class="flex-1 px-4 py-3 text-center text-xs font-bold uppercase tracking-wider whitespace-nowrap {{ request()->routeIs('employee.payslip*') ? 'text-[#0a2219] border-b-2 border-[#d4af37]' : 'text-gray-400 hover:text-[#0a2219]' }} transition">
                    Payslip
                </a>
            </div>
        </div>
    </div>

    <!-- Content Area -->
    <main class="pb-24 pt-4">
        <div class="p-4 max-w-2xl mx-auto">
            <!-- Flash Messages -->
            @if ($message = Session::get('success'))
                <div class="mb-5 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center shadow-sm">
                    <svg class="w-5 h-5 mr-2 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="font-semibold text-sm">{{ $message }}</p>
                </div>
            @endif

            @if ($message = Session::get('error'))
                <div class="mb-5 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center shadow-sm">
                    <svg class="w-5 h-5 mr-2 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="font-semibold text-sm">{{ $message }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-xl shadow-sm">
                    <p class="font-bold text-red-700 mb-2 text-sm">Terjadi kesalahan:</p>
                    <ul class="list-disc list-inside text-red-600 text-xs space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Bottom Navigation (Mobile Only) -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-100 z-40 md:hidden shadow-lg shadow-gray-300">
        <div class="flex justify-around max-w-lg mx-auto">
            <!-- Dashboard -->
            <a href="{{ route('employee.dashboard') }}" 
               class="flex-1 py-3 text-center {{ request()->routeIs('employee.dashboard') ? 'text-[#0a2219] border-t-2 border-[#d4af37]' : 'text-gray-400' }} transition">
                <svg class="w-5 h-5 mx-auto mb-1 {{ request()->routeIs('employee.dashboard') ? 'text-[#d4af37]' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="text-[10px] font-bold uppercase tracking-wider block">Dashboard</span>
            </a>
            <!-- Attendance -->
            <a href="{{ route('employee.attendance.index') }}" 
               class="flex-1 py-3 text-center {{ request()->routeIs('employee.attendance.*') ? 'text-[#0a2219] border-t-2 border-[#d4af37]' : 'text-gray-400' }} transition">
                <svg class="w-5 h-5 mx-auto mb-1 {{ request()->routeIs('employee.attendance.*') ? 'text-[#d4af37]' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-[10px] font-bold uppercase tracking-wider block">Absensi</span>
            </a>
            <!-- Leave Cuti -->
            <a href="{{ route('employee.leave-requests.index') }}" 
               class="flex-1 py-3 text-center {{ request()->routeIs('employee.leave-requests*') ? 'text-[#0a2219] border-t-2 border-[#d4af37]' : 'text-gray-400' }} transition">
                <svg class="w-5 h-5 mx-auto mb-1 {{ request()->routeIs('employee.leave-requests*') ? 'text-[#d4af37]' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-[10px] font-bold uppercase tracking-wider block">Cuti</span>
            </a>
            <!-- Presensi Ulang -->
            <a href="{{ route('employee.attendance-revisions.index') }}" 
               class="flex-1 py-3 text-center {{ request()->routeIs('employee.attendance-revisions*') ? 'text-[#0a2219] border-t-2 border-[#d4af37]' : 'text-gray-400' }} transition">
                <svg class="w-5 h-5 mx-auto mb-1 {{ request()->routeIs('employee.attendance-revisions*') ? 'text-[#d4af37]' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 003.7 3.7 48.657 48.657 0 007.324 0 4.006 4.006 0 003.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3l-3 3"/>
                </svg>
                <span class="text-[10px] font-bold uppercase tracking-wider block">Revisi</span>
            </a>
            <!-- Payslip -->
            <a href="{{ route('employee.payslip.index') }}" 
               class="flex-1 py-3 text-center {{ request()->routeIs('employee.payslip*') ? 'text-[#0a2219] border-t-2 border-[#d4af37]' : 'text-gray-400' }} transition">
                <svg class="w-5 h-5 mx-auto mb-1 {{ request()->routeIs('employee.payslip*') ? 'text-[#d4af37]' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="text-[10px] font-bold uppercase tracking-wider block">Payslip</span>
            </a>
        </div>
    </nav>

    {{-- PWA Service Worker & Install Prompt --}}
    <script>
    // ── Service Worker Registration ──────────────────────────
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js', { scope: '/' })
                .then(reg => {
                    console.log('[PWA] SW registered, scope:', reg.scope);

                    // Deteksi update SW — prompt user to refresh
                    reg.addEventListener('updatefound', () => {
                        const newWorker = reg.installing;
                        newWorker.addEventListener('statechange', () => {
                            if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                console.log('[PWA] New version available, reloading...');
                                newWorker.postMessage({ type: 'SKIP_WAITING' });
                                window.location.reload();
                            }
                        });
                    });

                    // Sync offline queue saat online kembali
                    window.addEventListener('online', () => {
                        console.log('[PWA] Back online, triggering sync...');
                        if (reg.sync) {
                            reg.sync.register('sync-attendance').catch(console.warn);
                        } else if (reg.active) {
                            reg.active.postMessage({ type: 'SYNC_ATTENDANCE' });
                        }
                    });
                })
                .catch(err => console.error('[PWA] SW registration failed:', err));
        });
    }

    // ── Install Prompt Handler ───────────────────────────────
    let deferredPrompt;
    const installBtn = document.getElementById('pwa-install-btn');

    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        if (installBtn) {
            installBtn.classList.remove('hidden');
        }
    });

    if (installBtn) {
        installBtn.addEventListener('click', async () => {
            if (!deferredPrompt) return;
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            console.log('[PWA] Install outcome:', outcome);
            deferredPrompt = null;
            installBtn.classList.add('hidden');
        });
    }

    window.addEventListener('appinstalled', () => {
        console.log('[PWA] App installed!');
        if (installBtn) installBtn.classList.add('hidden');
        deferredPrompt = null;
    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @yield('js')
</body>
</html>