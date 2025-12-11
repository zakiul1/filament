<x-admin.master-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                    Create Packing List
                </h2>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    Generate a packing list for shipment based on commercial invoice details.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div
                class="overflow-hidden rounded-xl bg-white p-6 shadow-sm dark:bg-zinc-900 dark:ring-1 dark:ring-zinc-700">

                <form wire:submit.prevent="create">
                    {{ $this->form }}

                    <div class="mt-6 flex justify-end">
                        <x-filament::button type="submit" icon="heroicon-o-check">
                            Save Packing List
                        </x-filament::button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-admin.master-layout>
