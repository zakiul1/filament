<?php

namespace App\Livewire\Admin\Trade;

use App\Models\LcAmendment;
use App\Models\LcReceive;
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

class LcAmendmentCreate extends Component implements HasSchemas, HasActions
{
    use InteractsWithSchemas;
    use InteractsWithActions;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'amendment_date' => now()->toDateString(),
            'status' => 'draft',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components($this->formSchema())
            ->statePath('data');
    }

    protected function formSchema(): array
    {
        return [
            Section::make('LC & Amendment Info')
                ->columns(3)
                ->schema([
                    Select::make('lc_receive_id')
                        ->label('LC Number')
                        ->options(
                            LcReceive::orderBy('lc_number')
                                ->pluck('lc_number', 'id')
                                ->toArray()
                        )
                        ->searchable()
                        ->required(),

                    TextInput::make('amendment_number')
                        ->label('Amendment No.')
                        ->required()
                        ->maxLength(50),

                    DatePicker::make('amendment_date')
                        ->label('Amendment Date')
                        ->required()
                        ->native(false),

                    Select::make('amendment_type')
                        ->label('Amendment Type')
                        ->options([
                            'VALUE_INCREASE' => 'Value Increase',
                            'VALUE_DECREASE' => 'Value Decrease',
                            'DATE_EXTEND' => 'Extend Expiry',
                            'DATE_CHANGE' => 'Change Shipment Date',
                            'TOLERANCE_CHANGE' => 'Tolerance Change',
                            'OTHER' => 'Other',
                        ])
                        ->required(),

                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'confirmed' => 'Confirmed',
                            'cancelled' => 'Cancelled',
                        ])
                        ->default('draft'),
                ]),

            Section::make('Value & Tolerance')
                ->columns(4)
                ->schema([
                    TextInput::make('previous_lc_amount')
                        ->label('Previous LC Amount')
                        ->numeric()
                        ->columnSpan(2),

                    TextInput::make('new_lc_amount')
                        ->label('New LC Amount')
                        ->numeric()
                        ->columnSpan(2),

                    TextInput::make('previous_tolerance_plus')
                        ->label('Prev Tol. +%')
                        ->numeric(),

                    TextInput::make('new_tolerance_plus')
                        ->label('New Tol. +%')
                        ->numeric(),

                    TextInput::make('previous_tolerance_minus')
                        ->label('Prev Tol. -%')
                        ->numeric(),

                    TextInput::make('new_tolerance_minus')
                        ->label('New Tol. -%')
                        ->numeric(),
                ]),

            Section::make('Dates')
                ->columns(2)
                ->schema([
                    DatePicker::make('previous_expiry_date')
                        ->label('Prev Expiry Date')
                        ->native(false),

                    DatePicker::make('new_expiry_date')
                        ->label('New Expiry Date')
                        ->native(false),

                    DatePicker::make('previous_last_shipment_date')
                        ->label('Prev Last Shipment Date')
                        ->native(false),

                    DatePicker::make('new_last_shipment_date')
                        ->label('New Last Shipment Date')
                        ->native(false),
                ]),

            Section::make('Notes')
                ->columns(2)
                ->schema([
                    Textarea::make('change_summary')
                        ->label('Change Summary')
                        ->rows(3)
                        ->columnSpan(2),

                    Textarea::make('other_changes')
                        ->label('Other Changes')
                        ->rows(3)
                        ->columnSpan(2),

                    Textarea::make('remarks')
                        ->label('Remarks (for LC / Bank)')
                        ->rows(3)
                        ->columnSpan(1),

                    Textarea::make('internal_notes')
                        ->label('Internal Notes')
                        ->rows(3)
                        ->columnSpan(1),
                ]),
        ];
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        LcAmendment::create($data);

        session()->flash('success', 'LC Amendment created successfully.');

        $this->redirectRoute('admin.trade.lc-amendments.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.trade.lc-amendment-create');
    }
}