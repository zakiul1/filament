<?php

namespace App\Livewire\Admin\Trade;

use App\Models\NegotiationLetter;
use App\Models\Customer;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;


use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Support\Contracts\TranslatableContentDriver;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;


class NegotiationLettersPage extends Component implements HasTable, HasActions, HasSchemas
{
    use InteractsWithTable, InteractsWithActions, InteractsWithSchemas;

    public function makeFilamentTranslatableContentDriver(): ?TranslatableContentDriver
    {
        return null;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(NegotiationLetter::query()->with(['customer', 'commercialInvoice']))
            ->columns([
                Tables\Columns\TextColumn::make('letter_number')->label('Letter No.')->searchable(),
                Tables\Columns\TextColumn::make('letter_date')->label('Date')->date(),
                Tables\Columns\TextColumn::make('commercialInvoice.invoice_number')->label('Commercial Invoice'),
                Tables\Columns\TextColumn::make('customer.name')->label('Customer'),
                Tables\Columns\TextColumn::make('net_payable_amount')->numeric()->label('Net Payable'),
                Tables\Columns\BadgeColumn::make('status')->colors([
                    'gray' => 'draft',
                    'success' => 'submitted',
                ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('New Negotiation Letter')
                    ->url(route('admin.trade.negotiation-letters.create')),
            ])
            ->actions([
                EditAction::make()
                    ->url(fn($record) => route('admin.trade.negotiation-letters.edit', $record)),
                Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->url(fn($record) => route('admin.trade.negotiation-letters.print', $record)),
            ])
            ->striped();
    }

    public function render(): View
    {
        return view('livewire.admin.trade.negotiation-letters-index');
    }
}