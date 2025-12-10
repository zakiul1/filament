<?php

namespace App\Http\Controllers\Print;

class LcReceivePrintController extends BaseDocumentPrintController
{
    protected function getView(): string
    {
        return 'pdf.lc-receive';
    }

    protected function getFileName($record): string
    {
        return 'LC-' . $record->lc_number . '.pdf';
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
            'portOfDischarge'
        ];
    }
}