<?php

namespace App\Http\Controllers\Print;

use App\Http\Controllers\Controller;
use App\Models\NegotiationLetter;
use Barryvdh\DomPDF\Facade\Pdf;

class NegotiationLetterPrintController extends Controller
{
    public function show(NegotiationLetter $negotiationLetter)
    {
        $record = $negotiationLetter->load([
            'commercialInvoice.customer',
            'beneficiaryCompany',
            'currency',
        ]);

        $pdf = Pdf::loadView('pdf.negotiation-letter', [
            'record' => $record, // ✅ use $record in blade
        ])->setPaper('A4', 'portrait');

        $filename = 'NegotiationLetter_' . ($record->letter_number ?? $record->id) . '.pdf';

        return $pdf->stream($filename);
    }
}