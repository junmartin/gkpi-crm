<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Trigram -->
        <div class="mt-4">
            <x-input-label for="username" :value="__('Trigram')" />
            <x-text-input id="username" class="block mt-1 w-full uppercase" type="text" name="username" :value="old('username')" required autocomplete="username" maxlength="3" placeholder="JMS" />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Use 3 letters that are easy to remember. This must be unique.') }}</p>
        </div>

        <!-- PIN -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('PIN')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            data-pin-input
                            maxlength="6"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Use a 4 to 6 digit PIN.') }}</p>
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
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
