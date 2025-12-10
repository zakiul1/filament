<?php

namespace App\Livewire\Admin\Trade;

use App\Models\BeneficiaryBankAccount;
use App\Models\BeneficiaryCompany;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\CustomerBank;
use App\Models\Incoterm;
use App\Models\LcReceive;
use App\Models\PaymentTerm;
use App\Models\Port;
use App\Models\ShipmentMode;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class LcReceiveCreate extends Component implements HasSchemas, HasActions
{
    use InteractsWithSchemas;
    use InteractsWithActions;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'lc_date' => now()->toDateString(),
            'status' => 'draft',
            'lc_type' => 'SIGHT',
            'partial_shipment_allowed' => 1,
            'transshipment_allowed' => 1,
            'tolerance_plus' => 0,
            'tolerance_minus' => 0,
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
            Section::make('LC Header')
                ->columns(4)
                ->schema([
                    TextInput::make('lc_number')
                        ->label('LC Number')
                        ->required()
                        ->maxLength(100),

                    DatePicker::make('lc_date')
                        ->label('LC Date')
                        ->required()
                        ->native(false),

                    Select::make('lc_type')
                        ->label('LC Type')
                        ->options([
                            'SIGHT' => 'Sight',
                            'USANCE' => 'Usance',
                            'DEFERRED' => 'Deferred',
                            'TRANSFERABLE' => 'Transferable',
                            'BACK_TO_BACK' => 'Back-to-Back',
                        ])
                        ->default('SIGHT'),

                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'received' => 'Received',
                            'confirmed' => 'Confirmed',
                            'cancelled' => 'Cancelled',
                            'closed' => 'Closed',
                            'expired' => 'Expired',
                        ])
                        ->default('draft'),
                ]),

            Section::make('Parties & Banks')
                ->columns(3)
                ->schema([
                    Select::make('customer_id')
                        ->label('Applicant / Customer')
                        ->options(
                            Customer::orderBy('name')
                                ->pluck('name', 'id')
                                ->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('beneficiary_company_id')
                        ->label('Beneficiary Company')
                        ->options(
                            BeneficiaryCompany::orderBy('short_name')
                                ->pluck('short_name', 'id')
                                ->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->required(),

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

                    Select::make('customer_bank_id')
                        ->label('Issuing Bank (Customer Bank)')
                        ->options(
                            CustomerBank::with('bankBranch.bank')
                                ->get()
                                ->mapWithKeys(fn($row) => [
                                    $row->id => ($row->bankBranch->bank->short_name ?? '') . ' - ' . $row->account_no,
                                ])
                                ->toArray()
                        )
                        ->searchable(),

                    Select::make('beneficiary_bank_account_id')
                        ->label('Beneficiary Bank A/C')
                        ->options(
                            BeneficiaryBankAccount::with('bankBranch.bank', 'beneficiaryCompany')
                                ->get()
                                ->mapWithKeys(fn($row) => [
                                    $row->id => ($row->beneficiaryCompany->short_name ?? '') .
                                        ' - ' .
                                        ($row->bankBranch->bank->short_name ?? '') .
                                        ' (' . $row->account_no . ')',
                                ])
                                ->toArray()
                        )
                        ->searchable(),

                    TextInput::make('reference_pi_number')
                        ->label('Linked PI No (Optional)')
                        ->maxLength(100),
                ]),

            Section::make('Amount & Validity')
                ->columns(4)
                ->schema([
                    TextInput::make('lc_amount')
                        ->label('LC Amount')
                        ->numeric()
                        ->required(),

                    TextInput::make('tolerance_plus')
                        ->label('Tolerance +%')
                        ->numeric()
                        ->default(0)
                        ->required(),

                    TextInput::make('tolerance_minus')
                        ->label('Tolerance -%')
                        ->numeric()
                        ->default(0)
                        ->required(),

                    TextInput::make('lc_amount_in_words')
                        ->label('Amount in Words')
                        ->maxLength(500)
                        ->columnSpan(4),

                    DatePicker::make('expiry_date')
                        ->label('Expiry Date')
                        ->native(false),

                    DatePicker::make('last_shipment_date')
                        ->label('Last Shipment Date')
                        ->native(false),

                    TextInput::make('presentation_days')
                        ->label('Presentation Days')
                        ->numeric()
                        ->helperText('No. of days after shipment allowed for document presentation.'),
                ]),

            Section::make('Shipment Details')
                ->columns(3)
                ->schema([
                    Select::make('incoterm_id')
                        ->label('Incoterm')
                        ->options(
                            Incoterm::orderBy('code')
                                ->pluck('code', 'id')
                                ->toArray()
                        )
                        ->searchable()
                        ->preload(),

                    Select::make('shipment_mode_id')
                        ->label('Shipment Mode')
                        ->options(
                            ShipmentMode::orderBy('name')
                                ->pluck('name', 'id')
                                ->toArray()
                        )
                        ->searchable()
                        ->preload(),

                    Select::make('port_of_loading_id')
                        ->label('Port of Loading')
                        ->options(
                            Port::orderBy('name')
                                ->pluck('name', 'id')
                                ->toArray()
                        )
                        ->searchable()
                        ->preload(),

                    Select::make('port_of_discharge_id')
                        ->label('Port of Discharge')
                        ->options(
                            Port::orderBy('name')
                                ->pluck('name', 'id')
                                ->toArray()
                        )
                        ->searchable()
                        ->preload(),

                    Select::make('partial_shipment_allowed')
                        ->label('Partial Shipment')
                        ->options([
                            1 => 'Allowed',
                            0 => 'Not Allowed',
                        ])
                        ->default(1),

                    Select::make('transshipment_allowed')
                        ->label('Transshipment')
                        ->options([
                            1 => 'Allowed',
                            0 => 'Not Allowed',
                        ])
                        ->default(1),

                    TextInput::make('reimbursement_bank')
                        ->label('Reimbursement Bank')
                        ->maxLength(255),
                ]),

            Section::make('Notes')
                ->columns(2)
                ->schema([
                    Textarea::make('remarks')
                        ->label('LC Remarks')
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

        // ensure numeric defaults so DB never gets NULL
        $data['tolerance_plus'] = (float) ($data['tolerance_plus'] ?? 0);
        $data['tolerance_minus'] = (float) ($data['tolerance_minus'] ?? 0);

        // booleans (stored as tinyint/bool)
        $data['partial_shipment_allowed'] = (bool) ($data['partial_shipment_allowed'] ?? false);
        $data['transshipment_allowed'] = (bool) ($data['transshipment_allowed'] ?? false);

        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        LcReceive::create($data);

        session()->flash('success', 'LC received and saved successfully.');

        $this->redirectRoute('admin.trade.lc-receives.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.trade.lc-receive-create');
    }
}