<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0b2d20">

    <title>Login - PT. Triliun Anugrah Nusantara</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0b2d20;
        }
        .text-gold {
            color: #d4af37;
        }
        .bg-gold {
            background-color: #d4af37;
        }
        .bg-dark-green {
            background-color: #0b2d20;
        }
        .border-gold {
            border-color: #d4af37;
        }
        .focus-ring-gold:focus {
            --tw-ring-color: #d4af37;
            border-color: #d4af37;
        }
    </style>
</head>
<body class="antialiased min-h-screen flex items-center justify-center p-0 sm:p-4">

    <!-- Main Container -->
    <div class="w-full h-screen sm:h-auto sm:max-w-5xl bg-white sm:rounded-2xl shadow-2xl overflow-hidden grid grid-cols-1 md:grid-cols-12">
        
        <!-- Left Side: Branding / Background Image -->
        <div class="hidden md:flex md:col-span-6 relative bg-dark-green items-center justify-center p-12 overflow-hidden">
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('images/login-bg.png') }}');"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-[#051811] via-[#0b2d20]/80 to-[#0b2d20]/40"></div>
            
            <!-- Branding Text -->
            <div class="relative z-10 text-center flex flex-col items-center">
                <!-- Gold Styled Icon -->
                <div class="w-20 h-20 bg-gradient-to-br from-[#f3e7c4] to-[#d4af37] rounded-2xl flex items-center justify-center shadow-lg shadow-black/30 mb-6 border border-white/20">
                    <svg class="w-12 h-12 text-[#0b2d20]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l5.25 3.03M12 12.75l9-5.25M12 12.75l-9-5.25m9 5.25v9l9-5.25M12 21.75v-9" />
                    </svg>
                </div>
                
                <h1 class="text-3xl font-extrabold text-white tracking-wide leading-tight">
                    PT. TRILIUN ANUGRAH NUSANTARA
                </h1>
                <p class="text-sm font-semibold text-[#d4af37] mt-2 uppercase tracking-widest">
                    Surabaya, Jawa Timur
                </p>
                <div class="w-16 h-1 bg-[#d4af37] my-6 rounded-full"></div>
                <p class="text-gray-300 text-sm max-w-sm font-light leading-relaxed">
                    Sistem Absensi & Penggajian Karyawan berbasis Geolocation PWA. Integrasi data kehadiran dengan akurasi tinggi.
                </p>
            </div>
            
            <!-- Small Footer Accent -->
            <div class="absolute bottom-6 left-6 right-6 text-center text-xs text-gray-400 font-light">
                &copy; {{ date('Y') }} PT. Triliun Anugrah Nusantara. All rights reserved.
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="col-span-1 md:col-span-6 flex flex-col justify-center p-8 sm:p-12 md:p-16 bg-white">
            
            <!-- Mobile Header (Hidden on Desktop) -->
            <div class="flex flex-col items-center md:hidden mb-8 text-center">
                <div class="w-14 h-14 bg-gradient-to-br from-[#f3e7c4] to-[#d4af37] rounded-xl flex items-center justify-center shadow-md mb-3 border border-white/20">
                    <svg class="w-8 h-8 text-[#0b2d20]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l5.25 3.03M12 12.75l9-5.25M12 12.75l-9-5.25m9 5.25v9l9-5.25M12 21.75v-9" />
                    </svg>
                </div>
                <h2 class="text-2xl font-extrabold text-[#0b2d20]">PT. Triliun Anugrah Nusantara</h2>
                <p class="text-xs font-semibold text-gold uppercase tracking-widest mt-1">Surabaya</p>
            </div>

            <!-- Title Form -->
            <div class="mb-8">
                <h3 class="text-2xl font-bold text-gray-800">Selamat Datang</h3>
                <p class="text-sm text-gray-500 mt-1">Silakan masuk menggunakan akun karyawan atau admin Anda.</p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                            </svg>
                        </div>
                        <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" 
                            class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus-ring-gold focus:border-[#d4af37] transition duration-200" 
                            placeholder="nama@sukajadilogam.com">
                    </div>
                    @if($errors->has('email'))
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $errors->first('email') }}</p>
                    @endif
                </div>

                <!-- Password -->
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label for="password" class="block text-sm font-semibold text-gray-700">Kata Sandi</label>
                        @if (Route::has('password.request'))
                            <a class="text-xs font-semibold text-gold hover:text-[#b8933d] transition duration-150" href="{{ route('password.request') }}">
                                Lupa Kata Sandi?
                            </a>
                        @endif
                    </div>
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input id="password" type="password" name="password" required autocomplete="current-password" 
                            class="pl-10 pr-12 w-full py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#d4af37] focus:border-[#d4af37] transition duration-200"
                            placeholder="Masukkan kata sandi">
                            
                        <!-- Ubah right-0 menjadi right-3 atau right-4 -->
                        <button type="button" onclick="togglePassword('password', 'eye-icon-password')" 
                            class="absolute inset-y-0 right-0 mr-4 flex items-center text-gray-400 hover:text-[#d4af37] focus:outline-none transition-colors">
                            <svg id="eye-icon-password" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    @if($errors->has('password'))
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $errors->first('password') }}</p>
                    @endif
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-1">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer">
                        <input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-300 text-[#0b2d20] shadow-sm focus:ring-[#d4af37] focus:border-[#d4af37]">
                        <span class="ms-2 text-xs font-semibold text-gray-600">Ingat Saya</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" class="w-full bg-gold hover:bg-[#b8933d] text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-yellow-600/10 hover:shadow-yellow-600/20 active:transform active:scale-[0.98] transition duration-200 text-sm">
                        Masuk Aplikasi
                    </button>
                </div>
            </form>
            
            <!-- Quick Info / Help desk -->
            <div class="mt-8 text-center text-xs text-gray-400">
                Mengalami kendala login? Hubungi bagian HRD / IT Support.
            </div>

        </div>

    </div>

</body>
<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />';
        } else {
            input.type = 'password';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
        }
    }
</script>
</html>
