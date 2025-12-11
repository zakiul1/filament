<?php

namespace App\Livewire\Admin\Trade;

use App\Models\SampleInvoice;
use App\Models\Customer;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Support\Contracts\TranslatableContentDriver;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;

class SampleInvoicesPage extends Component implements HasTable, HasActions, HasSchemas
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
                SampleInvoice::query()->with(['customer', 'currency'])
            )
            ->columns([
                TextColumn::make('sample_number')
                    ->label('Sample Inv. No.')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('sample_date')
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
                        'info' => 'sent',
                        'success' => 'approved',
                        'danger' => 'cancelled',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('customer_id')
                    ->label('Customer')
                    ->options(
                        Customer::orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray()
                    ),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Sent',
                        'approved' => 'Approved',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('New Sample Invoice')
                    ->icon('heroicon-o-plus')
                    ->url(fn() => route('admin.trade.sample-invoices.create')),
            ])
            ->actions([
                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn(SampleInvoice $record) => route('admin.trade.sample-invoices.edit', $record)),
                Action::make('print')
                    ->label('PDF')
                    ->icon('heroicon-o-printer')
                    ->url(fn(SampleInvoice $record) => route('admin.trade.sample-invoices.print', $record))
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('sample_date', 'desc')
            ->striped()
            ->emptyStateHeading('No Sample Invoices yet')
            ->emptyStateDescription('Create a sample invoice for sample shipments to buyers.');
    }

    public function render(): View
    {
        return view('livewire.admin.trade.sample-invoices-index');
    }
}