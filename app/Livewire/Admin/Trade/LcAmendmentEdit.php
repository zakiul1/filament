<?php

namespace App\Livewire\Admin\Trade;

use App\Models\LcAmendment;
use App\Models\LcReceive;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class LcAmendmentEdit extends Component implements HasSchemas, HasActions
{
    use InteractsWithSchemas;
    use InteractsWithActions;

    public LcAmendment $record;

    public ?array $data = [];

    public function mount(LcAmendment $record): void
    {
        $this->record = $record;
        $this->form->fill($record->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components($this->formSchema())
            ->statePath('data');
    }

    // same schema as create()
    protected function formSchema(): array
    {
        return (new LcAmendmentCreate())->formSchema();
    }

    public function update(): void
    {
        $data = $this->form->getState();
        $data['updated_by'] = auth()->id();

        $this->record->update($data);

        session()->flash('success', 'LC Amendment updated successfully.');

        $this->redirectRoute('admin.trade.lc-amendments.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.trade.lc-amendment-edit');
    }
}