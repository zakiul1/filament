<?php

namespace App\Livewire\Admin\Trade;

use App\Models\BillOfExchange;
use App\Models\ExportBundle;
use App\Models\NegotiationLetter;
use App\Models\PackingList;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ExportBundleView extends Component
{
    public ExportBundle $record;

    public function mount($record): void
    {
        $this->record = ExportBundle::query()
            ->with(['commercialInvoice.customer'])
            ->findOrFail($record);
    }

    public function render(): View
    {
        $ci = $this->record->commercialInvoice;

        // Related docs by commercial_invoice_id
        $packingList = $ci
            ? PackingList::query()->where('commercial_invoice_id', $ci->id)->latest('id')->first()
            : null;

        $negotiation = $ci
            ? NegotiationLetter::query()->where('commercial_invoice_id', $ci->id)->latest('id')->first()
            : null;

        // BOE: could be one or two. We’ll map by boe_type if you use it.
        $boes = $ci
            ? BillOfExchange::query()
                ->where('commercial_invoice_id', $ci->id)
                ->orderBy('boe_type')
                ->orderBy('id')
                ->get()
            : collect();

        $boeOne = $boes->firstWhere('boe_type', 'one') ?? $boes->first();
        $boeTwo = $boes->firstWhere('boe_type', 'two') ?? ($boes->count() > 1 ? $boes->get(1) : null);

        return view('livewire.admin.trade.export-bundle-view', [
            'ci' => $ci,
            'packingList' => $packingList,
            'negotiation' => $negotiation,
            'boeOne' => $boeOne,
            'boeTwo' => $boeTwo,
        ]);
    }
}