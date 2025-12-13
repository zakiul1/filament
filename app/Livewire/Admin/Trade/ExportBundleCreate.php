<?php

namespace App\Livewire\Admin\Trade;

use App\Models\CommercialInvoice;
use App\Models\ExportBundle;
use App\Models\ExportBundleDocument;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ExportBundleCreate extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public ?array $data = [
        'commercial_invoice_id' => null,
    ];

    public function mount(): void
    {
        $this->form->fill($this->data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->schema([
                Section::make('Select Commercial Invoice')
                    ->schema([
                        Select::make('commercial_invoice_id')
                            ->label('Commercial Invoice')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->options(
                                CommercialInvoice::query()
                                    ->with('customer')
                                    ->orderByDesc('id')
                                    ->get()
                                    ->mapWithKeys(fn($ci) => [
                                        $ci->id => $ci->invoice_number
                                            . ' — ' . ($ci->customer->name ?? 'No Customer')
                                            . ' — ' . optional($ci->invoice_date)->format('d-M-Y'),
                                    ])
                                    ->toArray()
                            ),
                    ]),
            ]);
    }

    public function createBundle(): void
    {
        $state = $this->form->getState();

        $this->validate([
            'data.commercial_invoice_id' => ['required', 'integer', 'exists:commercial_invoices,id'],
        ]);

        $bundle = DB::transaction(function () use ($state) {
            $nextNumber = (int) (ExportBundle::max('id') ?? 0) + 1;
            $bundleNo = 'EXP-' . str_pad((string) $nextNumber, 6, '0', STR_PAD_LEFT);

            /** @var ExportBundle $bundle */
            $bundle = ExportBundle::create([
                'commercial_invoice_id' => $state['commercial_invoice_id'],
                'bundle_no' => $bundleNo,
                'bundle_date' => now()->toDateString(),
                'status' => 'generated',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $docs = [
                [
                    'document_type' => 'commercial_invoice',
                    'print_route' => 'admin.trade.commercial-invoices.print',
                    'status' => 'ready',
                ],
                [
                    'document_type' => 'packing_list',
                    'print_route' => 'admin.trade.packing-lists.print',
                    'status' => 'ready',
                ],
                [
                    'document_type' => 'bill_of_exchange',
                    'print_route' => 'admin.trade.bill-of-exchanges.print',
                    'status' => 'ready',
                ],
                [
                    'document_type' => 'negotiation_letter',
                    'print_route' => 'admin.trade.negotiation-letters.print',
                    'status' => 'ready',
                ],
            ];

            foreach ($docs as $doc) {
                ExportBundleDocument::create([
                    'export_bundle_id' => $bundle->id,
                    'document_type' => $doc['document_type'],
                    'document_id' => null, // we are reusing CI id when printing
                    'print_route' => $doc['print_route'],
                    'status' => $doc['status'],
                ]);
            }

            return $bundle;
        });

        $this->redirectRoute('admin.trade.export-bundles.show', ['record' => $bundle->id], navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.trade.export-bundle-create');
    }
}