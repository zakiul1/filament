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
use Illuminate\Contracts\View\View;
use Livewire\Component;

class NegotiationLetterEdit extends Component implements HasSchemas, HasActions
{
    use InteractsWithSchemas;
    use InteractsWithActions;

    public NegotiationLetter $record;

    public ?array $data = [];

    public function mount(NegotiationLetter $record): void
    {
        $this->record = $record;

        $this->form->fill($record->toArray());
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
            Section::make('Letter Header')
                ->columns(3)
                ->schema([
                    TextInput::make('letter_number')
                        ->label('Letter No.')
                        ->required(),

                    DatePicker::make('letter_date')
                        ->label('Letter Date')
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
                        ->preload()
                        ->required(),
                ]),

            Section::make('Parties')
                ->columns(3)
                ->schema([
                    Select::make('lc_receive_id')
                        ->label('LC')
                        ->options(
                            LcReceive::orderBy('lc_number')
                                ->pluck('lc_number', 'id')
                                ->toArray()
                        )
                        ->searchable()
                        ->preload(),

                    Select::make('customer_id')
                        ->label('Customer')
                        ->options(
                            Customer::orderBy('name')
                                ->pluck('name', 'id')
                                ->toArray()
                        )
                        ->searchable()
                        ->preload(),

                    Select::make('beneficiary_company_id')
                        ->label('Beneficiary Company')
                        ->options(
                            BeneficiaryCompany::orderBy('short_name')
                                ->pluck('short_name', 'id')
                                ->toArray()
                        )
                        ->searchable()
                        ->preload(),

                    Select::make('currency_id')
                        ->label('Currency')
                        ->options(
                            Currency::where('is_active', true)
                                ->orderBy('code')
                                ->pluck('code', 'id')
                                ->toArray()
                        )
                        ->searchable()
                        ->preload(),
                ]),

            Section::make('Amounts')
                ->columns(3)
                ->schema([
                    TextInput::make('invoice_amount')
                        ->label('Invoice Amount')
                        ->numeric()
                        ->required(),

                    TextInput::make('deductions')
                        ->label('Deductions')
                        ->numeric()
                        ->default(0),

                    TextInput::make('net_payable_amount')
                        ->label('Net Payable')
                        ->numeric()
                        ->required(),
                ]),

            Section::make('Bank Info')
                ->columns(2)
                ->schema([
                    TextInput::make('bank_name')
                        ->label('Negotiating Bank Name'),

                    TextInput::make('bank_branch')
                        ->label('Branch'),

                    TextInput::make('swift_code')
                        ->label('SWIFT Code'),
                ]),

            Section::make('Remarks')
                ->schema([
                    Textarea::make('remarks')
                        ->label('Remarks / Body Text')
                        ->rows(4),
                ]),
        ];
    }

    public function update(): void
    {
        $data = $this->form->getState();

        $data['updated_by'] = auth()->id();

        $this->record->update($data);

        session()->flash('success', 'Negotiation Letter updated successfully.');

        $this->redirectRoute('admin.trade.negotiation-letters.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.trade.negotiation-letter-edit');
    }
}