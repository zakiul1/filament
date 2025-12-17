<?php

namespace App\Livewire\Admin\Trade;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Models\ExportBundle;
use App\Models\ExportBundleDocument;
use App\Support\Trade\ExportBundleDocKeys;

use App\Models\PackingList;
use App\Models\NegotiationLetter;
use App\Models\BillOfExchange;
use App\Models\CompanySetting;

class ExportBundleView extends Component
{
    use WithFileUploads;
    use AuthorizesRequests;

    public ExportBundle $exportBundle;

    /** @var array<int,string> */
    public array $lockErrors = [];

    /** @var array<int,string> */
    public array $submitErrors = [];

    // -------------------------
    // Step 5 (Bank Submission)
    // -------------------------
    public ?string $submission_ref = null;

    /** @var TemporaryUploadedFile|string|null */
    public $bank_ack_file = null;

    // -------------------------
    // Step 7 (Timeline / Bank workflow)
    // -------------------------
    public ?string $courier_ref = null;
    public ?string $bank_ref = null;

    public function mount(ExportBundle $exportBundle): void
    {
        $this->exportBundle = $exportBundle->load([
            'commercialInvoice.customer',
            'documents.documentable',
            'submittedBy',
            'lockedBy',

            // ✅ Step 7
            'events.user',
            'shipment',
        ]);

        $this->ensureCommercialInvoiceRegistry();
        $this->ensureRequiredRows();
        $this->refreshBundle();

        $this->submission_ref = $this->exportBundle->submission_ref;
    }

    private function refreshBundle(): void
    {
        $this->exportBundle->refresh()->load([
            'commercialInvoice.customer',
            'documents.documentable',
            'submittedBy',
            'lockedBy',

            // ✅ Step 7
            'events.user',
            'shipment',
        ]);
    }

    private function isLocked(): bool
    {
        return (bool) $this->exportBundle->locked_at;
    }

    private function isSubmitted(): bool
    {
        return (bool) $this->exportBundle->submitted_at;
    }

    private function docRow(string $docKey): ?ExportBundleDocument
    {
        return $this->exportBundle->documents->firstWhere('doc_key', $docKey);
    }

    // -------------------------
    // ✅ Step 7: Timeline Event Logger
    // -------------------------
    private function logEvent(string $event, ?string $ref = null, ?string $notes = null): void
    {
        // if relation doesn't exist yet, this will error - but Step7 requires it.
        $this->exportBundle->events()->create([
            'event' => $event,
            'event_at' => now(),
            'ref' => $ref,
            'notes' => $notes,
            'user_id' => Auth::id(),
        ]);
    }

    // -------------------------
    // Helpers
    // -------------------------
    private function defaultPlaceOfDrawing(): ?string
    {
        $company = CompanySetting::query()->first();
        if (!$company) {
            return null;
        }

        $addr = trim((string) ($company->address ?? ''));
        if ($addr === '') {
            return null;
        }

        $firstLine = trim(strtok($addr, "\n"));
        return $firstLine !== '' ? $firstLine : $addr;
    }

