<?php

namespace App\Livewire\Admin\Trade;

use App\Models\ExportBundle;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;

use Filament\Actions\EditAction;
use Filament\Support\Contracts\TranslatableContentDriver;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;


class ExportBundlesPage extends Component implements HasTable, HasActions, HasSchemas
{
    use InteractsWithTable;
    use InteractsWithActions;
    use InteractsWithSchemas;

    // Filament v4 translation driver requirement
    public function makeFilamentTranslatableContentDriver(): ?TranslatableContentDriver
    {
        return null;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ExportBundle::query()->with(['commercialInvoice.customer'])
            )
            ->columns([
                TextColumn::make('bundle_no')
                    ->label('Bundle No.')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('bundle_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('commercialInvoice.invoice_number')
                    ->label('Commercial Invoice')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('commercialInvoice.customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->toggleable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'generated',
                        'gray' => 'draft',
                        'warning' => 'archived',
                    ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('New Bundle')
                    ->icon('heroicon-o-plus')
                    ->url(fn() => route('admin.trade.export-bundles.create')),
            ])
            ->actions([
                Action::make('view')
                    ->label('Open')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn(ExportBundle $record) => route('admin.trade.export-bundles.show', ['exportBundle' => $record->id]))

                ,

                DeleteAction::make(),
            ])
            ->defaultSort('id', 'desc')
            ->striped()
            ->emptyStateHeading('No Export Bundles yet')
            ->emptyStateDescription('Create an export bundle from a commercial invoice to print all export documents.');
    }

    public function render(): View
    {
        return view('livewire.admin.trade.export-bundles-index');
    }
}