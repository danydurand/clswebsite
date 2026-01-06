<?php

use App\Livewire\Bets;
use Livewire\Volt\Volt;
use App\Livewire\Events;
use App\Livewire\Raffles;
use App\Livewire\Tickets;
use App\Livewire\HowToPlay;
use App\Livewire\EditTicket;
use App\Livewire\ViewTicket;
use Laravel\Fortify\Features;
use App\Livewire\CreateTicket;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/terms-and-conditions', \App\Livewire\TermsAndConditions::class)
    ->name('terms-and-conditions');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {

    Route::get('raffles', Raffles::class)->name('raffles');
    Route::get('events', Events::class)->name('events');
    Route::get('bets', Bets::class)->name('bets');
    Route::get('tickets', Tickets::class)->name('tickets.index');
    Route::get('tickets/create', CreateTicket::class)->name('tickets.create');
    Route::get('tickets/view/{id}', ViewTicket::class)->name('tickets.view');
    Route::get('tickets/edit/{id}', EditTicket::class)->name('tickets.edit');
    Route::get('tickets/how-to-play/{id}', HowToPlay::class)->name('tickets.how-to-play');

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