    // -------------------------
    // Validate before Lock (Step 1.9)
    // -------------------------
    private function validateBeforeLock(): array
    {
        $errors = [];

        foreach (ExportBundleDocKeys::required() as $key) {
            $row = $this->docRow($key);
            if (!$row || !$row->documentable_id) {
                $errors[] = strtoupper(str_replace('_', ' ', $key)) . " is missing. Please Generate it first.";
            }
        }

        if (!empty($errors)) {
            return $errors;
        }

        $ci = $this->exportBundle->commercialInvoice;

        // Packing List must have items
        $plRow = $this->docRow(ExportBundleDocKeys::PACKING_LIST);
        if ($plRow?->documentable_id) {
            $pl = PackingList::withCount('items')->find($plRow->documentable_id);
            if (!$pl || (int) $pl->items_count < 1) {
                $errors[] = "PACKING LIST has no items. Add at least 1 item before locking.";
            }
        }

        // Negotiation Letter checks
        $nlRow = $this->docRow(ExportBundleDocKeys::NEGOTIATION_LETTER);
        if ($nlRow?->documentable_id) {
            $nl = NegotiationLetter::find($nlRow->documentable_id);
            if ($nl) {
                if (blank($nl->bank_name)) {
                    $errors[] = "NEGOTIATION LETTER: bank_name is required before locking.";
                }
                if (blank($nl->bank_branch)) {
                    $errors[] = "NEGOTIATION LETTER: bank_branch is required before locking.";
                }
                if (blank($nl->swift_code)) {
                    $errors[] = "NEGOTIATION LETTER: swift_code is required before locking.";
                }

                if ($ci && (int) $nl->currency_id !== (int) ($ci->currency_id ?? 0)) {
                    $errors[] = "NEGOTIATION LETTER currency must match Commercial Invoice currency.";
                }
            }
        }

        // BOE checks
        foreach ([ExportBundleDocKeys::BOE_ONE, ExportBundleDocKeys::BOE_TWO] as $boeKey) {
            $boeRow = $this->docRow($boeKey);
            if (!$boeRow?->documentable_id) {
                continue;
            }

            $boe = BillOfExchange::find($boeRow->documentable_id);
            if (!$boe) {
                continue;
            }

            if (blank($boe->amount_in_words)) {
                $errors[] = strtoupper($boeKey) . ": amount_in_words is required before locking.";
            }

            if (blank($boe->place_of_drawing)) {
                $errors[] = strtoupper($boeKey) . ": place_of_drawing is required before locking.";
            }

            if ($ci && (int) $boe->currency_id !== (int) ($ci->currency_id ?? 0)) {
                $errors[] = strtoupper($boeKey) . ": currency must match Commercial Invoice currency.";
            }

            if (blank($boe->drawee_bank_name)) {
                $errors[] = strtoupper($boeKey) . ": drawee_bank_name is required before locking.";
            }

            if (blank($boe->drawee_bank_address)) {
                $errors[] = strtoupper($boeKey) . ": drawee_bank_address is required before locking.";
            }
        }

        return $errors;
    }

    // -------------------------
    // Validate before Submit (Step 5)
    // -------------------------
    private function validateBeforeSubmit(): array
    {
        $errors = [];

        if (!$this->isLocked()) {
            $errors[] = "Bundle must be LOCKED before submitting to bank.";
            return $errors;
        }

        if ($this->isSubmitted()) {
            $errors[] = "Bundle already submitted.";
            return $errors;
        }

        foreach (ExportBundleDocKeys::required() as $key) {
            $row = $this->docRow($key);

            if (!$row || !$row->documentable_id) {
                $errors[] = strtoupper(str_replace('_', ' ', $key)) . " is missing. Generate it first.";
                continue;
            }

            // Require printed before submit
            if (empty($row->printed_at) && (int) ($row->print_count ?? 0) < 1) {
                $errors[] = strtoupper(str_replace('_', ' ', $key)) . " has not been printed yet.";
            }
        }

        return $errors;
    }

