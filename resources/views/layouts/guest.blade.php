<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-[#0b2d20]">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[#0b2d20] bg-opacity-95">
            <div class="flex flex-col items-center">
                <a href="/" class="flex flex-col items-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-[#f3e7c4] to-[#d4af37] rounded-2xl flex items-center justify-center shadow-lg border border-white/20">
                        <svg class="w-10 h-10 text-[#0b2d20]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l5.25 3.03M12 12.75l9-5.25M12 12.75l-9-5.25m9 5.25v9l9-5.25M12 21.75v-9" />
                        </svg>
                    </div>
                    <span class="text-white font-extrabold mt-3 tracking-wider text-lg">PT. TRILIUN ANUGRAH NUSANTARA</span>
                    <span class="text-[#d4af37] text-xs font-semibold uppercase tracking-widest mt-0.5">Surabaya</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-5 bg-white shadow-2xl overflow-hidden sm:rounded-2xl border border-gray-100">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
