<?php

namespace App\Http\Controllers\Print;

class LcAmendmentPrintController extends BaseDocumentPrintController
{
    protected function getView(): string
    {
        return 'pdf.lc-amendment';
    }

    protected function getFileName($record): string
    {
        return 'LCA-' . $record->amendment_number . '.pdf';
    }

    protected function getRelations(): array
    {
        return [
            'lcReceive.customer.country',
            'lcReceive.beneficiaryCompany',
            'lcReceive.currency',
        ];
    }
}