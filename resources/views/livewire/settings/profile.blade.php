<?php

use App\Models\User;
use App\Helpers\Flash;
use Livewire\Volt\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

new class extends Component {
    public string $name = '';
    public string $email = '';
    public string $phone = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
        $this->phone = Auth::user()->customer->phone ?? '';
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id)
            ],

            'phone' => ['required', 'string', 'max:20'],
        ]);

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Update customer data
        if ($user->customer) {
            $user->customer->update([
                'phone' => $validated['phone'],
                'name' => $validated['name'], // Keep customer name in sync
            ]);
        }

        Flash::success('Profile updated successfully');

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profile Information')" :subheading="__('Update your account\'s profile information and email address')">
        <!-- Profile Avatar Section -->
        <div
            class="mb-8 flex items-center gap-6 rounded-lg bg-gradient-to-r from-blue-50 to-purple-50 p-6 dark:from-blue-950/20 dark:to-purple-950/20">
            <x-ui.user-avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" size="xl"
                :gradient="true" />
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ auth()->user()->email }}</p>
                @if(auth()->user()->customer?->phone)
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ auth()->user()->customer->phone }}</p>
                @endif
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">{{ __('Member since') }}
                    {{ auth()->user()->created_at->format('M Y') }}
                </p>
            </div>
        </div>

        <form wire:submit="updateProfileInformation" class="space-y-6">
            <!-- Name Field -->
            <div>
                <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name"
                    class="w-full" />
            </div>

            <!-- Phone Field -->
            <div>
                <flux:input wire:model="phone" :label="__('Phone Number')" type="tel" required autocomplete="tel"
                    class="w-full" placeholder="+1 (555) 123-4567" />
            </div>

            <!-- Email Field -->
            <div>
                <flux:input wire:model="email" :label="__('Email Address')" type="email" required autocomplete="email"
                    class="w-full" />

                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                    <div
                        class="mt-4 rounded-lg border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-900 dark:bg-yellow-950/20">
                        <div class="flex items-start gap-3">
                            <svg class="h-5 w-5 text-yellow-600 dark:text-yellow-500" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" />
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                    {{ __('Your email address is unverified.') }}
                                </p>
                                <button type="button" wire:click.prevent="resendVerificationNotification"
                                    class="mt-2 text-sm font-medium text-yellow-900 underline hover:text-yellow-700 dark:text-yellow-100 dark:hover:text-yellow-300">
                                    {{ __('Click here to re-send the verification email.') }}
                                </button>
                            </div>
                        </div>

                        @if (session('status') === 'verification-link-sent')
                            <div class="mt-3 rounded-md bg-green-50 p-3 dark:bg-green-950/20">
                                <p class="text-sm font-medium text-green-800 dark:text-green-200">
                                    {{ __('A new verification link has been sent to your email address.') }}
                                </p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-between gap-4 border-t border-gray-200 pt-6 dark:border-zinc-700">
                <x-action-message on="profile-updated" class="text-sm font-medium text-green-600 dark:text-green-400">
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                        </svg>
                        {{ __('Saved successfully!') }}
                    </div>
                </x-action-message>

                <button type="submit"
                    class="rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-2.5 font-semibold text-white shadow-md transition-all hover:from-blue-700 hover:to-purple-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900"
                    data-test="update-profile-button">
                    {{ __('Save Changes') }}
                </button>
            </div>
        </form>
    </x-settings.layout>
</section>