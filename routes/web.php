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

// Language switching
Route::get('/lang/{locale}', [App\Http\Controllers\LanguageController::class, 'switch'])->name('lang.switch');

// Public pages
Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/contact', [\App\Http\Controllers\ContactController::class, 'show'])->name('contact');
Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'send'])->name('contact.send');

Route::get('/terms-and-conditions', \App\Livewire\TermsAndConditions::class)
    ->name('terms-and-conditions');

Route::get('/faqs', \App\Livewire\Faqs::class)
    ->name('faqs');


Route::get('dashboard', \App\Livewire\Dashboard::class)
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
    Route::get('tickets/print/{id}', function ($id) {
        $ticket = \App\Models\Ticket::with(['ticketDetails.raffle.lottery', 'ticketDetails.game'])->findOrFail($id);
        return view('tickets.online-ticket-pdf', compact('ticket'));
    })->name('tickets.print');

});

Route::middleware(['auth'])->group(function () {

    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    // Volt::route('settings/two-factor', 'settings.two-factor')
    //     ->middleware(
    //         when(
    //             Features::canManageTwoFactorAuthentication()
    //             && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
    //             ['password.confirm'],
    //             [],
    //         ),
    //     )
    //     ->name('two-factor.show');
});
