<?php

namespace App\Livewire\Admin\Trade;

use Livewire\Component;
use App\Models\ExportBundle;
use App\Models\ExportBundleDocument;
use App\Support\Trade\ExportBundleDocKeys;
use Illuminate\Support\Carbon;

use App\Models\PackingList;
use App\Models\NegotiationLetter;
use App\Models\BillOfExchange;

class ExportBundleView extends Component
{
    public ExportBundle $exportBundle;

    public function mount(ExportBundle $exportBundle): void
    {
        $this->exportBundle = $exportBundle->load([
            'commercialInvoice.customer',
            'documents.documentable',
        ]);

        // Ensure CI registry row exists (so bundle always shows CI as ready)
        $this->ensureCommercialInvoiceRegistry();
    }

    private function refreshBundle(): void
    {
        $this->exportBundle->load([
            'commercialInvoice.customer',
            'documents.documentable',
        ]);
    }

    private function docRow(string $docKey): ?ExportBundleDocument
    {
        return $this->exportBundle->documents->firstWhere('doc_key', $docKey);
    }

    private function ensureCommercialInvoiceRegistry(): void
    {
        $ci = $this->exportBundle->commercialInvoice;
        if (!$ci)
            return;

        $exists = $this->docRow(ExportBundleDocKeys::COMMERCIAL_INVOICE);
        if ($exists)
            return;

        $this->exportBundle->documents()->create([
            'doc_key' => ExportBundleDocKeys::COMMERCIAL_INVOICE,
            'documentable_type' => get_class($ci),
            'documentable_id' => $ci->id,
            'status' => 'generated',
            'generated_at' => now(),
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        $this->refreshBundle();
    }

    // -------------------------
    // GENERATE DOCUMENTS
    // -------------------------

    public function generatePackingList(): void
    {
        $key = ExportBundleDocKeys::PACKING_LIST;
        if ($this->docRow($key))
            return;

        $ci = $this->exportBundle->commercialInvoice;

        // IMPORTANT: adjust fields to match your PackingList table required columns
        $pl = PackingList::create([
            'commercial_invoice_id' => $ci->id,
            'packing_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        $this->createRegistry($key, $pl->getMorphClass(), $pl->id);
    }

    public function generateNegotiationLetter(): void
    {
        $key = ExportBundleDocKeys::NEGOTIATION_LETTER;
        if ($this->docRow($key))
            return;

        $ci = $this->exportBundle->commercialInvoice;

        // IMPORTANT: adjust fields to match your NegotiationLetter required columns
        $nl = NegotiationLetter::create([
            'commercial_invoice_id' => $ci->id,
            'letter_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        $this->createRegistry($key, $nl->getMorphClass(), $nl->id);
    }

    public function generateBoeOne(): void
    {
        $this->generateBoe('one', ExportBundleDocKeys::BOE_ONE);
    }

    public function generateBoeTwo(): void
    {
        $this->generateBoe('two', ExportBundleDocKeys::BOE_TWO);
    }

    private function generateBoe(string $type, string $key): void
    {
        if ($this->docRow($key))
            return;

        $ci = $this->exportBundle->commercialInvoice;

        // IMPORTANT: adjust fields to match your BillOfExchange required columns
        $boe = BillOfExchange::create([
            'commercial_invoice_id' => $ci->id,
            'boe_date' => now()->toDateString(),
            'boe_type' => $type, // must be 'one' or 'two'
            'status' => 'draft',
        ]);

        $this->createRegistry($key, $boe->getMorphClass(), $boe->id);
    }

    private function createRegistry(string $docKey, string $type, int $id): void
    {
        $this->exportBundle->documents()->create([
            'doc_key' => $docKey,
            'documentable_type' => $type,
            'documentable_id' => $id,
            'status' => 'generated',
            'generated_at' => now(),
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        $this->refreshBundle();
    }

    // -------------------------
    // PRINT TRACKING
    // -------------------------

    public function markPrinted(string $docKey): void
    {
        $row = $this->docRow($docKey);
        if (!$row)
            return;

        $row->update([
            'printed_at' => now(),
            'print_count' => (int) ($row->print_count ?? 0) + 1,
            'status' => 'printed',
            'updated_by' => auth()->id(),
        ]);

        $this->refreshBundle();
    }

    public function render()
    {
        $docs = [];
        foreach (ExportBundleDocKeys::required() as $key) {
            $docs[$key] = $this->docRow($key);
        }

        return view('livewire.admin.trade.export-bundle-view', [
            'docs' => $docs,
            'requiredKeys' => ExportBundleDocKeys::required(),
        ]);
    }
}