<?php

namespace App\Livewire\Admin\Master;

use App\Models\BankBranch;
use App\Models\Customer;
use App\Models\CustomerBank;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms;
use Filament\Schemas\Components\Grid;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CustomerBanksPage extends Component implements HasTable, HasForms, HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                CustomerBank::query()
                    ->with(['customer', 'bankBranch.bank'])
            )
            ->columns([
                TextColumn::make('customer.short_name')
                    ->label('Customer')
                    ->sortable()
                    ->searchable()
                    ->weight('medium'),

                TextColumn::make('bankBranch.bank.name')
                    ->label('Bank')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('bankBranch.name')
                    ->label('Branch')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('label')
                    ->label('Label')
                    ->limit(20),

                IconColumn::make('is_default_lc')
                    ->label('LC')
                    ->boolean(),

                IconColumn::make('is_default_tt')
                    ->label('TT')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_default_lc')
                    ->label('Default LC Bank'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Customer Bank')
                    ->modalHeading('Add Customer Bank')
                    ->form($this->getFormSchema()),
            ])
            ->actions([
                EditAction::make()
                    ->modalHeading(fn(CustomerBank $record) => 'Edit Bank for: ' . $record->customer->short_name)
                    ->form($this->getFormSchema()),

                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->defaultSort('customer.short_name')
            ->striped()
            ->emptyStateHeading('No customer banks found')
            ->emptyStateDescription('Add LC issuing banks per customer to use in LC Receive.');
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(2)->schema([
                Select::make('customer_id')
                    ->label('Customer')
                    ->options(
                        Customer::where('is_active', true)
                            ->orderBy('short_name')
                            ->pluck('short_name', 'id')
                    )
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('bank_branch_id')
                    ->label('Bank Branch')
                    ->options(
                        BankBranch::where('is_active', true)
                            ->with('bank')
                            ->get()
                            ->sortBy(fn($b) => $b->bank->name . ' - ' . $b->name)
                            ->mapWithKeys(fn($b) => [
                                $b->id => $b->bank->name . ' - ' . $b->name,
                            ])
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
            ]),

            TextInput::make('label')
                ->label('Label')
                ->maxLength(100)
                ->placeholder('e.g. Main LC Bank, Backup Bank'),

            Grid::make(3)->schema([
                Toggle::make('is_default_lc')
                    ->label('Default LC Bank')
                    ->default(true),

                Toggle::make('is_default_tt')
                    ->label('Used for TT')
                    ->default(false),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]),

            Textarea::make('notes')
                ->label('Internal Notes')
                ->rows(3)
                ->columnSpanFull(),
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.master.customer-banks-page');
    }
}