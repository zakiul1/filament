<x-admin.master-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                    Allocations — Order Item #{{ $item->id }}
                </h2>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    Ordered Qty: {{ (float) ($item->order_qty ?? 0) }}
                    • Allocated: {{ (float) ($allocatedQty ?? 0) }}
                    • Remaining: {{ (float) ($remainingQty ?? 0) }}
                </p>
            </div>

            <flux:button variant="ghost" type="button" onclick="window.history.back()">
                Back
            </flux:button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div
                class="overflow-hidden rounded-xl bg-white p-4 shadow-sm dark:bg-zinc-900 dark:ring-1 dark:ring-zinc-700">
                {{ $this->table }}
            </div>
        </div>
    </div>
</x-admin.master-layout>
