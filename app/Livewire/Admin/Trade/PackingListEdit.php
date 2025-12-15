<?php

namespace App\Livewire\Admin\Trade;

use App\Models\CommercialInvoice;
use App\Models\LcReceive;
use App\Models\PackingList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Actions\Concerns\InteractsWithActions;

class PackingListEdit extends Component implements HasSchemas, HasActions
{
    use InteractsWithSchemas;
    use InteractsWithActions;

    public PackingList $record;

    public ?array $data = [];

    public function mount(PackingList $record): void
    {
        $this->record = $record;

        $this->form->fill(array_merge(
            $record->toArray(),
            [
                'items' => $record->items()
                    ->orderBy('line_no')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'line_no' => $item->line_no,
                            'description' => $item->description,
                            'carton_from' => $item->carton_from,
                            'carton_to' => $item->carton_to,
                            'qty_per_carton' => $item->qty_per_carton,
                            'net_weight' => $item->net_weight,
                            'gross_weight' => $item->gross_weight,
                            'cbm' => $item->cbm,
                        ];
                    })
                    ->toArray(),
            ]
        ));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components($this->getFormSchema());
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Packing List Header')
                ->columns(3)
                ->schema([
                    TextInput::make('pl_number')
                        ->label('PL Number')
                        ->required()
                        ->maxLength(50),

                    DatePicker::make('pl_date')
                        ->label('PL Date')
                        ->required()
                        ->native(false),

                    Select::make('commercial_invoice_id')
                        ->label('Commercial Invoice')
                        ->options(
                            CommercialInvoice::orderBy('invoice_number')
                                ->pluck('invoice_number', 'id')
                                ->toArray()
                        )
                        ->searchable()
                        ->preload(),

                    Select::make('lc_receive_id')
                        ->label('LC Receive (Optional)')
                        ->options(
                            LcReceive::orderBy('lc_number')
                                ->pluck('lc_number', 'id')
                                ->toArray()
                        )
                        ->searchable()
                        ->preload(),

                    Textarea::make('remarks')
                        ->label('Remarks (Shown on PL)')
                        ->rows(3)
                        ->columnSpanFull(),

                    Textarea::make('internal_notes')
                        ->label('Internal Notes')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),

            Section::make('Carton / Packing Details')
                ->schema([
                    Repeater::make('items')
                        ->label('Lines')
                        ->defaultItems(1)
                        ->columns(8)
                        ->schema([
                            TextInput::make('line_no')
                                ->label('Line')
                                ->numeric()
                                ->minValue(1)
                                ->default(1),

                            TextInput::make('description')
                                ->label('Description')
                                ->columnSpan(2),

                            TextInput::make('carton_from')
                                ->label('Ctn From')
                                ->numeric()
                                ->required(),

                            TextInput::make('carton_to')
                                ->label('Ctn To')
                                ->numeric()
                                ->required(),

                            TextInput::make('qty_per_carton')
                                ->label('Qty / Ctn')
                                ->numeric()
                                ->required(),

                            TextInput::make('net_weight')
                                ->label('N.W. (kg)')
                                ->numeric()
                                ->default(0),

                            TextInput::make('gross_weight')
                                ->label('G.W. (kg)')
                                ->numeric()
                                ->default(0),

                            TextInput::make('cbm')
                                ->label('CBM')
                                ->numeric(3)
                                ->default(0),
                        ])
                        ->columnSpanFull(),
                ]),
        ];
    }

    public function update(): void
    {
        $data = $this->form->getState();
        $items = $data['items'] ?? [];
        unset($data['items']);

        $totalCartons = 0;
        $totalQty = 0;
        $totalNW = 0;
        $totalGW = 0;
        $totalCBM = 0;

        // Compute totals
        foreach ($items as &$item) {
            $from = (int) ($item['carton_from'] ?? 0);
            $to = (int) ($item['carton_to'] ?? 0);

            $item['total_cartons'] = max(0, $to - $from + 1);
            $item['total_qty'] = $item['total_cartons'] * (int) ($item['qty_per_carton'] ?? 0);

            $totalCartons += $item['total_cartons'];
            $totalQty += $item['total_qty'];
            $totalNW += (float) ($item['net_weight'] ?? 0);
            $totalGW += (float) ($item['gross_weight'] ?? 0);
            $totalCBM += (float) ($item['cbm'] ?? 0);
        }

        $data['total_cartons'] = $totalCartons;
        $data['total_quantity'] = $totalQty;
        $data['total_nw'] = $totalNW;
        $data['total_gw'] = $totalGW;
        $data['total_cbm'] = $totalCBM;
        $data['updated_by'] = auth()->id();

        // Update header
        $this->record->update($data);

        // Recreate items
        $this->record->items()->delete();
        foreach ($items as $index => $itemData) {
            $itemData['line_no'] = $itemData['line_no'] ?? ($index + 1);
            $itemData['packing_list_id'] = $this->record->id;
            $this->record->items()->create($itemData);
        }

        session()->flash('success', 'Packing List updated successfully.');

        $this->redirectRoute('admin.trade.packing-lists.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.trade.packing-list-edit');
    }
}