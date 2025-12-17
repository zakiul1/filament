<?php

namespace App\Livewire\Admin\Trade;

use App\Models\ExportBundle;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Support\Contracts\TranslatableContentDriver;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
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

    public function makeFilamentTranslatableContentDriver(): ?TranslatableContentDriver
    {
        return null;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ExportBundle::query()->with([
                    'commercialInvoice.customer',
                    'submittedBy',
                ])
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
                    ->getStateUsing(function (ExportBundle $record) {
                        if ($record->closed_at) {
                            return 'closed';
                        }
                        if ($record->submitted_at) {
                            return 'submitted';
                        }
                        if ($record->locked_at) {
                            return 'locked';
                        }
                        return $record->status ?? 'draft';
                    })
                    ->colors([
                        'purple' => 'closed',
                        'info' => 'submitted',
                        'danger' => 'locked',
                        'success' => 'generated',
                        'gray' => 'draft',
                    ]),

                TextColumn::make('submittedBy.name')
                    ->label('Submitted By')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('submitted_at')
                    ->label('Submitted At')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status_filter')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'locked' => 'Locked',
                        'submitted' => 'Submitted',
                        'closed' => 'Closed',
                    ])
                    ->query(function ($query, array $data) {
                        $value = $data['value'] ?? null;

                        return match ($value) {
                            'closed' => $query->whereNotNull('closed_at'),
                            'submitted' => $query->whereNull('closed_at')->whereNotNull('submitted_at'),
                            'locked' => $query->whereNull('closed_at')->whereNull('submitted_at')->whereNotNull('locked_at'),
                            'draft' => $query->whereNull('closed_at')->whereNull('submitted_at')->whereNull('locked_at'),
                            default => $query,
                        };
                    }),
            ])
            ->headerActions([
                // ✅ Option B: Reports button
                Action::make('reports')
                    ->label('Reports')
                    ->icon('heroicon-o-chart-bar')
                    ->url(fn() => route('admin.trade.export-bundles.reports')),

                CreateAction::make()
                    ->label('New Bundle')
                    ->icon('heroicon-o-plus')
                    ->url(fn() => route('admin.trade.export-bundles.create')),
            ])
            ->actions([
                Action::make('view')
                    ->label('Open')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn(ExportBundle $record) => route('admin.trade.export-bundles.show', ['exportBundle' => $record->id])),

                DeleteAction::make()
                    ->visible(fn(ExportBundle $record) => !$record->locked_at && !$record->submitted_at && !$record->closed_at),
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