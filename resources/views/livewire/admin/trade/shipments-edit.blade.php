<x-admin.master-layout>
    <div class="py-6">
        <div class="mx-auto max-w-3xl space-y-4 sm:px-6 lg:px-8">

            <div class="rounded-xl bg-white p-4 shadow-sm dark:bg-zinc-900 dark:ring-1 dark:ring-zinc-700">
                <div class="text-lg font-semibold">Edit Shipment: {{ $shipment->shipment_no }}</div>
                @if (session('success'))
                    <div class="mt-2 text-sm text-green-600">{{ session('success') }}</div>
                @endif
            </div>

            @include('livewire.admin.trade.shipments-create') {{-- reuse same form layout --}}
        </div>
    </div>
</x-admin.master-layout>
