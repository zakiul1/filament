<?php

use App\Http\Controllers\Print\NegotiationLetterPrintController;
use App\Http\Controllers\Print\PackingListPrintController;
use App\Http\Controllers\Print\SampleInvoicePrintController;
use App\Livewire\Admin\Trade\NegotiationLetterCreate;
use App\Livewire\Admin\Trade\NegotiationLetterEdit;
use App\Livewire\Admin\Trade\NegotiationLettersPage;
use App\Livewire\Admin\Trade\PackingListCreate;
use App\Livewire\Admin\Trade\PackingListEdit;
use App\Livewire\Admin\Trade\PackingListsPage;
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
use App\Livewire\Admin\Trade\ProformaInvoiceCreate;
use App\Livewire\Admin\Trade\ProformaInvoiceEdit;

use App\Livewire\Admin\Trade\LcReceivesPage;
use App\Livewire\Admin\Trade\LcReceiveCreate;
use App\Livewire\Admin\Trade\LcReceiveEdit;

use App\Livewire\Admin\Trade\SampleInvoicesPage;
use App\Livewire\Admin\Trade\SampleInvoiceCreate;
use App\Livewire\Admin\Trade\SampleInvoiceEdit;




use App\Livewire\Admin\Trade\LcTransfersPage;
use App\Livewire\Admin\Trade\LcTransferCreate;
use App\Livewire\Admin\Trade\LcTransferEdit;

use App\Livewire\Admin\Trade\LcAmendmentsPage;
use App\Livewire\Admin\Trade\LcAmendmentCreate;
use App\Livewire\Admin\Trade\LcAmendmentEdit;

use App\Livewire\Admin\Trade\CommercialInvoicesPage;
use App\Livewire\Admin\Trade\CommercialInvoiceCreate;
use App\Livewire\Admin\Trade\CommercialInvoiceEdit;

use App\Http\Controllers\Print\ProformaInvoicePrintController;
use App\Http\Controllers\Print\CommercialInvoicePrintController;
use App\Http\Controllers\Print\LcReceivePrintController;
use App\Http\Controllers\Print\LcAmendmentPrintController;

