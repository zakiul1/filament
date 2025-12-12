<?php

namespace App\Livewire\Admin\Trade;

use App\Models\BuyerOrderItem;
use App\Models\BuyerOrderItemAllocation;
use App\Models\Factory;
use App\Models\FactoryMaster;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Support\Contracts\TranslatableContentDriver;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class BuyerOrderItemAllocationsPage extends Component implements HasTable, HasActions, HasSchemas
{
    use InteractsWithTable;
    use InteractsWithActions;
    use InteractsWithSchemas;

    public BuyerOrderItem $item;

    public function mount(BuyerOrderItem $item): void
    {
        $this->item = $item;
    }

    public function makeFilamentTranslatableContentDriver(): ?TranslatableContentDriver
    {
        return null;
    }

    public function getAllocatedQtyProperty(): float
    {
        return (float) $this->item->allocations()->sum('qty');
    }

    public function getRemainingQtyProperty(): float
    {
        return (float) ($this->item->order_qty ?? 0) - $this->allocatedQty;
    }

    protected function allocationFormSchema(?BuyerOrderItemAllocation $record = null): array
    {
        return [
            Select::make('factory_id')
                ->label('Factory')
                ->options(
                    Factory::query()
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray()
                )
                ->searchable()
                ->preload()
                ->required(),

            TextInput::make('qty')
                ->label('Allocated Qty')
                ->numeric()
                ->minValue(1)
                ->required()
                ->helperText('Cannot exceed remaining qty.'),

            Textarea::make('remarks')
                ->label('Remarks')
                ->rows(3),
        ];
    }

    protected function ensureQtyValid(float $qty, ?BuyerOrderItemAllocation $editing = null): void
    {
        $alreadyAllocated = (float) $this->item->allocations()->when(
            $editing,
            fn($q) => $q->where('id', '!=', $editing->id)
        )->sum('qty');

        $maxAllowed = (float) ($this->item->order_qty ?? 0) - $alreadyAllocated;

        if ($qty > $maxAllowed) {
            throw ValidationException::withMessages([
                'qty' => "Allocated qty exceeds item order qty. Max allowed now: {$maxAllowed}",
            ]);
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                BuyerOrderItemAllocation::query()
                    ->where('buyer_order_item_id', $this->item->id)
                    ->with('factory')
                    ->latest('id')
            )
            ->columns([
                TextColumn::make('factory.name')->label('Factory')->searchable()->sortable(),
                TextColumn::make('qty')->label('Qty')->numeric(0)->sortable(),
                TextColumn::make('remarks')->label('Remarks')->wrap(),
                BadgeColumn::make('id')->label('ID')->color('gray'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Allocation')
                    ->form(fn() => $this->allocationFormSchema())
                    ->mutateFormDataUsing(function (array $data) {
                        $qty = (float) ($data['qty'] ?? 0);
                        $this->ensureQtyValid($qty);

                        $data['buyer_order_item_id'] = $this->item->id;
                        $data['created_by'] = auth()->id();
                        $data['updated_by'] = auth()->id();

                        return $data;
                    })
                    ->using(fn(array $data) => BuyerOrderItemAllocation::create($data)),
            ])
            ->actions([
                EditAction::make()
                    ->form(fn(BuyerOrderItemAllocation $record) => $this->allocationFormSchema($record))
                    ->mutateFormDataUsing(function (array $data, BuyerOrderItemAllocation $record) {
                        $qty = (float) ($data['qty'] ?? 0);
                        $this->ensureQtyValid($qty, $record);

                        $data['updated_by'] = auth()->id();
                        return $data;
                    }),

                DeleteAction::make(),
            ])
            ->emptyStateHeading('No allocations yet')
            ->emptyStateDescription('Create allocations to split this style qty across factories.');
    }

    public function render(): View
    {
        return view('livewire.admin.trade.buyer-order-item-allocations-index', [
            'item' => $this->item,
            'allocatedQty' => $this->allocatedQty,
            'remainingQty' => $this->remainingQty,
        ]);
    }
}