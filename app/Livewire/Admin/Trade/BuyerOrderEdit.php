<?php

namespace App\Livewire\Admin\Trade;

use App\Models\BuyerOrder;
use App\Models\BuyerOrderItem;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class BuyerOrderEdit extends Component implements HasSchemas, HasActions
{
    use InteractsWithSchemas;
    use InteractsWithActions;

    public BuyerOrder $record;

    public ?array $data = [];

    public function mount(BuyerOrder $record): void
    {
        $this->record = $record;

        // ✅ IMPORTANT:
        // Do NOT use ->toArray() fully, because it includes created_at/updated_at (ISO string)
        // Only load fields you actually edit + keep id
        $items = $record->items()
            ->orderBy('line_no')
            ->get()
            ->map(function (BuyerOrderItem $item) {
                return [
                    'id' => $item->id,
                    'line_no' => $item->line_no,
                    'style_ref' => $item->style_ref,
                    'item_description' => $item->item_description,
                    'color' => $item->color,
                    'size' => $item->size,
                    'unit' => $item->unit,
                    'factory_subcategory_id' => $item->factory_subcategory_id,
                    'factory_id' => $item->factory_id,
                    'order_qty' => $item->order_qty,
                    'unit_price' => $item->unit_price,
                    'amount' => $item->amount,
                    'remarks' => $item->remarks,
                ];
            })
            ->toArray();

        $this->form->fill(array_merge(
            $record->toArray(),
            ['items' => $items],
        ));
    }

    public function form(Schema $schema): Schema
    {
        // reuse the create schema
        return (new BuyerOrderCreate())->form($schema);
    }

    public function update(): void
    {
        $data = $this->form->getState();

        $items = $data['items'] ?? [];
        unset($data['items']);

        // ✅ Safety: never allow these from form state
        unset($data['created_at'], $data['updated_at']);

        // Calculate total from items
        $total = 0;
        foreach ($items as $item) {
            $total += (float) ($item['amount'] ?? 0);
        }

        $data['order_value'] = (float) ($data['order_value'] ?? 0);
        if ($data['order_value'] <= 0) {
            $data['order_value'] = $total;
        }

        $data['updated_by'] = auth()->id();
        $this->record->update($data);

        /**
         * ✅ Items sync WITHOUT deleting everything:
         * - Update by ID
         * - Create new ones
         * - Delete removed ones only
         */

        $keepIds = collect($items)
            ->pluck('id')
            ->filter()
            ->map(fn($v) => (int) $v)
            ->values();

        // Delete removed items only
        $this->record->items()
            ->when($keepIds->isNotEmpty(), fn($q) => $q->whereNotIn('id', $keepIds))
            ->when($keepIds->isEmpty(), fn($q) => $q)
            ->delete();

        foreach ($items as $idx => $item) {
            $itemId = $item['id'] ?? null;

            // ✅ Very important: remove timestamps and non-edit keys
            unset(
                $item['id'],
                $item['buyer_order_id'],
                $item['created_at'],
                $item['updated_at']
            );

            $item['line_no'] = $item['line_no'] ?? ($idx + 1);
            $item['buyer_order_id'] = $this->record->id;

            if ($itemId) {
                // Update existing item (allocations remain)
                $this->record->items()
                    ->whereKey($itemId)
                    ->update($item);
            } else {
                // Create new item
                $this->record->items()->create($item);
            }
        }

        session()->flash('success', 'Buyer Order updated successfully.');
        $this->redirectRoute('admin.trade.buyer-orders.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.trade.buyer-order-edit');
    }
}