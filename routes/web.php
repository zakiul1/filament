<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;

use App\Livewire\Admin\Users\ManageUsers;
use App\Livewire\Admin\Settings\CompanyProfile;
use App\Livewire\Admin\Settings\SignatoriesPage;
use App\Livewire\Admin\Settings\DocumentSignSettingsPage;

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| User Settings (Breeze/Fortify built-in)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

    Route::get('settings/two-factor', TwoFactor::class)
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

/*
|--------------------------------------------------------------------------
| Phase 1 – Admin (System Foundation)
| Only SUPER_ADMIN can access these pages
|--------------------------------------------------------------------------
*/

Route::middleware(['auth',])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // User & Role Management
        Route::get('/users', ManageUsers::class)->name('users.index');

        // Company Settings
        Route::get('/company', CompanyProfile::class)->name('company.profile');

        // Signatories
        Route::get('/signatories', SignatoriesPage::class)->name('signatories.index');

        // Document Sign & Seal Mapping
        Route::get('/document-sign-settings', DocumentSignSettingsPage::class)
            ->name('document-sign-settings.index');
    });