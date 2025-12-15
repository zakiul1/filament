<?php

namespace App\Http\Controllers\Print;

use App\Models\LcTransfer;
use Illuminate\Database\Eloquent\Model;

class LcTransferLetterPrintController extends BaseDocumentPrintController
{
    protected function getView(): string
    {
        return 'pdf.lc-transfer-letter';
    }

    protected function getFileName(Model $record): string
    {
        /** @var LcTransfer $record */
        return 'LC_Transfer_Letter_' . ($record->transfer_no ?? $record->id) . '.pdf';
    }

    protected function getRelations(): array
    {
        return [
            'factory.country',
            'currency',
            'lcReceive.customer',
            'lcReceive.customerBank.bankBranch.bank',
            'lcReceive.beneficiaryCompany',
            'lcReceive.beneficiaryBankAccount',
            'lcReceive.currency',
            'lcReceive.proformaInvoice.items',
        ];
    }

    /**
     * Extra data needed by the Blade
     */
    protected function getExtraViewData(Model $record): array
    {
        /** @var LcTransfer $record */
        $lc = $record->lcReceive;
        $pi = $lc?->proformaInvoice;
        $items = $pi?->items ?? collect();

        return [
            'lc' => $lc,
            'pi' => $pi,
            'items' => $items,
        ];
    }
}