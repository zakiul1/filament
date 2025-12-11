<?php

namespace App\Livewire\Admin\Trade;

use App\Models\CommercialInvoice;
use App\Models\LcReceive;
use App\Models\PackingList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Livewire\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Illuminate\Contracts\View\View;
use Filament\Actions\Concerns\InteractsWithActions;

class PackingListCreate extends Component implements HasSchemas, HasActions
{
    use InteractsWithSchemas, InteractsWithActions;

    public ?array $data = [];

    public function mount()
    {
        $this->form->fill([
            'pl_date' => now()->toDateString(),
            'status' => 'draft',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Packing List Header')
                    ->columns(3)
                    ->schema([
                        TextInput::make('pl_number')->required(),
                        DatePicker::make('pl_date')->required(),

                        Select::make('commercial_invoice_id')
                            ->label('Commercial Invoice')
                            ->options(
                                CommercialInvoice::pluck('invoice_number', 'id')->toArray()
                            )
                            ->searchable()
                            ->preload(),

                        Select::make('lc_receive_id')
                            ->label('LC Receive')
                            ->options(
                                LcReceive::pluck('lc_number', 'id')->toArray()
                            )
                            ->searchable()
                            ->preload(),

                        Textarea::make('remarks')->columnSpanFull(),
                    ]),

                Section::make('Items')
                    ->schema([
                        Repeater::make('items')
                            ->columns(6)
                            ->schema([
                                TextInput::make('line_no')->numeric(),

                                TextInput::make('description')->columnSpan(2),

                                TextInput::make('carton_from')->numeric(),
                                TextInput::make('carton_to')->numeric(),

                                TextInput::make('qty_per_carton')->numeric(),

                                TextInput::make('net_weight')->numeric(2),
                                TextInput::make('gross_weight')->numeric(2),
                                TextInput::make('cbm')->numeric(3),
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function create()
    {
        $data = $this->form->getState();
        $items = $data['items'] ?? [];
        unset($data['items']);

        $totalCartons = 0;
        $totalQty = 0;
        $totalNW = 0;
        $totalGW = 0;
        $totalCBM = 0;

        foreach ($items as $item) {
            $item['total_cartons'] = $item['carton_to'] - $item['carton_from'] + 1;
            $item['total_qty'] = $item['total_cartons'] * $item['qty_per_carton'];

            $totalCartons += $item['total_cartons'];
            $totalQty += $item['total_qty'];
            $totalNW += $item['net_weight'];
            $totalGW += $item['gross_weight'];
            $totalCBM += $item['cbm'];
        }

        $data['total_cartons'] = $totalCartons;
        $data['total_quantity'] = $totalQty;
        $data['total_nw'] = $totalNW;
        $data['total_gw'] = $totalGW;
        $data['total_cbm'] = $totalCBM;
        $data['created_by'] = auth()->id();

        $pl = PackingList::create($data);

        foreach ($items as $i) {
            $pl->items()->create($i);
        }

        session()->flash('success', 'Packing List created.');
        return redirect()->route('admin.trade.packing-lists.index');
    }

    public function render(): View
    {
        return view('livewire.admin.trade.packing-list-create');
    }
}