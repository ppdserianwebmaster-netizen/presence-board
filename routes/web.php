<?php

use App\Livewire\Settings\{Appearance, Password, Profile, TwoFactor};
use App\Livewire\PublicBoard\PresenceBoard;
use App\Livewire\Admin;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
// Route::get('/', fn () => view('welcome'))->name('home');
Route::get('/', fn () => redirect()->route('login'))->name('home');
Route::get('/presence-board', PresenceBoard::class)->name('presence.board');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    
    /**
     * Dashboard Redirection Logic
     * Instead of a static view, you might want to point this to the 
     * EmployeeDashboard or an Admin Overview depending on the user.
     */
    Route::get('/dashboard', function () {
        return view('dashboard'); 
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Admin-Only Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['can:admin-access'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            // Dashboard / Stats
            Route::get('/users', Admin\User\UserIndex::class)->name('users.index');
            Route::get('/movements', Admin\Movement\MovementIndex::class)->name('movements.index');
        });

    /*
    |--------------------------------------------------------------------------
    | User Settings
    |--------------------------------------------------------------------------
    */
    Route::prefix('settings')->group(function () {
        Route::redirect('/', '/settings/profile');
        
        Route::get('/profile', Profile::class)->name('profile.edit');
        Route::get('/password', Password::class)->name('user-password.edit');
        Route::get('/appearance', Appearance::class)->name('appearance.edit');
        
        // Two-Factor Auth with conditional middleware
        // $tfaMiddleware = Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword') 
        //     ? ['password.confirm'] 
        //     : [];

        // Route::get('/two-factor', TwoFactor::class)
        //     ->middleware($tfaMiddleware)
        //     ->name('two-factor.show');
    });
});
