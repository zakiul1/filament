<?php

namespace App\Livewire\Admin\Trade;

use App\Models\Factory;
use App\Models\LcReceive;
use App\Models\LcTransfer;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
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
use Filament\Tables\Filters\SelectFilter;

class LcTransfersPage extends Component implements HasTable, HasActions, HasSchemas
{
    use InteractsWithTable;
    use InteractsWithActions;
    use InteractsWithSchemas;

    // For Filament v4 translation driver requirement
    public function makeFilamentTranslatableContentDriver(): ?TranslatableContentDriver
    {
        return null;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                LcTransfer::query()->with(['lcReceive.customer', 'factory', 'currency'])
            )
            ->columns([
                TextColumn::make('transfer_no')
                    ->label('Transfer No.')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('transfer_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('lcReceive.lc_number')
                    ->label('LC No.')
                    ->searchable(),

                TextColumn::make('lcReceive.customer.name')
                    ->label('Customer')
                    ->toggleable()
                    ->searchable(),

                TextColumn::make('factory.name')
                    ->label('Factory')
                    ->searchable(),

                TextColumn::make('currency.code')
                    ->label('Cur.'),

                TextColumn::make('transfer_amount')
                    ->label('Amount')
                    ->numeric(2)
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'gray' => 'draft',
                        'info' => 'issued',
                        'warning' => 'partially_utilized',
                        'success' => 'fully_utilized',
                        'danger' => 'cancelled',
                    ]),
            ])
            ->filters([
                SelectFilter::make('lc_receive_id')
                    ->label('Source LC')
                    ->options(
                        LcReceive::orderByDesc('lc_date')
                            ->pluck('lc_number', 'id')
                            ->toArray()
                    ),

                SelectFilter::make('factory_id')
                    ->label('Factory')
                    ->options(
                        Factory::orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray()
                    ),

                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'issued' => 'Issued',
                        'partially_utilized' => 'Partially Utilized',
                        'fully_utilized' => 'Fully Utilized',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('New Transfer')
                    ->icon('heroicon-o-plus')
                    ->url(fn() => route('admin.trade.lc-transfers.create')),
            ])
            ->actions([
                Action::make('print_letter')
                    ->label('Print Letter')
                    ->icon('heroicon-o-printer')
                    ->url(fn(LcTransfer $record) => route('admin.trade.lc-transfers.letter.print', $record))
                    ->openUrlInNewTab(),

                // Optional: if you also have normal LC Transfer print
                Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-document-text')
                    ->url(fn(LcTransfer $record) => route('admin.trade.lc-transfers.print', $record))
                    ->openUrlInNewTab()
                    ->visible(fn() => \Illuminate\Support\Facades\Route::has('admin.trade.lc-transfers.print')),

                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn(LcTransfer $record) => route('admin.trade.lc-transfers.edit', $record)),

                DeleteAction::make(),
            ])

            ->defaultSort('transfer_date', 'desc')
            ->striped()
            ->emptyStateHeading('No LC Transfers yet')
            ->emptyStateDescription('Create your first LC transfer to allocate LC value to factories.');
    }

    public function render(): View
    {
        return view('livewire.admin.trade.lc-transfers-index');
    }
}