<?php

namespace App\Http\Controllers\Print;

use App\Http\Controllers\Controller;
use App\Models\LcTransfer;
use Barryvdh\DomPDF\Facade\Pdf;

class LcTransferPrintController extends Controller
{
    public function show(LcTransfer $lcTransfer)
    {
        $record = $lcTransfer->load([
            'lcReceive.customer',
            'lcReceive.customerBank.bankBranch.bank', // ✅ correct
            'lcReceive.currency',
            'factory',
            'currency',
        ]);


        $pdf = Pdf::loadView('pdf.lc-transfer', [
            'record' => $record,
        ])->setPaper('a4', 'portrait');

        $filename = 'LC_Transfer_' . ($record->transfer_no ?? $record->id) . '.pdf';

        return $pdf->stream($filename);
    }
}