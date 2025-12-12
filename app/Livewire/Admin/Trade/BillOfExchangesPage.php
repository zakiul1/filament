<?php

namespace App\Livewire\Admin\Trade;

use App\Models\BillOfExchange;
use App\Models\Customer;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Support\Contracts\TranslatableContentDriver;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Filament\Actions\Action;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Filters\SelectFilter;

class BillOfExchangesPage extends Component implements HasTable, HasActions, HasSchemas
{
    use InteractsWithTable;
    use InteractsWithActions;
    use InteractsWithSchemas;

    public function makeFilamentTranslatableContentDriver(): ?TranslatableContentDriver
    {
        return null;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                BillOfExchange::query()->with(['customer', 'currency'])
            )
            ->columns([
                TextColumn::make('boe_number')
                    ->label('BOE No.')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('issue_date')
                    ->label('Issue Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('boe_type')
                    ->label('Type')
                    ->badge(),

                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('currency.code')
                    ->label('Cur.'),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->numeric(2)
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'gray' => 'draft',
                        'info' => 'sent',
                        'success' => 'accepted',
                        'danger' => 'cancelled',
                    ]),
            ])
            ->filters([
                SelectFilter::make('customer_id')
                    ->label('Customer')
                    ->options(
                        Customer::orderBy('name')->pluck('name', 'id')->toArray()
                    ),

                SelectFilter::make('boe_type')
                    ->options([
                        'FIRST' => 'First',
                        'SECOND' => 'Second',
                    ]),

                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Sent',
                        'accepted' => 'Accepted',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('New BOE')
                    ->icon('heroicon-o-plus')
                    ->url(fn() => route('admin.trade.bill-of-exchanges.create')),
            ])
            ->actions([
                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn(BillOfExchange $record) => route('admin.trade.bill-of-exchanges.edit', $record)),

                Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->url(fn(BillOfExchange $record) => route('admin.trade.bill-of-exchanges.print', $record))
                    ->openUrlInNewTab(),

                DeleteAction::make(),
            ])
            ->defaultSort('issue_date', 'desc')
            ->striped()
            ->emptyStateHeading('No Bills of Exchange yet')
            ->emptyStateDescription('Create your first Bill of Exchange for an LC / Commercial Invoice.');
    }

    public function render(): View
    {
        return view('livewire.admin.trade.bill-of-exchanges-index');
    }
}