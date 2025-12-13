<?php

namespace App\Http\Controllers\Print;

use App\Http\Controllers\Controller;
use App\Models\LcTransfer;
use Barryvdh\DomPDF\Facade\Pdf;

class LcTransferLetterPrintController extends Controller
{
    public function show(LcTransfer $lcTransfer)
    {
        $record = $lcTransfer->load([
            'factory.country',
            'currency',
            'lcReceive.customer',
            'lcReceive.customerBank.bankBranch.bank',   // ✅ IMPORTANT
            'lcReceive.beneficiaryCompany',
            'lcReceive.beneficiaryBankAccount',
            'lcReceive.currency',
            'lcReceive.proformaInvoice.items',
        ]);

        $lc = $record->lcReceive;
        $pi = $lc?->proformaInvoice;
        $items = $pi?->items ?? collect();

        $pdf = Pdf::loadView('pdf.lc-transfer-letter', [
            'record' => $record,
            'lc' => $lc,
            'pi' => $pi,
            'items' => $items,
        ])->setPaper('a4', 'portrait');

        $filename = 'LC_Transfer_Letter_' . ($record->transfer_no ?? $record->id) . '.pdf';

        return $pdf->stream($filename);
    }
}