use App\Livewire\Admin\Trade\BillOfExchangesPage;
use App\Livewire\Admin\Trade\BillOfExchangeCreate;
use App\Livewire\Admin\Trade\BillOfExchangeEdit;
use App\Http\Controllers\Print\BillOfExchangePrintController;


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
| Admin Area (Phase 1 + Phase 2)
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
        | Phase 2 – Master Data
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

                // Ports
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

                // Customer Banks
                Route::get('/customer-banks', CustomerBanksPage::class)
                    ->name('customer-banks.index');

                // Factories master
                Route::get('/factories', FactoriesPage::class)
                    ->name('factories.index');

                Route::get('/factory-categories', FactoryCategoriesPage::class)
                    ->name('factory-categories.index');

                Route::get('/factory-subcategories', FactorySubcategoriesPage::class)
                    ->name('factory-subcategories.index');

                Route::get('/factory-certificates', FactoryCertificatesPage::class)
                    ->name('factory-certificates.index');
            });

        /*
        |----------------------------------------------
        | Trade Module (Phase 3+)
        | URL prefix: /admin/trade/...
        |----------------------------------------------
        */

        Route::prefix('trade')
            ->name('trade.')
            ->group(function () {

                /*
                |-----------------------------
                | Proforma Invoices
                |-----------------------------
                */
                Route::get('/proforma-invoices', ProformaInvoicesPage::class)
                    ->name('proforma-invoices.index');

                Route::get('/proforma-invoices/create', ProformaInvoiceCreate::class)
                    ->name('proforma-invoices.create');

                Route::get('/proforma-invoices/{record}/edit', ProformaInvoiceEdit::class)
                    ->name('proforma-invoices.edit');

                Route::get(
                    '/proforma-invoices/{proformaInvoice}/print',
                    [ProformaInvoicePrintController::class, 'show']
                )->name('proforma-invoices.print');

                /*
                |-----------------------------
                | LC Receives
                |-----------------------------
                */
                Route::get('/lc-receives', LcReceivesPage::class)
                    ->name('lc-receives.index');

                Route::get('/lc-receives/create', LcReceiveCreate::class)
                    ->name('lc-receives.create');

                Route::get('/lc-receives/{record}/edit', LcReceiveEdit::class)
                    ->name('lc-receives.edit');

                Route::get(
                    '/lc-receives/{lcReceive}/print',
                    [LcReceivePrintController::class, 'show']
                )->name('lc-receives.print');

                /*
                |-----------------------------
                | LC Transfers
                |-----------------------------
                */
                Route::get('/lc-transfers', LcTransfersPage::class)
                    ->name('lc-transfers.index');

                Route::get('/lc-transfers/create', LcTransferCreate::class)
                    ->name('lc-transfers.create');

                Route::get('/lc-transfers/{lcTransfer}/edit', LcTransferEdit::class)
                    ->name('lc-transfers.edit');

                /*
                |-----------------------------
                | LC Amendments
                |-----------------------------
                */
                Route::get('/lc-amendments', LcAmendmentsPage::class)
                    ->name('lc-amendments.index');

                Route::get('/lc-amendments/create', LcAmendmentCreate::class)
                    ->name('lc-amendments.create');

                Route::get('/lc-amendments/{record}/edit', LcAmendmentEdit::class)
                    ->name('lc-amendments.edit');

                Route::get(
                    '/lc-amendments/{lcAmendment}/print',
                    [LcAmendmentPrintController::class, 'show']
                )->name('lc-amendments.print');

                /*
                |-----------------------------
                | Commercial Invoices
                |-----------------------------
                */
                Route::get('/commercial-invoices', CommercialInvoicesPage::class)
                    ->name('commercial-invoices.index');

                Route::get('/commercial-invoices/create', CommercialInvoiceCreate::class)
                    ->name('commercial-invoices.create');

                Route::get('/commercial-invoices/{record}/edit', CommercialInvoiceEdit::class)
                    ->name('commercial-invoices.edit');

                Route::get(
                    '/commercial-invoices/{commercialInvoice}/print',
                    [CommercialInvoicePrintController::class, 'show']
                )->name('commercial-invoices.print');


                Route::get('/packing-lists', PackingListsPage::class)->name('packing-lists.index');
                Route::get('/packing-lists/create', PackingListCreate::class)->name('packing-lists.create');
                Route::get('/packing-lists/{record}/edit', PackingListEdit::class)->name('packing-lists.edit');

                // PDF print
                Route::get('/packing-lists/{packingList}/print', [PackingListPrintController::class, 'show'])
                    ->name('packing-lists.print');


                // Bills of Exchange
                Route::get('/bill-of-exchanges', BillOfExchangesPage::class)
                    ->name('bill-of-exchanges.index');

                Route::get('/bill-of-exchanges/create', BillOfExchangeCreate::class)
                    ->name('bill-of-exchanges.create');

                Route::get('/bill-of-exchanges/{record}/edit', BillOfExchangeEdit::class)
                    ->name('bill-of-exchanges.edit');

                Route::get('/bill-of-exchanges/{billOfExchange}/print', [BillOfExchangePrintController::class, 'show'])
                    ->name('bill-of-exchanges.print');



                // Negotiation / Submission Letters
                Route::get('/negotiation-letters', NegotiationLettersPage::class)
                    ->name('negotiation-letters.index');

                Route::get('/negotiation-letters/create', NegotiationLetterCreate::class)
                    ->name('negotiation-letters.create');

                Route::get('/negotiation-letters/{record}/edit', NegotiationLetterEdit::class)
                    ->name('negotiation-letters.edit');

                Route::get(
                    '/negotiation-letters/{negotiationLetter}/print',
                    [NegotiationLetterPrintController::class, 'show']
                )->name('negotiation-letters.print');


                // Sample Invoices
                Route::get('/sample-invoices', SampleInvoicesPage::class)
                    ->name('sample-invoices.index');

                Route::get('/sample-invoices/create', SampleInvoiceCreate::class)
                    ->name('sample-invoices.create');

                Route::get('/sample-invoices/{record}/edit', SampleInvoiceEdit::class)
                    ->name('sample-invoices.edit');

                // Sample Invoice print
                Route::get('/sample-invoices/{sampleInvoice}/print', [SampleInvoicePrintController::class, 'show'])
                    ->name('sample-invoices.print');




            });
    });