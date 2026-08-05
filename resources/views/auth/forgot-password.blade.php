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
            <x-text-input id="email" class="block mt-1 w-full border-gray-300 focus:border-[#d4af37] focus:ring-[#d4af37]" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <a class="text-xs font-bold text-[#0b2d20] hover:text-[#d4af37] transition duration-150" href="{{ route('login') }}">
                Kembali ke Login
            </a>
            
            <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-[#d4af37] to-[#b8933d] hover:from-[#b8933d] hover:to-[#a6802e] border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest active:scale-95 transition ease-in-out duration-150 shadow-md">
                Kirim Tautan Reset
            </button>
        </div>
    </form>
</x-guest-layout>
