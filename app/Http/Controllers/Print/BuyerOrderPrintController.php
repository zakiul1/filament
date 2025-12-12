<?php

namespace App\Http\Controllers\Print;

use App\Http\Controllers\Controller;
use App\Models\BuyerOrder;
use Barryvdh\DomPDF\Facade\Pdf;

class BuyerOrderPrintController extends Controller
{
    public function show(BuyerOrder $buyerOrder)
    {
        $record = $buyerOrder->load([
            'customer.country',
            'beneficiaryCompany',
            'items.allocations.factory',
        ]);

        $pdf = Pdf::loadView('pdf.buyer-order', [
            'record' => $record,
        ])->setPaper('A4', 'portrait');

        $filename = 'BuyerOrder_' . ($record->order_number ?? $record->id) . '.pdf';

        return $pdf->stream($filename);
    }
}