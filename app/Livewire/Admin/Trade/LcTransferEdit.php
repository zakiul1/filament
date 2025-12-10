<?php

namespace App\Livewire\Admin\Trade;

use App\Models\LcTransfer;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class LcTransferEdit extends Component implements HasSchemas, HasActions
{
    use InteractsWithSchemas;
    use InteractsWithActions;

    public LcTransfer $record;

    public ?array $data = [];

    public function mount(LcTransfer $lcTransfer): void
    {
        $this->record = $lcTransfer;

        $this->form->fill($lcTransfer->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(LcTransferCreate::getFormSchema())
            ->statePath('data');
    }

    public function update(): void
    {
        $data = $this->form->getState();

        $data['updated_by'] = auth()->id();

        $this->record->update($data);

        session()->flash('success', 'LC Transfer updated successfully.');

        $this->redirectRoute('admin.trade.lc-transfers.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.trade.lc-transfer-edit');
    }
}