    // -------------------------
    // Lock / Unlock (Policy protected)
    // -------------------------
    public function lockBundle(): void
    {
        $this->authorize('lock', $this->exportBundle);

        if ($this->isLocked() || $this->isSubmitted()) {
            return;
        }

        $this->lockErrors = $this->validateBeforeLock();
        $this->submitErrors = [];

        if (!empty($this->lockErrors)) {
            return;
        }

        $this->exportBundle->update([
            'status' => 'locked',
            'locked_at' => now(),
            'locked_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        // ✅ Step 7 log
        $this->logEvent('locked');

        $this->refreshBundle();
    }

    public function unlockBundle(): void
    {
        $this->authorize('unlock', $this->exportBundle);

        if ($this->isSubmitted()) {
            abort(403, 'Cannot unlock a submitted bundle.');
        }

        $this->exportBundle->update([
            'status' => 'generated',
            'locked_at' => null,
            'locked_by' => null,
            'updated_by' => Auth::id(),
        ]);

        // ✅ Step 7 log
        $this->logEvent('unlocked');

        $this->lockErrors = [];
        $this->submitErrors = [];
        $this->refreshBundle();
    }

    // -------------------------
    // Submit / Unsubmit (Policy protected)
    // -------------------------
    public function submitToBank(): void
    {
        $this->authorize('submitToBank', $this->exportBundle);

        if ($this->isSubmitted()) {
            return;
        }

        // ✅ validate inputs (PDF optional)
        $this->validate([
            'submission_ref' => ['nullable', 'string', 'max:120'],
            'bank_ack_file' => ['nullable', 'file', 'mimes:pdf', 'max:5120'], // 5MB
        ]);

        $this->submitErrors = $this->validateBeforeSubmit();
        $this->lockErrors = [];

        if (!empty($this->submitErrors)) {
            return;
        }

        $path = $this->exportBundle->bank_ack_file_path;

        if ($this->bank_ack_file instanceof TemporaryUploadedFile) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }

            $path = $this->bank_ack_file->store('export-bundles/bank-ack', 'public');
        }

        $this->exportBundle->update([
            'submitted_at' => now(),
            'submitted_by' => Auth::id(),
            'submission_ref' => $this->submission_ref,
            'bank_ack_file_path' => $path,
            'status' => 'submitted',
            'updated_by' => Auth::id(),
        ]);

        // ✅ Step 7 log
        $this->logEvent('submitted', $this->submission_ref);

        $this->bank_ack_file = null;
        $this->submitErrors = [];
        $this->refreshBundle();
    }

    public function unsubmitFromBank(): void
    {
        $this->authorize('unsubmitFromBank', $this->exportBundle);

        if (!$this->isSubmitted()) {
            return;
        }

        $this->exportBundle->update([
            'submitted_at' => null,
            'submitted_by' => null,
            'status' => 'locked',
            'updated_by' => Auth::id(),
        ]);

        // ✅ Step 7 log
        $this->logEvent('unsubmitted');

        $this->submitErrors = [];
        $this->lockErrors = [];
        $this->refreshBundle();
    }

    public function downloadBankAck()
    {
        $this->authorize('downloadBankAck', $this->exportBundle);

        if (!$this->exportBundle->bank_ack_file_path) {
            return null;
        }

        return Storage::disk('public')->download($this->exportBundle->bank_ack_file_path);
    }

    // -------------------------
    // ✅ Step 7: Post-submit actions
    // -------------------------
    public function markCouriered(): void
    {
        $this->authorize('submitToBank', $this->exportBundle);

        if (!$this->isSubmitted()) {
            return;
        }

        $this->validate([
            'courier_ref' => ['nullable', 'string', 'max:120'],
        ]);

        $this->logEvent('couriered', $this->courier_ref);
        $this->courier_ref = null;

        $this->refreshBundle();
    }

    public function markBankAccepted(): void
    {
        $this->authorize('submitToBank', $this->exportBundle);

        if (!$this->isSubmitted()) {
            return;
        }

        $this->validate([
            'bank_ref' => ['nullable', 'string', 'max:120'],
        ]);

        $this->logEvent('bank_accepted', $this->bank_ref);
        $this->bank_ref = null;

        $this->refreshBundle();
    }

