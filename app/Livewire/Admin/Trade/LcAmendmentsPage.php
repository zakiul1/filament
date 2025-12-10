<?php

namespace App\Livewire\Admin\Trade;

use App\Models\LcAmendment;
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

class LcAmendmentsPage extends Component implements HasTable, HasActions, HasSchemas
{
    use InteractsWithTable;
    use InteractsWithActions;
    use InteractsWithSchemas;   // ✅ this trait defines $isCachingSchemas etc.

    /**
     * Required by Filament when mixing tables/actions with the
     * panel-less setup. We’re not actually translating content,
     * so just return null.
     */
    public function makeFilamentTranslatableContentDriver(): ?TranslatableContentDriver
    {
        return null;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                LcAmendment::query()->with('lcReceive')
            )
            ->columns([
                TextColumn::make('amendment_number')
                    ->label('Amendment No.')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('amendment_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('lcReceive.lc_number')
                    ->label('LC No.')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('amendment_type')
                    ->label('Type')
                    ->badge(),

                TextColumn::make('new_lc_amount')
                    ->label('LC Amount')
                    ->numeric(2)
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'gray' => 'draft',
                        'info' => 'confirmed',
                        'danger' => 'cancelled',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('lc_receive_id')
                    ->label('LC')
                    ->options(
                        LcReceive::orderBy('lc_number')
                            ->pluck('lc_number', 'id')
                            ->toArray()
                    ),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'confirmed' => 'Confirmed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('New Amendment')
                    ->icon('heroicon-o-plus')
                    ->url(fn() => route('admin.trade.lc-amendments.create')),
            ])
            ->actions([
                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn(LcAmendment $record) => route('admin.trade.lc-amendments.edit', $record)),

                DeleteAction::make(),
            ])
            ->defaultSort('amendment_date', 'desc')
            ->striped()
            ->emptyStateHeading('No LC Amendments yet')
            ->emptyStateDescription('Create your first amendment for an LC.');
    }

    public function render(): View
    {
        return view('livewire.admin.trade.lc-amendments-index');
    }
}