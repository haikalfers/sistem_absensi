<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Sistem Absensi PT. Triliun Anugrah Nusantara</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('css')

    <style>
        .sidebar-active {
            background-color: #123b2c;
            color: #d4af37;
            border-left: 4px solid #d4af37;
        }
    </style>
</head>

<body class="bg-[#f7f9f7] font-sans antialiased text-gray-800">
    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar -->
        <aside
            class="w-64 bg-[#0a2219] text-white shadow-xl flex flex-col justify-between border-r border-[#153a2b] flex-shrink-0">
            <div>
                <!-- Brand Header -->
                <div class="p-5 border-b border-[#153a2b] bg-[#071912] flex items-center space-x-3">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-[#f3e7c4] to-[#d4af37] rounded-xl flex items-center justify-center shadow-md border border-white/10 flex-shrink-0">
                        <svg class="w-6 h-6 text-[#0a2219]" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l5.25 3.03M12 12.75l9-5.25M12 12.75l-9-5.25m9 5.25v9l9-5.25M12 21.75v-9" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-sm font-extrabold tracking-wider text-white">Triliun Anugrah Nusantara</h1>
                        <p class="text-[10px] font-bold text-[#d4af37] tracking-widest uppercase">Admin Portal</p>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="mt-6 space-y-1.5 px-3">
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center px-4 py-2.5 rounded-xl transition duration-150 text-sm font-semibold {{ request()->routeIs('admin.dashboard') ? 'sidebar-active' : 'text-gray-300 hover:bg-[#123b2c] hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.dashboard') ? 'text-[#d4af37]' : 'text-gray-400' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21.75h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21.75h2.25" />
                        </svg>
                        Dashboard
                    </a>

                    <!-- Management Header -->
                    <div class="pt-4 pb-1">
                        <p class="px-4 text-[10px] font-extrabold text-[#d4af37]/60 uppercase tracking-widest">
                            Management</p>
                    </div>

                    <!-- Karyawan -->
                    <a href="{{ route('admin.employees.index') }}"
                        class="flex items-center px-4 py-2.5 rounded-xl transition duration-150 text-sm font-semibold {{ request()->routeIs('admin.employees*') ? 'sidebar-active' : 'text-gray-300 hover:bg-[#123b2c] hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.employees*') ? 'text-[#d4af37]' : 'text-gray-400' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.109A11.386 11.386 0 0110.089 20.5a11.378 11.378 0 01-4.94-1.263v-.11a11.353 11.353 0 010-3.187m0 4.382v-.003c0-1.113.285-2.16.786-3.07M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm6.375 2.25a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zM13.5 8.25a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                        </svg>
                        Karyawan
                    </a>

                    <!-- Absensi -->
                    <a href="{{ route('admin.attendance.index') }}"
                        class="flex items-center px-4 py-2.5 rounded-xl transition duration-150 text-sm font-semibold {{ request()->routeIs('admin.attendance.*') ? 'sidebar-active' : 'text-gray-300 hover:bg-[#123b2c] hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.attendance.*') ? 'text-[#d4af37]' : 'text-gray-400' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zM14.25 15h.008v.008H14.25V15zm0 2.25h.008v.008H14.25v-.008zM16.5 15h.008v.008H16.5V15zm0 2.25h.008v.008H16.5v-.008zM12 12.75h.008v.008H12v-.008zM9.75 12.75h.008v.008H9.75v-.008zM7.5 12.75h.008v.008H7.5v-.008zM14.25 12.75h.008v.008H14.25v-.008zM16.5 12.75h.008v.008H16.5v-.008z" />
                        </svg>
                        Absensi
                    </a>

                    <!-- Lembur -->
                    <a href="{{ route('admin.overtime.index') }}"
                        class="flex items-center px-4 py-2.5 rounded-xl transition duration-150 text-sm font-semibold {{ request()->routeIs('admin.overtime*') ? 'sidebar-active' : 'text-gray-300 hover:bg-[#123b2c] hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.overtime*') ? 'text-[#d4af37]' : 'text-gray-400' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Lembur
                    </a>

                    <!-- Cuti & Izin -->
                    <a href="{{ route('admin.leave-requests.index') }}"
                        class="flex-1 flex items-center px-4 py-2.5 rounded-xl transition duration-150 text-sm font-semibold {{ request()->routeIs('admin.leave-requests*') ? 'sidebar-active' : 'text-gray-300 hover:bg-[#123b2c] hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.leave-requests*') ? 'text-[#d4af37]' : 'text-gray-400' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        Cuti & Izin
                    </a>

                    <!-- Presensi Ulang -->
                    <a href="{{ route('admin.attendance-revisions.index') }}"
                        class="flex items-center px-4 py-2.5 rounded-xl transition duration-150 text-sm font-semibold {{ request()->routeIs('admin.attendance-revisions*') ? 'sidebar-active' : 'text-gray-300 hover:bg-[#123b2c] hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.attendance-revisions*') ? 'text-[#d4af37]' : 'text-gray-400' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 003.7 3.7 48.657 48.657 0 007.324 0 4.006 4.006 0 003.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3l-3 3" />
                        </svg>
                        Presensi Ulang
                    </a>

                    <!-- Penggajian -->
                    <a href="{{ route('admin.payrolls.index') }}"
                        class="flex items-center px-4 py-2.5 rounded-xl transition duration-150 text-sm font-semibold {{ request()->routeIs('admin.payrolls*') ? 'sidebar-active' : 'text-gray-300 hover:bg-[#123b2c] hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.payrolls*') ? 'text-[#d4af37]' : 'text-gray-400' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5h16.5M3.75 20.25zM3.75 20.25H21M3.75 20.25zM21 4.5V18.75m0-14.25a8.997 8.997 0 00-6.002-3.364M21 4.5v14.25m0-14.25a8.997 8.997 0 01-6.002-3.364M18.75 9.75A3.75 3.75 0 0015 6a3.75 3.75 0 00-3.75 3.75A3.75 3.75 0 0015 13.5a3.75 3.75 0 003.75-3.75z" />
                        </svg>
                        Penggajian
                    </a>

                    <!-- Others Header -->
                    <div class="pt-4 pb-1">
                        <p class="px-4 text-[10px] font-extrabold text-[#d4af37]/60 uppercase tracking-widest">Lainnya
                        </p>
                    </div>

                    <!-- Laporan -->
                    <a href="{{ route('admin.reports.attendance') }}"
                        class="flex items-center px-4 py-2.5 rounded-xl transition duration-150 text-sm font-semibold {{ request()->routeIs('admin.reports*') ? 'sidebar-active' : 'text-gray-300 hover:bg-[#123b2c] hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.reports*') ? 'text-[#d4af37]' : 'text-gray-400' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12h9m9 3H12m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        Laporan
                    </a>

                    {{-- <!-- Pengaturan (Disembunyikan sementara) -->
                    <a href="{{ route('admin.settings.locations') }}"
                        class="flex items-center px-4 py-2.5 rounded-xl transition duration-150 text-sm font-semibold {{ request()->routeIs('admin.settings*') ? 'sidebar-active' : 'text-gray-300 hover:bg-[#123b2c] hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.settings*') ? 'text-[#d4af37]' : 'text-gray-400' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.43l-1.003.828c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.43l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.991l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Pengaturan
                    </a> --}}
                </nav>
            </div>

            <!-- User Info & Logout -->
            <div class="p-4 border-t border-[#153a2b] bg-[#071912]">
                <div class="flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] font-bold text-[#d4af37] tracking-wider uppercase">Administrator</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="ml-2">
                        @csrf
                        <button type="submit"
                            class="text-gray-400 hover:text-red-400 p-1.5 rounded-lg hover:bg-white/5 transition"
                            title="Logout">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Navbar -->
            <header class="bg-white border-b border-gray-100 shadow-sm flex-shrink-0">
                <div class="px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <h2 class="text-lg font-bold text-[#0a2219]">@yield('page-title', 'Dashboard')</h2>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div
                            class="flex items-center space-x-1.5 bg-[#f0f4f2] text-[#0a2219] px-3 py-1.5 rounded-lg text-xs font-bold border border-[#d2dfd8]">
                            <svg class="w-4 h-4 text-[#0a2219]" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>{{ now()->format('d M Y, H:i') }} WIB</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 overflow-auto">
                <div class="p-6">
                    <!-- Flash Messages -->
                    @if ($message = Session::get('success'))
                        <div
                            class="mb-5 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center shadow-sm">
                            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="font-semibold text-sm">{{ $message }}</p>
                        </div>
                    @endif

                    @if ($message = Session::get('error'))
                        <div
                            class="mb-5 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center shadow-sm">
                            <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
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
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmAction(event, message, element) {
            event.preventDefault();
            Swal.fire({
                title: 'Konfirmasi',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0a2219',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal',
                borderRadius: '1rem'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (element.tagName === 'FORM') {
                        element.submit();
                    } else if (element.closest('form')) {
                        element.closest('form').submit();
                    } else if (element.getAttribute('href')) {
                        window.location.href = element.getAttribute('href');
                    }
                }
            });
        }
    </script>
    @yield('js')
</body>

</html>
