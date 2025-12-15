<?php

namespace App\Http\Controllers\Print;

use App\Models\BillOfExchange;
use Illuminate\Database\Eloquent\Model;

class BillOfExchangePrintController extends BaseDocumentPrintController
{
    protected function getView(): string
    {
        return 'pdf.bill-of-exchange';
    }

    protected function getFileName(Model $record): string
    {
        /** @var BillOfExchange $record */
        return 'BOE_' . ($record->boe_number ?? $record->id) . '.pdf';
    }

    protected function getRelations(): array
    {
        return [
            'customer.country',
            'beneficiaryCompany',
            'currency',
            'lcReceive',
            'commercialInvoice',
        ];
    }
}