<?php

namespace App\Livewire\Admin\Trade;

use App\Models\BillOfExchange;
use App\Models\ExportBundle;
use App\Models\ExportBundleDocument;
use App\Models\NegotiationLetter;
use App\Models\PackingList;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class ExportBundleView extends Component
{
    public ExportBundle $exportBundle;

    public function mount(ExportBundle $exportBundle): void
    {
        $this->exportBundle = $exportBundle->load([
            'commercialInvoice.customer',
            'documents',
        ]);

        $this->syncRegistry(); // ✅ ONLY place registry is touched
    }

    /**
     * Ensure registry rows exist and CI status is correct
     */
    protected function syncRegistry(): void
    {
        $expected = [
            'commercial_invoice' => 'admin.trade.commercial-invoices.print',
            'packing_list' => 'admin.trade.packing-lists.print',
            'negotiation_letter' => 'admin.trade.negotiation-letters.print',
            'boe_one' => 'admin.trade.bill-of-exchanges.print',
            'boe_two' => 'admin.trade.bill-of-exchanges.print',
        ];

        foreach ($expected as $type => $route) {
            ExportBundleDocument::updateOrCreate(
                [
                    'export_bundle_id' => $this->exportBundle->id,
                    'document_type' => $type,
                ],
                [
                    'print_route' => $route,
                    'status' => 'missing',
                ]
            );
        }

        // Sync CI status once
        $ci = $this->exportBundle->commercialInvoice;

        ExportBundleDocument::updateOrCreate(
            [
                'export_bundle_id' => $this->exportBundle->id,
                'document_type' => 'commercial_invoice',
            ],
            [
                'document_id' => $ci?->id,
                'status' => $ci ? 'ready' : 'missing',
            ]
        );

        $this->exportBundle->load('documents');
    }

    protected function setDoc(string $type, ?int $id): void
    {
        ExportBundleDocument::updateOrCreate(
            [
                'export_bundle_id' => $this->exportBundle->id,
                'document_type' => $type,
            ],
            [
                'document_id' => $id,
                'status' => $id ? 'ready' : 'missing',
            ]
        );

        $this->exportBundle->load('documents');
    }

    public function generatePackingList(): void
    {
        $ci = $this->exportBundle->commercialInvoice;
        if (!$ci)
            return;

        $pl = PackingList::firstOrCreate(
            ['commercial_invoice_id' => $ci->id],
            [
                'pl_number' => 'PL-' . now()->format('ymd') . '-' . Str::upper(Str::random(4)),
                'pl_date' => now(),
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]
        );

        $this->setDoc('packing_list', $pl->id);
    }

    public function generateNegotiationLetter(): void
    {
        $ci = $this->exportBundle->commercialInvoice;
        if (!$ci)
            return;

        $nl = NegotiationLetter::firstOrCreate(
            ['commercial_invoice_id' => $ci->id],
            [
                'letter_number' => 'NL-' . now()->format('ymd') . '-' . Str::upper(Str::random(4)),
                'letter_date' => now(),
                'invoice_amount' => $ci->total_amount,
                'net_payable_amount' => $ci->total_amount,
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]
        );

        $this->setDoc('negotiation_letter', $nl->id);
    }

    public function generateBoeOne(): void
    {
        $ci = $this->exportBundle->commercialInvoice;
        if (!$ci)
            return;

        $boe = BillOfExchange::firstOrCreate(
            [
                'commercial_invoice_id' => $ci->id,
                'boe_type' => 'one',
            ],
            [
                'boe_number' => 'BOE-1-' . now()->format('ymd') . '-' . Str::upper(Str::random(4)),
                'issue_date' => now(),
                'amount' => $ci->total_amount,
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]
        );

        $this->setDoc('boe_one', $boe->id);
    }

    public function generateBoeTwo(): void
    {
        $ci = $this->exportBundle->commercialInvoice;
        if (!$ci)
            return;

        $boe = BillOfExchange::firstOrCreate(
            [
                'commercial_invoice_id' => $ci->id,
                'boe_type' => 'two',
            ],
            [
                'boe_number' => 'BOE-2-' . now()->format('ymd') . '-' . Str::upper(Str::random(4)),
                'issue_date' => now(),
                'amount' => $ci->total_amount,
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]
        );

        $this->setDoc('boe_two', $boe->id);
    }

    public function generateAllMissing(): void
    {
        $this->generatePackingList();
        $this->generateNegotiationLetter();
        $this->generateBoeOne();
        $this->generateBoeTwo();
    }

    public function render(): View
    {
        $docs = $this->exportBundle->documents
            ->sortBy(fn($d) => match ($d->document_type) {
                'commercial_invoice' => 1,
                'packing_list' => 2,
                'negotiation_letter' => 3,
                'boe_one' => 4,
                'boe_two' => 5,
                default => 99,
            })
            ->values();

        return view('livewire.admin.trade.export-bundle-view', [
            'exportBundle' => $this->exportBundle,
            'ci' => $this->exportBundle->commercialInvoice,
            'docs' => $docs,
        ]);
    }
}