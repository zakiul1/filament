<?php

namespace App\Livewire\Admin\Trade;

use App\Models\NegotiationLetter;
use App\Models\CommercialInvoice;
use App\Models\LcReceive;
use App\Models\BeneficiaryCompany;
use App\Models\Customer;
use App\Models\Currency;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Livewire\Component;

class NegotiationLetterCreate extends Component implements HasSchemas, HasActions
{
    use InteractsWithSchemas, InteractsWithActions;

    public ?array $data = [];

    public function mount()
    {
        $this->form->fill([
            'letter_date' => now()->toDateString(),
            'status' => 'draft',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components($this->getFormSchema())->statePath('data');
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Letter Header')
                ->columns(3)
                ->schema([
                    TextInput::make('letter_number')->required(),
                    DatePicker::make('letter_date')->required()->native(false),
                    Select::make('commercial_invoice_id')
                        ->label('Commercial Invoice')
                        ->options(
                            CommercialInvoice::orderBy('invoice_number')->pluck('invoice_number', 'id')
                        )
                        ->searchable()
                        ->preload()
                        ->required(),
                ]),

            Section::make('Parties')
                ->columns(3)
                ->schema([
                    Select::make('lc_receive_id')
                        ->label('LC')
                        ->options(LcReceive::orderBy('lc_number')->pluck('lc_number', 'id'))
                        ->searchable(),

                    Select::make('customer_id')
                        ->label('Customer')
                        ->options(Customer::orderBy('name')->pluck('name', 'id'))
                        ->searchable(),

                    Select::make('beneficiary_company_id')
                        ->label('Beneficiary Company')
                        ->options(BeneficiaryCompany::pluck('short_name', 'id'))
                        ->searchable(),

                    Select::make('currency_id')
                        ->label('Currency')
                        ->options(Currency::pluck('code', 'id'))
                        ->searchable(),
                ]),

            Section::make('Amounts')
                ->columns(3)
                ->schema([
                    TextInput::make('invoice_amount')->numeric()->required(),
                    TextInput::make('deductions')->numeric()->default(0),
                    TextInput::make('net_payable_amount')->numeric()->required(),
                ]),

            Section::make('Bank Info')
                ->columns(2)
                ->schema([
                    TextInput::make('bank_name'),
                    TextInput::make('bank_branch'),
                    TextInput::make('swift_code'),
                ]),

            Section::make('Remarks')
                ->schema([
                    Textarea::make('remarks')->rows(3),
                ]),
        ];
    }

    public function create()
    {
        $data = $this->form->getState();
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        NegotiationLetter::create($data);

        session()->flash('success', 'Negotiation Letter created successfully.');

        return redirect()->route('admin.trade.negotiation-letters.index');
    }

    public function render()
    {
        return view('livewire.admin.trade.negotiation-letter-create');
    }
}