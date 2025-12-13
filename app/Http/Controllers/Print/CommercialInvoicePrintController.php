<?php

namespace App\Http\Controllers\Print;

use App\Models\CommercialInvoice;
use Illuminate\Database\Eloquent\Model;

class CommercialInvoicePrintController extends BaseDocumentPrintController
{
    public function show(CommercialInvoice $commercialInvoice)
    {
        return $this->streamPdf($commercialInvoice);
    }

    protected function getView(): string
    {
        return 'pdf.commercial-invoice';
    }

    protected function getFileName(Model $record): string
    {
        return 'CI-' . ($record->invoice_number ?? $record->id) . '.pdf';
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
            'items',
        ];
    }
}