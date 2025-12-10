<?php

namespace App\Livewire\Admin\Trade;

use App\Models\Customer;
use App\Models\LcReceive;
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
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class LcReceivesPage extends Component implements HasTable, HasActions, HasSchemas
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
                LcReceive::query()->with(['customer', 'currency'])
            )
            ->columns([
                TextColumn::make('lc_number')
                    ->label('LC No.')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('lc_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('currency.code')
                    ->label('Cur.'),

                TextColumn::make('lc_amount')
                    ->label('Amount')
                    ->numeric(2)
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'gray' => 'draft',
                        'info' => 'received',
                        'success' => 'confirmed',
                        'danger' => 'cancelled',
                        'warning' => 'expired',
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
                        'received' => 'Received',
                        'confirmed' => 'Confirmed',
                        'cancelled' => 'Cancelled',
                        'closed' => 'Closed',
                        'expired' => 'Expired',
                    ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('New LC Receive')
                    ->icon('heroicon-o-plus')
                    ->url(fn() => route('admin.trade.lc-receives.create')),
            ])
            ->actions([
                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn(LcReceive $record) => route('admin.trade.lc-receives.edit', $record)),
                DeleteAction::make(),
            ])
            ->defaultSort('lc_date', 'desc')
            ->striped()
            ->emptyStateHeading('No LC records yet')
            ->emptyStateDescription('Create your first LC to start tracking buyer LCs.');
    }

    public function render(): View
    {
        return view('livewire.admin.trade.lc-receives-index');
    }
}