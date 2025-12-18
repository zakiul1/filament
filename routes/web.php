<?php

use App\Http\Controllers\Print\ExportBundlePrintAllController;
use App\Livewire\Admin\Trade\ExportBundleReportsPage;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

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

use App\Livewire\Admin\Trade\LcTransfersPage;
use App\Livewire\Admin\Trade\LcTransferCreate;
use App\Livewire\Admin\Trade\LcTransferEdit;

use App\Livewire\Admin\Trade\LcAmendmentsPage;
use App\Livewire\Admin\Trade\LcAmendmentCreate;
use App\Livewire\Admin\Trade\LcAmendmentEdit;

use App\Livewire\Admin\Trade\CommercialInvoicesPage;
use App\Livewire\Admin\Trade\CommercialInvoiceCreate;
use App\Livewire\Admin\Trade\CommercialInvoiceEdit;

use App\Livewire\Admin\Trade\PackingListsPage;
use App\Livewire\Admin\Trade\PackingListCreate;
use App\Livewire\Admin\Trade\PackingListEdit;

use App\Livewire\Admin\Trade\BillOfExchangesPage;
use App\Livewire\Admin\Trade\BillOfExchangeCreate;
use App\Livewire\Admin\Trade\BillOfExchangeEdit;

use App\Livewire\Admin\Trade\NegotiationLettersPage;
use App\Livewire\Admin\Trade\NegotiationLetterCreate;
use App\Livewire\Admin\Trade\NegotiationLetterEdit;

use App\Livewire\Admin\Trade\SampleInvoicesPage;
use App\Livewire\Admin\Trade\SampleInvoiceCreate;
use App\Livewire\Admin\Trade\SampleInvoiceEdit;

use App\Livewire\Admin\Trade\BuyerOrdersPage;
use App\Livewire\Admin\Trade\BuyerOrderCreate;
use App\Livewire\Admin\Trade\BuyerOrderEdit;
use App\Livewire\Admin\Trade\BuyerOrderItemAllocationsPage;
use App\Livewire\Admin\Trade\BuyerOrderSummaryPage;

use App\Livewire\Admin\Trade\ExportBundlesPage;
use App\Livewire\Admin\Trade\ExportBundleCreate;
use App\Livewire\Admin\Trade\ExportBundleView;

use App\Livewire\Admin\Reports\TradeReportsPage;
use App\Livewire\Admin\Reports\BuyerOrderSummarySelectPage;
use App\Livewire\Admin\Reports\BuyerOrderSummaryPage as ReportsBuyerOrderSummaryPage;
use App\Livewire\Admin\Reports\BuyerOrderFactoryAllocationSelectPage;

use App\Http\Controllers\Print\ProformaInvoicePrintController;
use App\Http\Controllers\Print\CommercialInvoicePrintController;
use App\Http\Controllers\Print\LcReceivePrintController;
use App\Http\Controllers\Print\LcAmendmentPrintController;
use App\Http\Controllers\Print\PackingListPrintController;
use App\Http\Controllers\Print\BillOfExchangePrintController;
use App\Http\Controllers\Print\NegotiationLetterPrintController;
use App\Http\Controllers\Print\SampleInvoicePrintController;

use App\Http\Controllers\Print\BuyerOrderPrintController;
use App\Http\Controllers\Print\BuyerOrderSummaryPrintController;
use App\Http\Controllers\Print\BuyerOrderFactoryAllocationPrintController;

use App\Http\Controllers\Print\LcTransferPrintController;
use App\Http\Controllers\Print\LcTransferLetterPrintController;

