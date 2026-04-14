<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Trigram -->
        <div>
            <x-input-label for="username" :value="__('Trigram')" />
            <x-text-input id="username" class="block mt-1 w-full uppercase" type="text" name="username" :value="old('username', $username)" required autofocus autocomplete="username" maxlength="3" placeholder="JMS" />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <!-- PIN -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('New PIN')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" data-pin-input maxlength="6" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm PIN -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm PIN')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation"
                                data-pin-input
                                maxlength="6"
                                required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Reset PIN') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
