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
use App\Livewire\Admin\Master\PortsPage;
use App\Livewire\Admin\Master\ShipmentModesPage;
use App\Livewire\Admin\Master\IncotermsPage;
use App\Livewire\Admin\Master\PaymentTermsPage;
use App\Livewire\Admin\Master\BanksPage;
use App\Livewire\Admin\Master\BankBranchesPage;
use App\Livewire\Admin\Master\BeneficiaryCompaniesPage;
use App\Livewire\Admin\Master\BeneficiaryBankAccountsPage;
use App\Livewire\Admin\Master\CouriersPage;
use App\Livewire\Admin\Master\CustomersPage;
use App\Livewire\Admin\Master\CustomerBanksPage;
use App\Livewire\Admin\Master\FactoriesPage;
use App\Livewire\Admin\Master\FactoryCategoriesPage;
use App\Livewire\Admin\Master\FactorySubcategoriesPage;
use App\Livewire\Admin\Master\FactoryCertificatesPage;
use App\Livewire\Admin\Trade\ProformaInvoicesPage;
use App\Livewire\Admin\Trade\LcReceivesPage;
use App\Livewire\Admin\Trade\LcReceiveCreate;
use App\Livewire\Admin\Trade\LcReceiveEdit;
use App\Http\Controllers\Admin\Trade\ProformaInvoicePrintController;
use App\Http\Controllers\Admin\Trade\CommercialInvoicePrintController;
use App\Livewire\Admin\Trade\LcTransfersPage;
use App\Livewire\Admin\Trade\LcTransferCreate;
use App\Livewire\Admin\Trade\LcTransferEdit;
use App\Livewire\Admin\Trade\LcAmendmentsPage;
use App\Livewire\Admin\Trade\LcAmendmentCreate;
use App\Livewire\Admin\Trade\LcAmendmentEdit;
use App\Livewire\Admin\Trade\CommercialInvoicesPage;
use App\Livewire\Admin\Trade\CommercialInvoiceCreate;
use App\Livewire\Admin\Trade\CommercialInvoiceEdit;


use App\Livewire\Admin\Trade\ProformaInvoiceCreate;
use App\Livewire\Admin\Trade\ProformaInvoiceEdit;







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

                Route::get('/ports', PortsPage::class)
                    ->name('ports.index');

                // Shipment Modes
                Route::get('/shipment-modes', ShipmentModesPage::class)
                    ->name('shipment-modes.index');
                // Incoterms
                Route::get('/incoterms', IncotermsPage::class)
                    ->name('incoterms.index');

                // Payment Terms
                Route::get('/payment-terms', PaymentTermsPage::class)
                    ->name('payment-terms.index');

                // Banks
                Route::get('/banks', BanksPage::class)
                    ->name('banks.index');

                // Bank Branches
                Route::get('/bank-branches', BankBranchesPage::class)
                    ->name('bank-branches.index');

                // Beneficiary Companies
                Route::get('/beneficiary-companies', BeneficiaryCompaniesPage::class)
                    ->name('beneficiary-companies.index');

                // Beneficiary Bank Accounts
                Route::get('/beneficiary-bank-accounts', BeneficiaryBankAccountsPage::class)
                    ->name('beneficiary-bank-accounts.index');

                // Couriers
                Route::get('/couriers', CouriersPage::class)
                    ->name('couriers.index');
                // Customers
                Route::get('/customers', CustomersPage::class)
                    ->name('customers.index');

                Route::get('/customer-banks', CustomerBanksPage::class)
                    ->name('customer-banks.index');
                Route::get('/factories', FactoriesPage::class)->name('factories.index');
                Route::get('/factory-categories', FactoryCategoriesPage::class)->name('factory-categories.index');
                Route::get('/factory-subcategories', FactorySubcategoriesPage::class)->name('factory-subcategories.index');
                Route::get('/factory-certificates', FactoryCertificatesPage::class)->name('factory-certificates.index');


            });



        Route::prefix('trade')
            ->name('trade.')
            ->group(function () {
                Route::get('/proforma-invoices', ProformaInvoicesPage::class)
                    ->name('proforma-invoices.index');

                Route::get('/proforma-invoices/create', ProformaInvoiceCreate::class)
                    ->name('proforma-invoices.create');

                Route::get('/proforma-invoices/{record}/edit', ProformaInvoiceEdit::class)
                    ->name('proforma-invoices.edit');

                Route::get('/proforma-invoices/{proformaInvoice}/print', [ProformaInvoicePrintController::class, 'show'])
                    ->name('proforma-invoices.print');

                // LC Receive
                Route::get('/lc-receives', LcReceivesPage::class)->name('lc-receives.index');
                Route::get('/lc-receives/create', LcReceiveCreate::class)->name('lc-receives.create');
                Route::get('/lc-receives/{record}/edit', LcReceiveEdit::class)->name('lc-receives.edit');

                // Trade – LC Transfers
                Route::get('/lc-transfers', LcTransfersPage::class)
                    ->name('lc-transfers.index');

                Route::get('/lc-transfers/create', LcTransferCreate::class)
                    ->name('lc-transfers.create');

                Route::get('/lc-transfers/{lcTransfer}/edit', LcTransferEdit::class)
                    ->name('lc-transfers.edit');

                // LC Amendments
                Route::get('/lc-amendments', LcAmendmentsPage::class)
                    ->name('lc-amendments.index');

                Route::get('/lc-amendments/create', LcAmendmentCreate::class)
                    ->name('lc-amendments.create');

                Route::get('/lc-amendments/{record}/edit', LcAmendmentEdit::class)
                    ->name('lc-amendments.edit');



                // Commercial Invoices
                Route::get('/commercial-invoices', CommercialInvoicesPage::class)
                    ->name('commercial-invoices.index');

                Route::get('/commercial-invoices/create', CommercialInvoiceCreate::class)
                    ->name('commercial-invoices.create');

                Route::get('/commercial-invoices/{record}/edit', CommercialInvoiceEdit::class)
                    ->name('commercial-invoices.edit');

                // ⬇️ NEW: Commercial Invoice print route
                Route::get('/commercial-invoices/{commercialInvoice}/print', [CommercialInvoicePrintController::class, 'show'])
                    ->name('commercial-invoices.print');


            });


    });