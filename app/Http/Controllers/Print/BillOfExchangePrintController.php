<?php

namespace App\Http\Controllers\Print;

use App\Http\Controllers\Controller;
use App\Models\BillOfExchange;
use Barryvdh\DomPDF\Facade\Pdf;

class BillOfExchangePrintController extends Controller
{
    public function show(BillOfExchange $billOfExchange)
    {
        // Eager-load all relations needed in the PDF
        $boe = $billOfExchange->load([
            'customer.country',
            'beneficiaryCompany',
            'currency',
            'lcReceive',
            'commercialInvoice',
        ]);

        // Render the PDF view
        $pdf = Pdf::loadView('pdf.bill-of-exchange', [
            'boe' => $boe,   // <-- matches the variable used in the Blade template
        ])->setPaper('A4', 'portrait');

        $filename = 'BOE_' . ($boe->boe_number ?? $boe->id) . '.pdf';

        return $pdf->stream($filename);
        // or ->download($filename);
    }
}