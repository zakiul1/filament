<?php

namespace App\Livewire\Admin\Trade;

use App\Models\Currency;
use App\Models\Factory;
use App\Models\LcReceive;
use App\Models\LcTransfer;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class LcTransferCreate extends Component implements HasSchemas, HasActions
{
    use InteractsWithSchemas;
    use InteractsWithActions;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'transfer_date' => now()->toDateString(),
            'status' => 'draft',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(static::getFormSchema())
            ->statePath('data');
    }

    public static function getFormSchema(): array
    {
        return [
            Section::make('Transfer Header')
                ->columns(3)
                ->schema([
                    TextInput::make('transfer_no')
                        ->label('Transfer No.')
                        ->required()
                        ->maxLength(100),

                    DatePicker::make('transfer_date')
                        ->label('Transfer Date')
                        ->native(false)
                        ->required(),

                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'issued' => 'Issued to Factory',
                            'partially_utilized' => 'Partially Utilized',
                            'fully_utilized' => 'Fully Utilized',
                            'cancelled' => 'Cancelled',
                        ])
                        ->default('draft'),
                ]),

            Section::make('Source LC & Factory')
                ->columns(2)
                ->schema([
                    Select::make('lc_receive_id')
                        ->label('Source LC')
                        ->options(
                            LcReceive::with('customer')
                                ->orderByDesc('lc_date')
                                ->get()
                                ->mapWithKeys(fn($lc) => [
                                    $lc->id => $lc->lc_number . ' — ' . optional($lc->customer)->name,
                                ])
                                ->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('factory_id')
                        ->label('Factory (Beneficiary)')
                        ->options(
                            Factory::orderBy('name')
                                ->pluck('name', 'id')
                                ->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->required(),
                ]),

            Section::make('Amount')
                ->columns(3)
                ->schema([
                    Select::make('currency_id')
                        ->label('Currency')
                        ->options(
                            Currency::where('is_active', true)
                                ->orderBy('code')
                                ->pluck('code', 'id')
                                ->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->required(),

                    TextInput::make('transfer_amount')
                        ->label('Transfer Amount')
                        ->numeric()
                        ->required(),

                    TextInput::make('tolerance_plus')
                        ->label('+%')
                        ->numeric()
                        ->nullable(),

                    TextInput::make('tolerance_minus')
                        ->label('-%')
                        ->numeric()
                        ->nullable(),
                ])
                ->columns(4),

            Section::make('Notes')
                ->columns(2)
                ->schema([
                    Textarea::make('remarks')
                        ->label('Remarks (visible)')
                        ->rows(3),

                    Textarea::make('internal_notes')
                        ->label('Internal Notes')
                        ->rows(3),
                ]),
        ];
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        LcTransfer::create($data);

        session()->flash('success', 'LC Transfer created successfully.');

        $this->redirectRoute('admin.trade.lc-transfers.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.trade.lc-transfer-create');
    }
}