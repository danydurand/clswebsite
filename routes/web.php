<?php

use App\Livewire\Bets;
use Livewire\Volt\Volt;
use App\Livewire\Events;
use App\Livewire\Raffles;
use App\Livewire\Tickets;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {

    Route::get('raffles', Raffles::class)->name('raffles');
    Route::get('events', Events::class)->name('events');
    Route::get('bets', Bets::class)->name('bets');
    Route::get('tickets', Tickets::class)->name('tickets');

});

Route::middleware(['auth'])->group(function () {

    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});
