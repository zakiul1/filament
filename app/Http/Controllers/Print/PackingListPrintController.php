<?php

namespace App\Http\Controllers\Print;

use App\Http\Controllers\Controller;
use App\Models\PackingList;
use Barryvdh\DomPDF\Facade\Pdf;

class PackingListPrintController extends Controller
{
    public function show(PackingList $packingList)
    {
        $record = $packingList->load([
            'items',
            'commercialInvoice.customer.country',
            'beneficiaryCompany',
        ]);

        $pdf = Pdf::loadView('pdf.packing-list', [
            'record' => $record,
        ])->setPaper('A4', 'portrait');

        $filename = 'PackingList_' . ($record->pl_number ?? $record->id) . '.pdf';

        return $pdf->stream($filename);
        // or ->download($filename);
    }
}