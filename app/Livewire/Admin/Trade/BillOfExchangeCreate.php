<?php

namespace App\Livewire\Admin\Trade;

use App\Models\BeneficiaryCompany;
use App\Models\BillOfExchange;
use App\Models\CommercialInvoice;
use App\Models\Currency;
use App\Models\Customer;
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

class BillOfExchangeCreate extends Component implements HasSchemas, HasActions
{
    use InteractsWithSchemas;
    use InteractsWithActions;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'issue_date' => now()->toDateString(),
            'boe_type' => 'FIRST',
            'status' => 'draft',
            'tenor_days' => 0,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components($this->getFormSchema())
            ->statePath('data');
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('BOE Header')
                ->columns(4)
                ->schema([
                    TextInput::make('boe_number')
                        ->label('BOE Number')
                        ->required()
                        ->maxLength(100),

                    DatePicker::make('issue_date')
                        ->label('Issue Date')
                        ->required()
                        ->native(false),

                    Select::make('boe_type')
                        ->label('Type')
                        ->options([
                            'FIRST' => 'First of Exchange',
                            'SECOND' => 'Second of Exchange',
                        ])
                        ->required(),

                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'sent' => 'Sent',
                            'accepted' => 'Accepted',
                            'cancelled' => 'Cancelled',
                        ])
                        ->default('draft'),
                ]),

            Section::make('Parties & References')
                ->columns(3)
                ->schema([
                    Select::make('customer_id')
                        ->label('Customer (Drawee)')
                        ->options(
                            Customer::orderBy('name')->pluck('name', 'id')->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('beneficiary_company_id')
                        ->label('Beneficiary Company (Drawer)')
                        ->options(
                            BeneficiaryCompany::orderBy('short_name')->pluck('short_name', 'id')->toArray()
                        )
                        ->searchable()
                        ->preload(),

                    Select::make('currency_id')
                        ->label('Currency')
                        ->options(
                            Currency::where('is_active', true)->orderBy('code')->pluck('code', 'id')->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('lc_receive_id')
                        ->label('Linked LC')
                        ->options(
                            LcReceive::orderBy('lc_number')->pluck('lc_number', 'id')->toArray()
                        )
                        ->searchable()
                        ->preload(),

                    Select::make('commercial_invoice_id')
                        ->label('Commercial Invoice')
                        ->options(
                            CommercialInvoice::orderBy('invoice_number')->pluck('invoice_number', 'id')->toArray()
                        )
                        ->searchable()
                        ->preload(),

                    TextInput::make('place_of_drawing')
                        ->label('Place of Drawing')
                        ->maxLength(255),
                ]),

            Section::make('Amount & Tenor')
                ->columns(3)
                ->schema([
                    TextInput::make('amount')
                        ->label('Amount')
                        ->numeric()
                        ->required(),

                    TextInput::make('amount_in_words')
                        ->label('Amount in Words')
                        ->columnSpan(2),

                    TextInput::make('tenor_days')
                        ->label('Tenor (days)')
                        ->numeric()
                        ->default(0),

                    DatePicker::make('maturity_date')
                        ->label('Maturity Date')
                        ->native(false)
                        ->helperText('If empty, system will calculate from Issue Date + Tenor.'),

                    TextInput::make('drawee_name')
                        ->label('Drawee Name')
                        ->maxLength(255),

                    Textarea::make('drawee_address')
                        ->label('Drawee Address')
                        ->rows(2)
                        ->columnSpan(2),

                    TextInput::make('drawee_bank_name')
                        ->label('Drawee Bank')
                        ->maxLength(255),

                    Textarea::make('drawee_bank_address')
                        ->label('Drawee Bank Address')
                        ->rows(2)
                        ->columnSpan(2),
                ]),

            Section::make('Notes')
                ->columns(2)
                ->schema([
                    Textarea::make('remarks')
                        ->label('BOE Text / Remarks')
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

        // calculate maturity if not set
        if (empty($data['maturity_date']) && !empty($data['issue_date']) && !empty($data['tenor_days'])) {
            $data['maturity_date'] = now()
                ->parse($data['issue_date'])
                ->addDays((int) $data['tenor_days'])
                ->toDateString();
        }

        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        BillOfExchange::create($data);

        session()->flash('success', 'Bill of Exchange created successfully.');

        $this->redirectRoute('admin.trade.bill-of-exchanges.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.trade.bill-of-exchange-create');
    }
}