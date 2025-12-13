<?php

namespace App\Http\Controllers\Print;

use App\Models\BuyerOrder;

class BuyerOrderPrintController extends BaseDocumentPrintController
{
    /**
     * ✅ Keep compatibility with routes that call ->show()
     */
    public function show(BuyerOrder $buyerOrder)
    {
        return $this->__invoke($buyerOrder);
    }

    protected function getView(): string
    {
        return 'pdf.buyer-order';
    }

    protected function getFileName($record): string
    {
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

    /**
     * Optional (only if your BaseDocumentPrintController supports this)
     */
    protected function getPaper(): array
    {
        return ['A4', 'portrait'];
    }
}