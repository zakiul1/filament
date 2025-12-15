<?php

namespace App\Http\Controllers\Print;

use App\Models\BuyerOrder;
use Illuminate\Database\Eloquent\Model;

class BuyerOrderPrintController extends BaseDocumentPrintController
{
    protected function getView(): string
    {
        return 'pdf.buyer-order';
    }

    protected function getFileName(Model $record): string
    {
        /** @var BuyerOrder $record */
        return 'BuyerOrder_' . ($record->order_number ?? $record->id) . '.pdf';
    }

    protected function getRelations(): array
    {
        return [
            'customer.country',
            'beneficiaryCompany',
            'items.allocations.factory',
        ];
    }
}