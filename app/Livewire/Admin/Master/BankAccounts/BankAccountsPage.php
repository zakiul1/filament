<?php

namespace App\Livewire\Admin\Master\BankAccounts;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Contracts\HasSchemas;
use Livewire\Component;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Concerns\InteractsWithSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;




use Illuminate\Database\Eloquent\Builder;

use App\Models\BankAccount;

class BankAccountsPage extends Component implements HasTable, HasForms, HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;




    public function table(Table $table): Table
    {
        return $table
            ->query($this->baseQuery())
            ->columns([
                TextColumn::make('account_title')
                    ->label('Account Title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('account_number')
                    ->label('Account No')
                    ->searchable(),

                TextColumn::make('branch.bank.name')
                    ->label('Bank')
                    ->sortable(),

                TextColumn::make('branch.name')
                    ->label('Branch')
                    ->sortable(),

                TextColumn::make('country.name')
                    ->label('Country')
                    ->sortable(),

                TextColumn::make('currency.code')
                    ->label('Currency')
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->actions([
                Action::make('edit')
                    ->url(
                        fn(BankAccount $record) =>
                        route('admin.master.bank-accounts.edit', $record)
                    ),
            ])
            ->defaultSort('id', 'desc');
    }

    private function baseQuery(): Builder
    {
        return BankAccount::query()
            ->with(['branch.bank', 'country', 'currency']);
    }

    public function render()
    {
        return view('livewire.admin.master.bank-accounts.index');
    }
}