    // -------------------------
    // Registry Rows
    // -------------------------
    private function ensureRequiredRows(): void
    {
        foreach (ExportBundleDocKeys::required() as $key) {
            if ($this->docRow($key)) {
                continue;
            }

            $this->exportBundle->documents()->create([
                'doc_key' => $key,
                'status' => 'missing',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }
    }

    private function ensureCommercialInvoiceRegistry(): void
    {
        $ci = $this->exportBundle->commercialInvoice;
        if (!$ci) {
            return;
        }

        $row = $this->docRow(ExportBundleDocKeys::COMMERCIAL_INVOICE);

        if ($row) {
            if (!$row->documentable_id) {
                $row->update([
                    'documentable_type' => get_class($ci),
                    'documentable_id' => $ci->id,
                    'status' => 'generated',
                    'generated_at' => now(),
                    'updated_by' => Auth::id(),
                ]);
            }
            return;
        }

        $this->exportBundle->documents()->create([
            'doc_key' => ExportBundleDocKeys::COMMERCIAL_INVOICE,
            'documentable_type' => get_class($ci),
            'documentable_id' => $ci->id,
            'status' => 'generated',
            'generated_at' => now(),
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);
    }

    private function upsertRegistry(string $docKey, string $type, int $id): void
    {
        $row = $this->docRow($docKey);

        if ($row) {
            $row->update([
                'documentable_type' => $type,
                'documentable_id' => $id,
                'status' => 'generated',
                'generated_at' => now(),
                'updated_by' => Auth::id(),
            ]);
        } else {
            $this->exportBundle->documents()->create([
                'doc_key' => $docKey,
                'documentable_type' => $type,
                'documentable_id' => $id,
                'status' => 'generated',
                'generated_at' => now(),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }

        $this->refreshBundle();
    }

    // -------------------------
    // Generate (block when locked/submitted)
    // -------------------------
    private function nextPlNumber(): string
    {
        $next = (int) (PackingList::max('id') ?? 0) + 1;
        return 'PL-' . now()->format('Y') . '-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    private function nextNegotiationLetterNumber(): string
    {
        $next = (int) (NegotiationLetter::max('id') ?? 0) + 1;
        return 'NL-' . now()->format('Y') . '-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    private function nextBoeNumber(string $type): string
    {
        $next = (int) (BillOfExchange::max('id') ?? 0) + 1;
        $suffix = $type === 'one' ? '1ST' : '2ND';
        return 'BOE-' . now()->format('Y') . '-' . $suffix . '-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    public function generatePackingList(): void
    {
        $this->authorize('generate', $this->exportBundle);

        if ($this->isLocked() || $this->isSubmitted()) {
            return;
        }

        $key = ExportBundleDocKeys::PACKING_LIST;
        $row = $this->docRow($key);
        if ($row && $row->documentable_id) {
            return;
        }

        $ci = $this->exportBundle->commercialInvoice;
        if (!$ci) {
            return;
        }

        $pl = PackingList::create([
            'pl_number' => $this->nextPlNumber(),
            'pl_date' => now()->toDateString(),
            'commercial_invoice_id' => $ci->id,
            'lc_receive_id' => $ci->lc_receive_id ?? null,
            'customer_id' => $ci->customer_id ?? null,
            'beneficiary_company_id' => $ci->beneficiary_company_id ?? null,
            'total_cartons' => 0,
            'total_quantity' => 0,
            'total_nw' => 0,
            'total_gw' => 0,
            'total_cbm' => 0,
            'remarks' => null,
            'internal_notes' => null,
            'status' => 'draft',
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        $this->upsertRegistry($key, $pl->getMorphClass(), $pl->id);
    }

    public function generateNegotiationLetter(): void
    {
        $this->authorize('generate', $this->exportBundle);

        if ($this->isLocked() || $this->isSubmitted()) {
            return;
        }

        $key = ExportBundleDocKeys::NEGOTIATION_LETTER;
        $row = $this->docRow($key);
        if ($row && $row->documentable_id) {
            return;
        }

        $ci = $this->exportBundle->commercialInvoice;
        if (!$ci) {
            return;
        }

        $nl = NegotiationLetter::create([
            'letter_number' => $this->nextNegotiationLetterNumber(),
            'letter_date' => now()->toDateString(),
            'commercial_invoice_id' => $ci->id,
            'lc_receive_id' => $ci->lc_receive_id ?? null,
            'beneficiary_company_id' => $ci->beneficiary_company_id ?? null,
            'customer_id' => $ci->customer_id ?? null,
            'currency_id' => $ci->currency_id ?? null,
            'invoice_amount' => 0,
            'net_payable_amount' => 0,
            'deductions' => 0,
            'bank_name' => null,
            'bank_branch' => null,
            'swift_code' => null,
            'remarks' => null,
            'status' => 'draft',
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        $this->upsertRegistry($key, $nl->getMorphClass(), $nl->id);
    }

    public function generateBoeOne(): void
    {
        $this->authorize('generate', $this->exportBundle);

        if ($this->isLocked() || $this->isSubmitted()) {
            return;
        }

        $this->generateBoe('one', ExportBundleDocKeys::BOE_ONE);
    }

    public function generateBoeTwo(): void
    {
        $this->authorize('generate', $this->exportBundle);

        if ($this->isLocked() || $this->isSubmitted()) {
            return;
        }

        $this->generateBoe('two', ExportBundleDocKeys::BOE_TWO);
    }

    private function generateBoe(string $type, string $key): void
    {
        if ($this->isLocked() || $this->isSubmitted()) {
            return;
        }

        $row = $this->docRow($key);
        if ($row && $row->documentable_id) {
            return;
        }

        $ci = $this->exportBundle->commercialInvoice;
        if (!$ci) {
            return;
        }

        $amount = (float) ($ci->net_amount ?? $ci->total_amount ?? 0);

        $boe = BillOfExchange::create([
            'boe_number' => $this->nextBoeNumber($type),
            'boe_type' => $type,
            'issue_date' => now()->toDateString(),
            'tenor_days' => 0,
            'maturity_date' => null,
            'customer_id' => $ci->customer_id ?? null,
            'beneficiary_company_id' => $ci->beneficiary_company_id ?? null,
            'lc_receive_id' => $ci->lc_receive_id ?? null,
            'commercial_invoice_id' => $ci->id,
            'currency_id' => $ci->currency_id ?? null,
            'amount' => $amount,
            'amount_in_words' => null,
            'place_of_drawing' => $this->defaultPlaceOfDrawing(),
            'drawee_name' => $ci->customer?->name ?? null,
            'drawee_address' => null,
            'drawee_bank_name' => null,
            'drawee_bank_address' => null,
            'status' => 'draft',
            'remarks' => null,
            'internal_notes' => null,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        $this->upsertRegistry($key, $boe->getMorphClass(), $boe->id);
    }

    // -------------------------
    // Print (allowed even if locked/submitted)
    // -------------------------
    public function printDoc(string $docKey): void
    {
        $this->authorize('print', $this->exportBundle);

        $row = $this->docRow($docKey);
        if (!$row || !$row->documentable_id) {
            return;
        }

        $row->update([
            'printed_at' => now(),
            'print_count' => (int) ($row->print_count ?? 0) + 1,
            'status' => 'printed',
            'updated_by' => Auth::id(),
        ]);

        // ✅ Step 7 log
        $this->logEvent('printed', null, "Printed: {$docKey}");

        $url = $this->getPrintUrl($docKey, (int) $row->documentable_id);
        $this->dispatch('open-print', url: $url);

        $this->refreshBundle();
    }

    private function getPrintUrl(string $docKey, int $id): string
    {
        return match ($docKey) {
            ExportBundleDocKeys::COMMERCIAL_INVOICE
            => route('admin.trade.commercial-invoices.print', ['commercialInvoice' => $id]),

            ExportBundleDocKeys::PACKING_LIST
            => route('admin.trade.packing-lists.print', ['packingList' => $id]),

            ExportBundleDocKeys::NEGOTIATION_LETTER
            => route('admin.trade.negotiation-letters.print', ['negotiationLetter' => $id]),

            ExportBundleDocKeys::BOE_ONE, ExportBundleDocKeys::BOE_TWO
            => route('admin.trade.bill-of-exchanges.print', ['billOfExchange' => $id]),

            default => '#',
        };
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
            'lockErrors' => $this->lockErrors,
            'submitErrors' => $this->submitErrors,

            // ✅ Step 7: you can show timeline easily in blade
            'events' => $this->exportBundle->events?->sortByDesc('event_at') ?? collect(),
            'shipment' => $this->exportBundle->shipment,
        ]);
    }
}