/*
|--------------------------------------------------------------------------
| Root Redirect
|--------------------------------------------------------------------------
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
| Admin Area
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |-------------------------------------------------
        | Phase 1 – System Foundation
        |-------------------------------------------------
        */
        Route::get('/users', ManageUsers::class)->name('users.index');
        Route::get('/company', CompanyProfile::class)->name('company.profile');
        Route::get('/signatories', SignatoriesPage::class)->name('signatories.index');
        Route::get('/document-sign-settings', DocumentSignSettingsPage::class)
            ->name('document-sign-settings.index');

        /*
        |-------------------------------------------------
        | Phase 2 – Master Data
        | URL: /admin/master/...
        |-------------------------------------------------
        */
        Route::prefix('master')->name('master.')->group(function () {
            Route::get('/countries', CountriesPage::class)->name('countries.index');
            Route::get('/currencies', CurrenciesPage::class)->name('currencies.index');
            Route::get('/ports', PortsPage::class)->name('ports.index');
            Route::get('/shipment-modes', ShipmentModesPage::class)->name('shipment-modes.index');
            Route::get('/incoterms', IncotermsPage::class)->name('incoterms.index');
            Route::get('/payment-terms', PaymentTermsPage::class)->name('payment-terms.index');
            Route::get('/banks', BanksPage::class)->name('banks.index');
            Route::get('/bank-branches', BankBranchesPage::class)->name('bank-branches.index');

            Route::get('/beneficiary-companies', BeneficiaryCompaniesPage::class)
                ->name('beneficiary-companies.index');
            Route::get('/beneficiary-bank-accounts', BeneficiaryBankAccountsPage::class)
                ->name('beneficiary-bank-accounts.index');

            Route::get('/couriers', CouriersPage::class)->name('couriers.index');
            Route::get('/customers', CustomersPage::class)->name('customers.index');
            Route::get('/customer-banks', CustomerBanksPage::class)->name('customer-banks.index');

            Route::get('/factories', FactoriesPage::class)->name('factories.index');
            Route::get('/factory-categories', FactoryCategoriesPage::class)->name('factory-categories.index');
            Route::get('/factory-subcategories', FactorySubcategoriesPage::class)->name('factory-subcategories.index');
            Route::get('/factory-certificates', FactoryCertificatesPage::class)->name('factory-certificates.index');
        });

        /*
        |-------------------------------------------------
        | Phase 3+ – Trade Module
        | URL: /admin/trade/...
        |-------------------------------------------------
        */
        Route::prefix('trade')->name('trade.')->group(function () {

            // Proforma Invoices
            Route::get('/proforma-invoices', ProformaInvoicesPage::class)->name('proforma-invoices.index');
            Route::get('/proforma-invoices/create', ProformaInvoiceCreate::class)->name('proforma-invoices.create');
            Route::get('/proforma-invoices/{record}/edit', ProformaInvoiceEdit::class)->name('proforma-invoices.edit');
            Route::get('/proforma-invoices/{proformaInvoice}/print', [ProformaInvoicePrintController::class, 'show'])
                ->name('proforma-invoices.print');

            // LC Receives
            Route::get('/lc-receives', LcReceivesPage::class)->name('lc-receives.index');
            Route::get('/lc-receives/create', LcReceiveCreate::class)->name('lc-receives.create');
            Route::get('/lc-receives/{record}/edit', LcReceiveEdit::class)->name('lc-receives.edit');
            Route::get('/lc-receives/{lcReceive}/print', [LcReceivePrintController::class, 'show'])
                ->name('lc-receives.print');

            // LC Transfers
            Route::get('/lc-transfers', LcTransfersPage::class)->name('lc-transfers.index');
            Route::get('/lc-transfers/create', LcTransferCreate::class)->name('lc-transfers.create');
            Route::get('/lc-transfers/{lcTransfer}/edit', LcTransferEdit::class)->name('lc-transfers.edit');
            Route::get('/lc-transfers/{lcTransfer}/print', [LcTransferPrintController::class, 'show'])
                ->name('lc-transfers.print');
            Route::get('/lc-transfers/{lcTransfer}/letter/print', [LcTransferLetterPrintController::class, 'show'])
                ->name('lc-transfers.letter.print');

            // LC Amendments
            Route::get('/lc-amendments', LcAmendmentsPage::class)->name('lc-amendments.index');
            Route::get('/lc-amendments/create', LcAmendmentCreate::class)->name('lc-amendments.create');
            Route::get('/lc-amendments/{record}/edit', LcAmendmentEdit::class)->name('lc-amendments.edit');
            Route::get('/lc-amendments/{lcAmendment}/print', [LcAmendmentPrintController::class, 'show'])
                ->name('lc-amendments.print');

            // Commercial Invoices
            Route::get('/commercial-invoices', CommercialInvoicesPage::class)->name('commercial-invoices.index');
            Route::get('/commercial-invoices/create', CommercialInvoiceCreate::class)->name('commercial-invoices.create');
            Route::get('/commercial-invoices/{record}/edit', CommercialInvoiceEdit::class)->name('commercial-invoices.edit');
            Route::get('/commercial-invoices/{commercialInvoice}/print', [CommercialInvoicePrintController::class, 'show'])
                ->name('commercial-invoices.print');

            // Packing Lists
            Route::get('/packing-lists', PackingListsPage::class)->name('packing-lists.index');
            Route::get('/packing-lists/create', PackingListCreate::class)->name('packing-lists.create');
            Route::get('/packing-lists/{record}/edit', PackingListEdit::class)->name('packing-lists.edit');
            Route::get('/packing-lists/{packingList}/print', [PackingListPrintController::class, 'show'])
                ->name('packing-lists.print');

            // Bills of Exchange
            Route::get('/bill-of-exchanges', BillOfExchangesPage::class)->name('bill-of-exchanges.index');
            Route::get('/bill-of-exchanges/create', BillOfExchangeCreate::class)->name('bill-of-exchanges.create');
            Route::get('/bill-of-exchanges/{record}/edit', BillOfExchangeEdit::class)->name('bill-of-exchanges.edit');
            Route::get('/bill-of-exchanges/{billOfExchange}/print', [BillOfExchangePrintController::class, 'show'])
                ->name('bill-of-exchanges.print');

            // Negotiation Letters
            Route::get('/negotiation-letters', NegotiationLettersPage::class)->name('negotiation-letters.index');
            Route::get('/negotiation-letters/create', NegotiationLetterCreate::class)->name('negotiation-letters.create');
            Route::get('/negotiation-letters/{record}/edit', NegotiationLetterEdit::class)->name('negotiation-letters.edit');
            Route::get(
                '/negotiation-letters/{negotiationLetter}/print',
                [NegotiationLetterPrintController::class, 'show']
            )->name('negotiation-letters.print');





            // Sample Invoices
            Route::get('/sample-invoices', SampleInvoicesPage::class)->name('sample-invoices.index');
            Route::get('/sample-invoices/create', SampleInvoiceCreate::class)->name('sample-invoices.create');
            Route::get('/sample-invoices/{record}/edit', SampleInvoiceEdit::class)->name('sample-invoices.edit');
            Route::get('/sample-invoices/{sampleInvoice}/print', [SampleInvoicePrintController::class, 'show'])
                ->name('sample-invoices.print');

            // Buyer Orders
            Route::get('/buyer-orders', BuyerOrdersPage::class)->name('buyer-orders.index');
            Route::get('/buyer-orders/create', BuyerOrderCreate::class)->name('buyer-orders.create');
            Route::get('/buyer-orders/{record}/edit', BuyerOrderEdit::class)->name('buyer-orders.edit');

            Route::get('/buyer-order-items/{item}/allocations', BuyerOrderItemAllocationsPage::class)
                ->name('buyer-order-items.allocations');

            Route::get('/buyer-orders/{buyerOrder}/print', [BuyerOrderPrintController::class, 'show'])
                ->name('buyer-orders.print');

            Route::get('/buyer-orders/{buyerOrder}/summary', BuyerOrderSummaryPage::class)
                ->name('buyer-orders.summary.show');
            Route::get('/buyer-orders/{buyerOrder}/summary/print', [BuyerOrderSummaryPrintController::class, 'show'])
                ->name('buyer-orders.summary.print');

            Route::get('/buyer-orders/{buyerOrder}/factory-allocation/print', [BuyerOrderFactoryAllocationPrintController::class, 'show'])
                ->name('buyer-orders.factory-allocation.print');

            // ✅ Export Bundles
            Route::get('/export-bundles', ExportBundlesPage::class)->name('export-bundles.index');
            Route::get('/export-bundles/create', ExportBundleCreate::class)->name('export-bundles.create');
            // inside Route::prefix('trade')->name('trade.')->group(...)
            Route::get('/export-bundles/reports', ExportBundleReportsPage::class)
                ->name('export-bundles.reports');
            Route::get('/export-bundles/{exportBundle}', ExportBundleView::class)->name('export-bundles.show');
            Route::get('/export-bundles/{exportBundle}/print-all', [ExportBundlePrintAllController::class, 'zip'])
                ->name('export-bundles.print-all');


            // ✅ Shipments
            Route::get('/shipments', \App\Livewire\Admin\Trade\ShipmentsPage::class)->name('shipments.index');
            Route::get('/shipments/create', \App\Livewire\Admin\Trade\ShipmentCreate::class)->name('shipments.create');
            Route::get('/shipments/{shipment}/edit', \App\Livewire\Admin\Trade\ShipmentEdit::class)->name('shipments.edit');




        });

        /*
        |-------------------------------------------------
        | Reports Module
        | URL: /admin/reports/...
        |-------------------------------------------------
        */
        Route::prefix('reports')->name('reports.')->group(function () {

            Route::get('/trade', TradeReportsPage::class)->name('trade.index');

            // Buyer Order Summary (Reports)
            Route::get('/buyer-orders/summary', BuyerOrderSummarySelectPage::class)
                ->name('buyer-orders.summary.select');

            Route::get('/buyer-orders/{buyerOrder}/summary', ReportsBuyerOrderSummaryPage::class)
                ->name('buyer-orders.summary.show');

            Route::get('/buyer-orders/{buyerOrder}/summary/print', [BuyerOrderSummaryPrintController::class, 'show'])
                ->name('buyer-orders.summary.print');

            // Factory Allocation (select + print)
            Route::get('/buyer-orders/factory-allocation', BuyerOrderFactoryAllocationSelectPage::class)
                ->name('buyer-orders.factory-allocation.select');

            Route::get('/buyer-orders/{buyerOrder}/factory-allocation/print', [BuyerOrderFactoryAllocationPrintController::class, 'show'])
                ->name('buyer-orders.factory-allocation.print');


        });
    });