<?php

namespace App\Livewire\Admin\Trade;

use App\Models\Shipment;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Support\Contracts\TranslatableContentDriver;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ShipmentsPage extends Component implements HasTable, HasActions, HasSchemas
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
                Shipment::query()->with(['exportBundle', 'commercialInvoice.customer'])
            )
            ->columns([
                TextColumn::make('shipment_no')->label('Shipment No')->searchable()->sortable(),
                TextColumn::make('shipment_date')->label('Date')->date()->sortable()->toggleable(),
                TextColumn::make('mode')->label('Mode')->sortable()->toggleable(),
                TextColumn::make('bl_awb_no')->label('B/L / AWB')->searchable()->toggleable(),
                TextColumn::make('exportBundle.bundle_no')->label('Bundle')->searchable()->toggleable(),
                TextColumn::make('commercialInvoice.invoice_number')->label('CI')->searchable()->toggleable(),
                TextColumn::make('commercialInvoice.customer.name')->label('Customer')->searchable()->toggleable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'gray' => 'draft',
                        'warning' => 'booked',
                        'info' => 'shipped',
                        'success' => 'delivered',
                        'danger' => 'cancelled',
                    ]),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'draft' => 'Draft',
                    'booked' => 'Booked',
                    'shipped' => 'Shipped',
                    'delivered' => 'Delivered',
                    'cancelled' => 'Cancelled',
                ]),
                SelectFilter::make('mode')->options([
                    'sea' => 'Sea',
                    'air' => 'Air',
                    'courier' => 'Courier',
                ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('New Shipment')
                    ->url(fn() => route('admin.trade.shipments.create')),
            ])
            ->actions([
                Action::make('edit')
                    ->label('Edit')
                    ->url(fn(Shipment $record) => route('admin.trade.shipments.edit', ['shipment' => $record->id])),
            ])
            ->defaultSort('id', 'desc')
            ->striped();
    }

    public function render(): View
    {
        return view('livewire.admin.trade.shipments-index');
    }
}