<?php

namespace App\Http\Controllers\Print;

use App\Models\NegotiationLetter;

class NegotiationLetterPrintController extends BaseDocumentPrintController
{
    // ✅ Keeps compatibility with routes using ->show()
    public function show(NegotiationLetter $negotiationLetter)
    {
        return $this->__invoke($negotiationLetter);
    }

    protected function getView(): string
    {
        return 'pdf.negotiation-letter';
    }

    protected function getFileName($record): string
    {
        return 'NegotiationLetter_' . $record->letter_number . '.pdf';
    }

    protected function getRelations(): array
    {
        return [
            'commercialInvoice.customer',
            'beneficiaryCompany',
            'currency',
        ];
    }
}