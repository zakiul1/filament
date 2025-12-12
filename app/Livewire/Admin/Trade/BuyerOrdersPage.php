<?php

namespace App\Livewire\Admin\Trade;

use App\Models\BuyerOrder;
use App\Models\Customer;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
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
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Filters\SelectFilter;



class BuyerOrdersPage extends Component implements HasTable, HasActions, HasSchemas
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
            ->query(BuyerOrder::query()->with('customer'))
            ->columns([
                TextColumn::make('order_number')->label('Order No')->searchable()->sortable(),
                TextColumn::make('order_date')->label('Date')->date()->sortable(),
                TextColumn::make('customer.name')->label('Customer')->searchable()->sortable(),
                TextColumn::make('buyer_po_number')->label('Buyer PO')->searchable(),
                TextColumn::make('season')->label('Season')->toggleable(),
                TextColumn::make('order_value')->label('Value')->numeric(2)->sortable(),

                BadgeColumn::make('status')->label('Status')->colors([
                    'gray' => 'draft',
                    'info' => 'running',
                    'success' => 'shipped',
                    'primary' => 'closed',
                    'danger' => 'cancelled',
                ]),
            ])
            ->filters([
                SelectFilter::make('customer_id')
                    ->label('Customer')
                    ->options(Customer::orderBy('name')->pluck('name', 'id')->toArray()),

                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'running' => 'Running',
                        'shipped' => 'Shipped',
                        'closed' => 'Closed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->headerActions([
                Action::make('create')
                    ->label('New Order')
                    ->icon('heroicon-o-plus')
                    ->url(fn() => route('admin.trade.buyer-orders.create')),
            ])
            ->actions([
                Action::make('factoryPdf')
                    ->label('Factory PDF')
                    ->icon('heroicon-o-printer')
                    ->url(fn(BuyerOrder $record) => route('admin.trade.buyer-orders.factory-allocation.print', $record))
                    ->openUrlInNewTab(),
                Action::make('print')
                    ->label('PDF')
                    ->icon('heroicon-o-printer')
                    ->url(fn(BuyerOrder $record) => route('admin.trade.buyer-orders.print', $record))
                    ->openUrlInNewTab(),
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn(BuyerOrder $record) => route('admin.trade.buyer-orders.edit', $record)),

                DeleteAction::make(),
            ])
            ->defaultSort('id', 'desc')
            ->striped()
            ->emptyStateHeading('No Orders yet')
            ->emptyStateDescription('Create the first Order/Style plan from PI/LC.');
    }

    public function render(): View
    {
        return view('livewire.admin.trade.buyer-orders-index');
    }
}