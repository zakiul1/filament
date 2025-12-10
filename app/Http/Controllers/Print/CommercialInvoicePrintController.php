<?php

namespace App\Http\Controllers\Print;

class CommercialInvoicePrintController extends BaseDocumentPrintController
{
    protected function getView(): string
    {
        return 'pdf.commercial-invoice';
    }

    protected function getFileName($record): string
    {
        return 'CI-' . $record->invoice_number . '.pdf';
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