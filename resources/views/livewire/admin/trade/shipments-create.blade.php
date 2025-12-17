<x-admin.master-layout>
    <div class="py-6">
        <div class="mx-auto max-w-3xl space-y-4 sm:px-6 lg:px-8">

            <div class="rounded-xl bg-white p-4 shadow-sm dark:bg-zinc-900 dark:ring-1 dark:ring-zinc-700">
                <div class="text-lg font-semibold">Create Shipment</div>
                <div class="text-sm text-zinc-500">Fill shipment details and save.</div>
            </div>

            <div class="rounded-xl bg-white p-4 shadow-sm dark:bg-zinc-900 dark:ring-1 dark:ring-zinc-700 space-y-3">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                    <div>
                        <label class="text-sm text-zinc-600">Shipment No</label>
                        <input class="w-full rounded-lg border px-3 py-2 dark:bg-zinc-800 dark:border-zinc-700"
                            wire:model.defer="form.shipment_no" />
                        @error('form.shipment_no')
                            <div class="text-red-600 text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm text-zinc-600">Shipment Date</label>
                        <input type="date"
                            class="w-full rounded-lg border px-3 py-2 dark:bg-zinc-800 dark:border-zinc-700"
                            wire:model.defer="form.shipment_date" />
                    </div>

                    <div>
                        <label class="text-sm text-zinc-600">Mode</label>
                        <select class="w-full rounded-lg border px-3 py-2 dark:bg-zinc-800 dark:border-zinc-700"
                            wire:model.defer="form.mode">
                            <option value="sea">Sea</option>
                            <option value="air">Air</option>
                            <option value="courier">Courier</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm text-zinc-600">B/L or AWB No</label>
                        <input class="w-full rounded-lg border px-3 py-2 dark:bg-zinc-800 dark:border-zinc-700"
                            wire:model.defer="form.bl_awb_no" />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                    <div>
                        <label class="text-sm text-zinc-600">ETD</label>
                        <input type="date"
                            class="w-full rounded-lg border px-3 py-2 dark:bg-zinc-800 dark:border-zinc-700"
                            wire:model.defer="form.etd" />
                    </div>
                    <div>
                        <label class="text-sm text-zinc-600">ETA</label>
                        <input type="date"
                            class="w-full rounded-lg border px-3 py-2 dark:bg-zinc-800 dark:border-zinc-700"
                            wire:model.defer="form.eta" />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                    <div>
                        <label class="text-sm text-zinc-600">Port of Loading</label>
                        <input class="w-full rounded-lg border px-3 py-2 dark:bg-zinc-800 dark:border-zinc-700"
                            wire:model.defer="form.port_of_loading" />
                    </div>
                    <div>
                        <label class="text-sm text-zinc-600">Port of Discharge</label>
                        <input class="w-full rounded-lg border px-3 py-2 dark:bg-zinc-800 dark:border-zinc-700"
                            wire:model.defer="form.port_of_discharge" />
                    </div>
                </div>

                <div>
                    <label class="text-sm text-zinc-600">Final Destination</label>
                    <input class="w-full rounded-lg border px-3 py-2 dark:bg-zinc-800 dark:border-zinc-700"
                        wire:model.defer="form.final_destination" />
                </div>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                    <div>
                        <label class="text-sm text-zinc-600">Forwarder Name</label>
                        <input class="w-full rounded-lg border px-3 py-2 dark:bg-zinc-800 dark:border-zinc-700"
                            wire:model.defer="form.forwarder_name" />
                    </div>
                    <div>
                        <label class="text-sm text-zinc-600">Forwarder Contact</label>
                        <input class="w-full rounded-lg border px-3 py-2 dark:bg-zinc-800 dark:border-zinc-700"
                            wire:model.defer="form.forwarder_contact" />
                    </div>
                </div>

                <div>
                    <label class="text-sm text-zinc-600">Remarks</label>
                    <textarea class="w-full rounded-lg border px-3 py-2 dark:bg-zinc-800 dark:border-zinc-700"
                        wire:model.defer="form.remarks"></textarea>
                </div>

                <div class="flex justify-end">
                    <flux:button wire:click="save" variant="primary">Save Shipment</flux:button>
                </div>
            </div>

        </div>
    </div>
</x-admin.master-layout>
