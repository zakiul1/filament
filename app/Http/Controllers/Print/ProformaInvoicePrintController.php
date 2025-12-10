<?php

namespace App\Http\Controllers\Print;

use App\Models\ProformaInvoice;

class ProformaInvoicePrintController extends BaseDocumentPrintController
{
    protected function getView(): string
    {
        return 'pdf.proforma-invoice';
    }

    protected function getFileName($record): string
    {
        return 'PI-' . $record->pi_number . '.pdf';
    }

    protected function getRelations(): array
    {
        return [
            'customer.country',
            'beneficiaryCompany',
            'currency',
            'incoterm',
            'shipmentMode',
            'portOfLoading',
            'portOfDischarge',
            'items'
        ];
    }
}