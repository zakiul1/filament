<?php

namespace App\Http\Controllers\Print;

use App\Http\Controllers\Controller;
use App\Models\ExportBundle;
use App\Support\Trade\ExportBundleDocKeys;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use ZipArchive;

class ExportBundlePrintAllController extends Controller
{
    public function zip(ExportBundle $exportBundle)
    {
        $exportBundle->load([
            'commercialInvoice.customer.country',
            'documents.documentable',
        ]);

        // ✅ validate all docs exist
        foreach (ExportBundleDocKeys::required() as $key) {
            $row = $exportBundle->documents->firstWhere('doc_key', $key);
            if (!$row || !$row->documentable_id) {
                return back()->with('error', "Missing document: {$key}. Generate all documents first.");
            }
        }

        // ✅ temp dir
        $tmpDir = storage_path('app/tmp/export-bundles/' . $exportBundle->id);
        if (!File::exists($tmpDir)) {
            File::makeDirectory($tmpDir, 0755, true);
        }

        $zipName = $exportBundle->bundle_no . '-PRINT-ALL.zip';
        $zipPath = $tmpDir . '/' . $zipName;

        if (File::exists($zipPath)) {
            File::delete($zipPath);
        }

        // ✅ generate pdf files (DomPDF output)
        $pdfFiles = $this->generatePdfFiles($exportBundle, $tmpDir);

        // ✅ create zip
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
            return back()->with('error', 'Could not create ZIP file.');
        }

        foreach ($pdfFiles as $filePath => $zipFileName) {
            if (File::exists($filePath)) {
                $zip->addFile($filePath, $zipFileName);
            }
        }

        $zip->close();

        // ✅ update print tracking
        foreach (ExportBundleDocKeys::required() as $key) {
            $row = $exportBundle->documents->firstWhere('doc_key', $key);
            if (!$row)
                continue;

            $row->update([
                'printed_at' => now(),
                'print_count' => (int) ($row->print_count ?? 0) + 1,
                'status' => 'printed',
                'updated_by' => auth()->id(),
            ]);
        }

        return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
    }

    private function generatePdfFiles(ExportBundle $exportBundle, string $tmpDir): array
    {
        $files = [];

        foreach (ExportBundleDocKeys::required() as $docKey) {
            $row = $exportBundle->documents->firstWhere('doc_key', $docKey);
            $record = $row?->documentable;

            if (!$record)
                continue;

            // ✅ Choose view + relations + filename
            if ($docKey === ExportBundleDocKeys::COMMERCIAL_INVOICE) {
                $record->load(['customer.country']);
                $bytes = Pdf::loadView('pdf.commercial-invoice', ['record' => $record])
                    ->setPaper('A4', 'portrait')
                    ->output();

                $fileName = '01-Commercial-Invoice.pdf';
            } elseif ($docKey === ExportBundleDocKeys::PACKING_LIST) {
                $record->load(['items', 'commercialInvoice.customer.country', 'beneficiaryCompany']);
                $bytes = Pdf::loadView('pdf.packing-list', ['record' => $record])
                    ->setPaper('A4', 'portrait')
                    ->output();

                $fileName = '02-Packing-List.pdf';
            } elseif ($docKey === ExportBundleDocKeys::NEGOTIATION_LETTER) {
                $record->load(['commercialInvoice.customer.country', 'beneficiaryCompany', 'currency']);
                $bytes = Pdf::loadView('pdf.negotiation-letter', ['record' => $record])
                    ->setPaper('A4', 'portrait')
                    ->output();

                $fileName = '03-Negotiation-Letter.pdf';
            } elseif ($docKey === ExportBundleDocKeys::BOE_ONE) {
                $record->load(['customer.country', 'beneficiaryCompany', 'currency']);
                $bytes = Pdf::loadView('pdf.bill-of-exchange', ['boe' => $record])
                    ->setPaper('A4', 'portrait')
                    ->output();

                $fileName = '04-BOE-1st.pdf';
            } elseif ($docKey === ExportBundleDocKeys::BOE_TWO) {
                $record->load(['customer.country', 'beneficiaryCompany', 'currency']);
                $bytes = Pdf::loadView('pdf.bill-of-exchange', ['boe' => $record])
                    ->setPaper('A4', 'portrait')
                    ->output();

                $fileName = '05-BOE-2nd.pdf';
            } else {
                continue;
            }

            $path = $tmpDir . '/' . $fileName;
            File::put($path, $bytes);

            $files[$path] = $fileName;
        }

        return $files;
    }
}