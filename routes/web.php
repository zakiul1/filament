<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;

use App\Livewire\Admin\Users\ManageUsers;
use App\Livewire\Admin\Settings\CompanyProfile;
use App\Livewire\Admin\Settings\SignatoriesPage;
use App\Livewire\Admin\Settings\DocumentSignSettingsPage;

use App\Livewire\Admin\Master\CountriesPage;
use App\Livewire\Admin\Master\CurrenciesPage;

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

/*
|--------------------------------------------------------------------------
| Root Redirect
|--------------------------------------------------------------------------
| No public "home" page. When someone hits '/', send them to dashboard.
*/

Route::redirect('/', '/dashboard')->name('home');

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
| Phase 1 + Phase 2 Admin Area
| (Later you can add a SUPER_ADMIN middleware here)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |----------------------------------------------
        | Phase 1 – System Foundation
        |----------------------------------------------
        */

        // User & Role Management
        Route::get('/users', ManageUsers::class)->name('users.index');

        // Company Settings
        Route::get('/company', CompanyProfile::class)->name('company.profile');

        // Signatories
        Route::get('/signatories', SignatoriesPage::class)->name('signatories.index');

        // Document Sign & Seal Mapping
        Route::get('/document-sign-settings', DocumentSignSettingsPage::class)
            ->name('document-sign-settings.index');

        /*
        |----------------------------------------------
        | Phase 2.1 – Master Data: Countries & Currencies
        | URL prefix: /admin/master/...
        |----------------------------------------------
        */

        Route::prefix('master')
            ->name('master.')
            ->group(function () {
                // Countries
                Route::get('/countries', CountriesPage::class)
                    ->name('countries.index');

                // Currencies
                Route::get('/currencies', CurrenciesPage::class)
                    ->name('currencies.index');
            });
    });
