<?php

namespace App\Livewire\Admin\Trade;

use Livewire\Component;
use App\Models\Shipment;
use App\Models\ExportBundle;
use Illuminate\Support\Facades\Auth;

class ShipmentCreate extends Component
{
    public ?int $export_bundle_id = null;

    public array $form = [
        'shipment_no' => '',
        'shipment_date' => null,
        'mode' => 'sea',
        'bl_awb_no' => null,
        'vessel_name' => null,
        'voyage_no' => null,
        'container_no' => null,
        'seal_no' => null,
        'port_of_loading' => null,
        'port_of_discharge' => null,
        'final_destination' => null,
        'etd' => null,
        'eta' => null,
        'forwarder_name' => null,
        'forwarder_contact' => null,
        'remarks' => null,
        'status' => 'draft',
    ];

    public function mount(): void
    {
        $this->export_bundle_id = (int) request()->query('export_bundle_id', 0) ?: null;

        $this->form['shipment_no'] = $this->nextShipmentNumber();
        $this->form['shipment_date'] = now()->toDateString();

        if ($this->export_bundle_id) {
            $bundle = ExportBundle::with('commercialInvoice')->find($this->export_bundle_id);
            if ($bundle) {
                $this->form['port_of_discharge'] = $bundle->commercialInvoice?->port_of_discharge ?? null;
                $this->form['final_destination'] = $bundle->commercialInvoice?->final_destination ?? null;
            }
        }
    }

    private function nextShipmentNumber(): string
    {
        $next = (int) (Shipment::max('id') ?? 0) + 1;
        return 'SHP-' . now()->format('Y') . '-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    public function save()
    {
        $data = $this->validate([
            'export_bundle_id' => ['nullable', 'integer', 'exists:export_bundles,id'],
            'form.shipment_no' => ['required', 'string', 'max:50', 'unique:shipments,shipment_no'],
            'form.shipment_date' => ['nullable', 'date'],
            'form.mode' => ['required', 'in:sea,air,courier'],
            'form.bl_awb_no' => ['nullable', 'string', 'max:100'],
            'form.vessel_name' => ['nullable', 'string', 'max:150'],
            'form.voyage_no' => ['nullable', 'string', 'max:50'],
            'form.container_no' => ['nullable', 'string', 'max:50'],
            'form.seal_no' => ['nullable', 'string', 'max:50'],
            'form.port_of_loading' => ['nullable', 'string', 'max:150'],
            'form.port_of_discharge' => ['nullable', 'string', 'max:150'],
            'form.final_destination' => ['nullable', 'string', 'max:150'],
            'form.etd' => ['nullable', 'date'],
            'form.eta' => ['nullable', 'date'],
            'form.forwarder_name' => ['nullable', 'string', 'max:150'],
            'form.forwarder_contact' => ['nullable', 'string', 'max:150'],
            'form.remarks' => ['nullable', 'string'],
            'form.status' => ['required', 'in:draft,booked,shipped,delivered,cancelled'],
        ]);

        $bundle = $this->export_bundle_id ? ExportBundle::with('commercialInvoice')->find($this->export_bundle_id) : null;

        $shipment = Shipment::create([
            'export_bundle_id' => $bundle?->id,
            'commercial_invoice_id' => $bundle?->commercial_invoice_id,
            ...$data['form'],
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('admin.trade.shipments.edit', ['shipment' => $shipment->id]);
    }

    public function render()
    {
        return view('livewire.admin.trade.shipments-create');
    }
}