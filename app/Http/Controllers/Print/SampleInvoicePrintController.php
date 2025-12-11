<?php

namespace App\Http\Controllers\Print;

use App\Http\Controllers\Controller;
use App\Models\SampleInvoice;
use Barryvdh\DomPDF\Facade\Pdf;

class SampleInvoicePrintController extends Controller
{
    public function show(SampleInvoice $sampleInvoice)
    {
        $record = $sampleInvoice->load([
            'items.factorySubcategory',
            'customer.country',
            'beneficiaryCompany',
            'currency',
            'incoterm',
            'shipmentMode',
            'portOfLoading',
            'portOfDischarge',
            'courier',
        ]);

        $pdf = Pdf::loadView('pdf.sample-invoice', [
            'record' => $record,
        ])->setPaper('A4', 'portrait');

        $filename = 'SampleInvoice_' . ($record->sample_number ?? $record->id) . '.pdf';

        return $pdf->stream($filename);
        // Or ->download($filename);
    }
}