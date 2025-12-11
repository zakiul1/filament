<?php

namespace App\Livewire\Admin\Trade;

use App\Models\BillOfExchange;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class BillOfExchangeEdit extends Component implements HasSchemas, HasActions
{
    use InteractsWithSchemas;
    use InteractsWithActions;

    public BillOfExchange $record;

    public ?array $data = [];

    public function mount(BillOfExchange $record): void
    {
        $this->record = $record;

        $this->form->fill(
            $record->toArray()
        );
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components($this->getFormSchema())
            ->statePath('data');
    }

    // Reuse schema from create:
    protected function getFormSchema(): array
    {
        return (new BillOfExchangeCreate())->getFormSchema();
    }

    public function update(): void
    {
        $data = $this->form->getState();

        if (empty($data['maturity_date']) && !empty($data['issue_date']) && !empty($data['tenor_days'])) {
            $data['maturity_date'] = now()
                ->parse($data['issue_date'])
                ->addDays((int) $data['tenor_days'])
                ->toDateString();
        }

        $data['updated_by'] = auth()->id();

        $this->record->update($data);

        session()->flash('success', 'Bill of Exchange updated successfully.');

        $this->redirectRoute('admin.trade.bill-of-exchanges.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.trade.bill-of-exchange-edit');
    }
}