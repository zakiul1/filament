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

        /** @var ExportBundle $bundle */
        $bundle = DB::transaction(function () use ($state) {

            $nextNumber = (int) (ExportBundle::max('id') ?? 0) + 1;
            $bundleNo = 'EXP-' . str_pad((string) $nextNumber, 6, '0', STR_PAD_LEFT);

            $bundle = ExportBundle::create([
                'commercial_invoice_id' => $state['commercial_invoice_id'],
                'bundle_no' => $bundleNo,
                'bundle_date' => now()->toDateString(),
                'status' => 'generated',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // ✅ Registry rows using your new structure
            $docs = [
                'commercial_invoice',
                'packing_list',
                'negotiation_letter',
                'boe_one',
                'boe_two',
            ];

            foreach ($docs as $key) {

                // only CI is linked immediately, others are missing for now
                $docType = null;
                $docId = null;
                $status = 'missing';
                $generatedAt = null;

                if ($key === 'commercial_invoice') {
                    $docType = CommercialInvoice::class;
                    $docId = (int) $state['commercial_invoice_id'];
                    $status = 'ready';
                    $generatedAt = now();
                }

                ExportBundleDocument::create([
                    'export_bundle_id' => $bundle->id,
                    'doc_key' => $key,
                    'documentable_type' => $docType,
                    'documentable_id' => $docId,
                    'status' => $status,
                    'generated_at' => $generatedAt,
                    'print_count' => 0,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            }

            return $bundle;
        });

        $this->redirectRoute('admin.trade.export-bundles.show', ['exportBundle' => $bundle->id], navigate: true);

    }

    public function render(): View
    {
        return view('livewire.admin.trade.export-bundle-create');
    }
}