<?php

namespace App\Http\Controllers\Print;

use App\Http\Controllers\Controller;
use App\Models\BillOfExchange;
use Barryvdh\DomPDF\Facade\Pdf;

class BillOfExchangePrintController extends Controller
{
    public function show(BillOfExchange $billOfExchange)
    {
        $boe = $billOfExchange->load([
            'customer.country',
            'beneficiaryCompany',
            'currency',
            'lcReceive',
            'commercialInvoice',
        ]);

        $pdf = Pdf::loadView('pdf.bill-of-exchange', [
            'boe' => $boe,
        ])->setPaper('A4', 'portrait');

        $filename = 'BOE_' . ($boe->boe_number ?? $boe->id) . '.pdf';

        return $pdf->stream($filename);
    }
}