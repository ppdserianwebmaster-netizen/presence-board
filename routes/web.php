<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use App\Livewire\PublicBoard\PresenceBoard;
use App\Livewire\Admin;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('welcome'))->name('home');
Route::get('/presence-board', PresenceBoard::class)->name('presence.board');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Default Dashboard (Entry point after login)
    Route::view('dashboard', 'dashboard')->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Admin Resources
    |--------------------------------------------------------------------------
    */
    Route::middleware(['can:admin-access'])->prefix('admin')->name('admin.')->group(function () {
        
        Route::get('/users', Admin\User\UserIndex::class)->name('users.index');
        Route::get('/movements', Admin\Movement\MovementIndex::class)->name('movements.index');
    });

    /*
    |--------------------------------------------------------------------------
    | Settings (Default Starter Kit)
    |--------------------------------------------------------------------------
    */
    Route::redirect('settings', 'settings/profile');
    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');
    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(when(Features::canManageTwoFactorAuthentication() && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'), ['password.confirm'], []))
        ->name('two-factor.show');
});
