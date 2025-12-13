<?php

namespace App\Http\Controllers\Print;

use App\Http\Controllers\Controller;
use App\Models\BillOfExchange;
use App\Models\ExportBundle;
use App\Models\NegotiationLetter;
use App\Models\PackingList;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use ZipArchive;

class ExportBundlePrintAllController extends Controller
{
    public function zip(ExportBundle $exportBundle)
    {
        $exportBundle->load(['commercialInvoice.customer']);

        $ci = $exportBundle->commercialInvoice;
        abort_if(!$ci, 404, 'Commercial Invoice not found for this Export Bundle.');

        $packingList = PackingList::query()
            ->where('commercial_invoice_id', $ci->id)
            ->latest('id')
            ->first();

        $negotiation = NegotiationLetter::query()
            ->where('commercial_invoice_id', $ci->id)
            ->latest('id')
            ->first();

        $boes = BillOfExchange::query()
            ->where('commercial_invoice_id', $ci->id)
            ->orderBy('boe_type')
            ->orderBy('id')
            ->get();

        $boeOne = $boes->firstWhere('boe_type', 'one') ?? $boes->first();
        $boeTwo = $boes->firstWhere('boe_type', 'two') ?? ($boes->count() > 1 ? $boes->get(1) : null);

        $tmpDir = storage_path('app/tmp/' . Str::uuid());
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0777, true);
        }

        $zipPath = $tmpDir . '/ExportBundle-' . ($exportBundle->bundle_no ?? $exportBundle->id) . '.zip';

        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // helper to add pdf to zip
        $addPdf = function (string $zipName, string $view, $record) use ($zip, $tmpDir) {
            if (!$record)
                return;

            $pdfPath = $tmpDir . '/' . $zipName;

            Pdf::loadView($view, ['record' => $record])
                ->setPaper('A4', 'portrait')
                ->save($pdfPath);

            $zip->addFile($pdfPath, $zipName);
        };

        // ✅ Add documents (ensure these blades exist and use $record)
        $addPdf('01-Commercial-Invoice.pdf', 'pdf.commercial-invoice', $ci);
        $addPdf('02-Packing-List.pdf', 'pdf.packing-list', $packingList);
        $addPdf('03-Negotiation-Letter.pdf', 'pdf.negotiation-letter', $negotiation);
        $addPdf('04-Bill-of-Exchange-One.pdf', 'pdf.bill-of-exchange', $boeOne);
        $addPdf('05-Bill-of-Exchange-Two.pdf', 'pdf.bill-of-exchange', $boeTwo);

        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}