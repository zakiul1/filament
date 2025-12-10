<?php

namespace App\Livewire\Admin\Trade;

use App\Models\CommercialInvoice;
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
use Filament\Schemas\Concerns\InteractsWithSchemas;   // ⬅️ NEW
use Filament\Schemas\Contracts\HasSchemas;            // ⬅️ NEW
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Filament\Actions\Action;

class CommercialInvoicesPage extends Component implements HasTable, HasActions, HasSchemas
{
    use InteractsWithTable;
    use InteractsWithActions;
    use InteractsWithSchemas; // ⬅️ NEW

    public function makeFilamentTranslatableContentDriver(): ?TranslatableContentDriver
    {
        return null;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                CommercialInvoice::query()->with(['customer', 'currency'])
            )
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Invoice No.')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('invoice_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('currency.code')
                    ->label('Cur.'),

                TextColumn::make('total_amount')
                    ->label('Total')
                    ->numeric(2)
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'gray' => 'draft',
                        'info' => 'confirmed',
                        'warning' => 'submitted',
                        'success' => 'paid',
                        'danger' => 'cancelled',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('customer_id')
                    ->label('Customer')
                    ->options(
                        Customer::orderBy('name')->pluck('name', 'id')->toArray()
                    ),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'confirmed' => 'Confirmed',
                        'submitted' => 'Submitted',
                        'paid' => 'Paid',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('New Commercial Invoice')
                    ->icon('heroicon-o-plus')
                    ->url(fn() => route('admin.trade.commercial-invoices.create')),
            ])
            ->actions([

                // ⬇️ NEW: Print button
                Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->url(fn(CommercialInvoice $record) => route('admin.trade.commercial-invoices.print', $record))
                    ->openUrlInNewTab(),
                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->url(
                        fn(CommercialInvoice $record) =>
                        route('admin.trade.commercial-invoices.edit', $record)
                    ),

                DeleteAction::make(),
            ])
            ->defaultSort('invoice_date', 'desc')
            ->striped()
            ->emptyStateHeading('No Commercial Invoices yet')
            ->emptyStateDescription('Create your first commercial invoice from a PI.');
    }

    public function render(): View
    {
        return view('livewire.admin.trade.commercial-invoices-index');
    }
}