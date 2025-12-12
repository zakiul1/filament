<x-admin.master-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                    Edit Buyer Order
                </h2>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    Update order header & style items.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">

            {{-- ORDER FORM --}}
            <form wire:submit.prevent="update">
                <div
                    class="overflow-hidden rounded-xl bg-white p-4 shadow-sm
                           dark:bg-zinc-900 dark:ring-1 dark:ring-zinc-700">
                    {{ $this->form }}
                </div>

                <div class="mt-4 flex gap-2">
                    <flux:button type="submit" variant="primary">
                        Update
                    </flux:button>

                    <flux:button type="button" variant="ghost" as="a"
                        href="{{ route('admin.trade.buyer-orders.index') }}">
                        Back
                    </flux:button>

                </div>
            </form>

            {{-- FACTORY ALLOCATIONS --}}
            <div
                class="overflow-hidden rounded-xl bg-white p-4 shadow-sm
                       dark:bg-zinc-900 dark:ring-1 dark:ring-zinc-700">

                <div class="mb-3">
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
                        Factory Allocations
                    </h3>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                        Allocate order item quantities to one or more factories.
                    </p>
                </div>

                <div class="space-y-2">
                    @forelse($record->items()->orderBy('line_no')->get() as $item)
                        <div
                            class="flex items-center justify-between rounded-lg
                                   border border-zinc-200 px-3 py-2
                                   dark:border-zinc-700">

                            <div>
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                    Line {{ $item->line_no }} — {{ $item->item_description }}
                                </div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                    Order Qty: {{ number_format((float) $item->order_qty, 2) }}
                                </div>
                            </div>

                            <flux:button size="sm" variant="primary" as="a"
                                href="{{ route('admin.trade.buyer-order-items.allocations', $item->id) }}"
                                wire:navigate>
                                Manage Allocations
                            </flux:button>

                        </div>
                    @empty
                        <div class="text-sm text-zinc-500 dark:text-zinc-400">
                            No order items found. Add items above to enable factory allocation.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-admin.master-layout>
