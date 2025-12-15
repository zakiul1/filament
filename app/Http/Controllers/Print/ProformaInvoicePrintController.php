<?php

namespace App\Http\Controllers\Print;

use App\Models\ProformaInvoice;
use Illuminate\Database\Eloquent\Model;

class ProformaInvoicePrintController extends BaseDocumentPrintController
{
    protected function getView(): string
    {
        return 'pdf.proforma-invoice';
    }

    protected function getFileName(Model $record): string
    {
        /** @var ProformaInvoice $record */
        $no = $record->pi_number ?? $record->id;

        return 'PI-' . $no . '.pdf';
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