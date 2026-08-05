<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Alamat Email')" class="font-bold text-gray-700 mb-1" />
            <x-text-input id="email" class="block mt-1 w-full border-gray-300 focus:border-[#d4af37] focus:ring-[#d4af37]" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password Baru')" class="font-bold text-gray-700 mb-1" />
            <x-text-input id="password" class="block mt-1 w-full border-gray-300 focus:border-[#d4af37] focus:ring-[#d4af37]" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" class="font-bold text-gray-700 mb-1" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full border-gray-300 focus:border-[#d4af37] focus:ring-[#d4af37]" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-gradient-to-r from-[#d4af37] to-[#b8933d] hover:from-[#b8933d] hover:to-[#a6802e] border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest active:scale-95 transition ease-in-out duration-150 shadow-md">
                {{ __('Reset Password') }}
            </button>
        </div>
    </form>
</x-guest-layout>
