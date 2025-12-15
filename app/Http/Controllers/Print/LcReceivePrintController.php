<?php

namespace App\Http\Controllers\Print;

use App\Models\LcReceive;
use Illuminate\Database\Eloquent\Model;

class LcReceivePrintController extends BaseDocumentPrintController
{
    protected function getView(): string
    {
        return 'pdf.lc-receive';
    }

    protected function getFileName(Model $record): string
    {
        /** @var LcReceive $record */
        $no = $record->lc_number ?? $record->id;

        return 'LC-' . $no . '.pdf';
    }

    protected function getRelations(): array
    {
        return [
            'customer.country',
            'beneficiaryCompany',
            'currency',
            'customerBank.bankBranch.bank',
            'beneficiaryBankAccount.bankBranch.bank',
            'incoterm',
            'shipmentMode',
            'portOfLoading',
            'portOfDischarge',
        ];
    }
}