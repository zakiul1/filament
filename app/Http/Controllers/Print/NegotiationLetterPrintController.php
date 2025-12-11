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
            'record' => $record,
        ])->setPaper('A4');

        return $pdf->stream('NegotiationLetter_' . $record->letter_number . '.pdf');
    }
}