<?php

namespace App\Livewire\Admin\Trade;

use Livewire\Component;

use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

use Filament\Actions\Action;
use Filament\Support\Contracts\TranslatableContentDriver;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

use App\Models\ExportBundle;
use App\Models\ExportBundleEvent;

class ExportBundleReportsPage extends Component implements HasTable
{
    use InteractsWithTable;

    // ✅ Fix for: must implement makeFilamentTranslatableContentDriver
    public function makeFilamentTranslatableContentDriver(): ?TranslatableContentDriver
    {
        return null;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->baseQuery())
            ->columns([
                TextColumn::make('bundle_no')
                    ->label('Bundle No.')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('commercialInvoice.customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->toggleable(),

                BadgeColumn::make('report_status')
                    ->label('Report Status')
                    ->getStateUsing(function (ExportBundle $r) {
                        if ($r->bank_accepted_at)
                            return 'bank_accepted';
                        if ($r->couriered_at)
                            return 'couriered';
                        if ($r->submitted_at)
                            return 'submitted';
                        if ($r->locked_at)
                            return 'locked';
                        return 'draft';
                    })
                    ->colors([
                        'success' => 'bank_accepted',
                        'warning' => 'couriered',
                        'info' => 'submitted',
                        'danger' => 'locked',
                        'gray' => 'draft',
                    ]),

                TextColumn::make('submitted_at')
                    ->label('Submitted')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                TextColumn::make('submission_ref')
                    ->label('Submission Ref')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('courier_ref')
                    ->label('Courier Ref')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('bank_ref')
                    ->label('Bank Ref')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('total_prints')
                    ->label('Total Prints')
                    ->sortable()
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('report_status')
                    ->label('Report Status')
                    ->options([
                        'draft' => 'Draft',
                        'locked' => 'Locked',
                        'submitted' => 'Submitted (Pending Courier)',
                        'couriered' => 'Couriered (Pending Bank)',
                        'bank_accepted' => 'Bank Accepted',
                    ])
                    ->query(function (Builder $query, array $data) {
                        $v = $data['value'] ?? null;

                        return match ($v) {
                            'bank_accepted' => $query->whereNotNull('bank_accepted_at'),
                            'couriered' => $query->whereNull('bank_accepted_at')->whereNotNull('couriered_at'),
                            'submitted' => $query->whereNull('couriered_at')->whereNotNull('submitted_at'),
                            'locked' => $query->whereNull('submitted_at')->whereNotNull('locked_at'),
                            'draft' => $query->whereNull('submitted_at')->whereNull('locked_at'),
                            default => $query,
                        };
                    }),

                Filter::make('submitted_today')
                    ->label('Submitted Today')
                    ->query(fn(Builder $q) => $q->whereDate('submitted_at', now()->toDateString())),
            ])
            ->actions([
                Action::make('open')
                    ->label('Open')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn(ExportBundle $record) => route('admin.trade.export-bundles.show', ['exportBundle' => $record->id]))
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('id', 'desc')
            ->striped();
    }

    private function baseQuery(): Builder
    {
        return ExportBundle::query()
            ->with(['commercialInvoice.customer'])
            ->withSum('documents as total_prints', 'print_count')
            ->addSelect([
                'courier_ref' => ExportBundleEvent::query()
                    ->select('ref')
                    ->whereColumn('export_bundle_id', 'export_bundles.id')
                    ->where('event', 'couriered')
                    ->orderByDesc('event_at')
                    ->limit(1),

                'bank_ref' => ExportBundleEvent::query()
                    ->select('ref')
                    ->whereColumn('export_bundle_id', 'export_bundles.id')
                    ->where('event', 'bank_accepted')
                    ->orderByDesc('event_at')
                    ->limit(1),

                'couriered_at' => ExportBundleEvent::query()
                    ->select('event_at')
                    ->whereColumn('export_bundle_id', 'export_bundles.id')
                    ->where('event', 'couriered')
                    ->orderByDesc('event_at')
                    ->limit(1),

                'bank_accepted_at' => ExportBundleEvent::query()
                    ->select('event_at')
                    ->whereColumn('export_bundle_id', 'export_bundles.id')
                    ->where('event', 'bank_accepted')
                    ->orderByDesc('event_at')
                    ->limit(1),
            ]);
    }

    public function render(): View
    {
        return view('livewire.admin.trade.export-bundles-reports');
    }
}