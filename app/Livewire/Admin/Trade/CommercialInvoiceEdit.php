<?php

// app/Livewire/Admin/Trade/CommercialInvoiceEdit.php

namespace App\Livewire\Admin\Trade;

use App\Models\CommercialInvoice;
use App\Models\CommercialInvoiceItem;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CommercialInvoiceEdit extends Component implements HasSchemas, HasActions
{
    use InteractsWithSchemas;
    use InteractsWithActions;

    public CommercialInvoice $record;

    public ?array $data = [];

    public function mount(CommercialInvoice $record): void
    {
        $this->record = $record;

        $this->form->fill(array_merge(
            $record->toArray(),
            [
                'items' => $record->items()
                    ->orderBy('line_no')
                    ->get()
                    ->map(fn(CommercialInvoiceItem $item) => $item->only([
                        'line_no',
                        'style_ref',
                        'item_description',
                        'hs_code',
                        'factory_subcategory_id',
                        'color',
                        'size',
                        'unit',
                        'quantity',
                        'unit_price',
                        'amount',
                        'carton_count',
                        'net_weight',
                        'gross_weight',
                        'cbm',
                    ]))
                    ->toArray(),
            ]
        ));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components((new CommercialInvoiceCreate())->getFormSchema())
            ->statePath('data');
    }

    public function update(): void
    {
        $data = $this->form->getState();
        $items = $data['items'] ?? [];
        unset($data['items']);

        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += (float) ($item['amount'] ?? 0);
        }

        $data['subtotal'] = $subtotal;
        $data['discount_amount'] = (float) ($data['discount_amount'] ?? 0);
        $data['other_charges'] = (float) ($data['other_charges'] ?? 0);
        $data['total_amount'] = $subtotal - $data['discount_amount'] + $data['other_charges'];
        $data['updated_by'] = auth()->id();

        $this->record->update($data);

        $this->record->items()->delete();

        foreach ($items as $index => $itemData) {
            $itemData['line_no'] = $itemData['line_no'] ?? ($index + 1);
            $itemData['commercial_invoice_id'] = $this->record->id;
            CommercialInvoiceItem::create($itemData);
        }

        session()->flash('success', 'Commercial Invoice updated successfully.');

        $this->redirectRoute('admin.trade.commercial-invoices.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.trade.commercial-invoice-edit');
    }
}