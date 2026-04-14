<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('Forgot your PIN? Enter your trigram and we will send a reset link to the email saved on your account.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Trigram -->
        <div>
            <x-input-label for="username" :value="__('Trigram')" />
            <x-text-input id="username" class="block mt-1 w-full uppercase" type="text" name="username" :value="old('username')" required autofocus maxlength="3" placeholder="JMS" />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Send PIN Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
