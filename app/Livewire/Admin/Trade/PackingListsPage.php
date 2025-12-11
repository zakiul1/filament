<?php

namespace App\Livewire\Admin\Trade;

use App\Models\PackingList;
use App\Models\CommercialInvoice;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Support\Contracts\TranslatableContentDriver;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;

class PackingListsPage extends Component implements HasTable, HasActions, HasSchemas
{
    use InteractsWithTable;
    use InteractsWithActions;
    use InteractsWithSchemas;

    // Required by Filament (we're not actually translating)
    public function makeFilamentTranslatableContentDriver(): ?TranslatableContentDriver
    {
        return null;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PackingList::query()
                    ->with(['commercialInvoice.customer', 'beneficiaryCompany'])
            )
            ->columns([
                TextColumn::make('pl_number')
                    ->label('PL No.')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('pl_date')
                    ->label('PL Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('commercialInvoice.invoice_number')
                    ->label('Invoice No.')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('commercialInvoice.customer.name')
                    ->label('Customer')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('total_cartons')
                    ->label('Cartons')
                    ->sortable(),

                TextColumn::make('total_quantity')
                    ->label('Qty')
                    ->sortable(),

                TextColumn::make('total_gw')
                    ->label('G.W. (kg)')
                    ->numeric(2)
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'gray' => 'draft',
                        'info' => 'confirmed',
                        'success' => 'closed',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('commercial_invoice_id')
                    ->label('Commercial Invoice')
                    ->options(
                        CommercialInvoice::orderBy('invoice_number')
                            ->pluck('invoice_number', 'id')
                            ->toArray()
                    ),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'confirmed' => 'Confirmed',
                        'closed' => 'Closed',
                    ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('New Packing List')
                    ->icon('heroicon-o-plus')
                    ->url(fn() => route('admin.trade.packing-lists.create')),
            ])
            ->actions([
                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->url(
                        fn(PackingList $record) =>
                        route('admin.trade.packing-lists.edit', $record)
                    ),

                Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->url(
                        fn(PackingList $record) =>
                        route('admin.trade.packing-lists.print', $record)
                    )
                    ->openUrlInNewTab(),

                DeleteAction::make(),
            ])
            ->defaultSort('pl_date', 'desc')
            ->striped()
            ->emptyStateHeading('No Packing Lists yet')
            ->emptyStateDescription('Create your first Packing List for a Commercial Invoice.');
    }

    public function render(): View
    {
        return view('livewire.admin.trade.packing-lists-index');
    }
}