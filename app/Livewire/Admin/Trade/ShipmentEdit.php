<?php

namespace App\Livewire\Admin\Trade;

use Livewire\Component;
use App\Models\Shipment;
use Illuminate\Support\Facades\Auth;

class ShipmentEdit extends Component
{
    public Shipment $shipment;

    public array $form = [];

    public function mount(Shipment $shipment): void
    {
        $this->shipment = $shipment->load(['exportBundle', 'commercialInvoice.customer']);
        $this->form = $this->shipment->only([
            'shipment_no',
            'shipment_date',
            'mode',
            'bl_awb_no',
            'vessel_name',
            'voyage_no',
            'container_no',
            'seal_no',
            'port_of_loading',
            'port_of_discharge',
            'final_destination',
            'etd',
            'eta',
            'forwarder_name',
            'forwarder_contact',
            'remarks',
            'status',
        ]);
    }

    public function save()
    {
        $data = $this->validate([
            'form.shipment_no' => ['required', 'string', 'max:50', 'unique:shipments,shipment_no,' . $this->shipment->id],
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

        $this->shipment->update([
            ...$data['form'],
            'updated_by' => Auth::id(),
        ]);

        session()->flash('success', 'Shipment updated.');
    }

    public function render()
    {
        return view('livewire.admin.trade.shipments-edit');
    }
}