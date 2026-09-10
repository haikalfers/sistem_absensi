<x-guest-layout>
    <div class="mb-4 text-xs font-semibold text-gray-500 leading-relaxed">
        {{ __('Lupa kata sandi? Silakan masukkan alamat email Anda. Kami akan mengirimkan tautan reset kata sandi melalui email agar Anda dapat membuat password baru.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Alamat Email')" class="font-bold text-gray-700 mb-1" />
            <x-text-input id="email" class="block mt-1 w-full border-gray-300 focus:border-red-500 focus:ring-red-500" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <a class="text-xs font-bold text-red-600 hover:text-red-700 transition duration-150" href="{{ route('login') }}">
                Kembali ke Login
            </a>
            
            <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-[#dc2626] to-[#991b1b] hover:from-[#b91c1c] hover:to-[#7f1d1d] border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest active:scale-95 transition ease-in-out duration-150 shadow-md">
                Kirim Tautan Reset
            </button>
        </div>
    </form>
</x-guest-layout>
