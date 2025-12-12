<x-admin.master-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                    Factory Allocation Report
                </h2>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    Select a buyer order and print factory allocation PDF.
                </p>
            </div>
        </div>
    </x-slot>

    @php
        // Only build the route when an ID exists (avoids route() errors too)
        $printUrl = !empty($buyerOrderId)
            ? route('admin.reports.buyer-orders.factory-allocation.print', ['buyerOrder' => $buyerOrderId])
            : null;
    @endphp

    <div class="py-6">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div
                class="overflow-hidden rounded-xl bg-white p-4 shadow-sm
                       dark:bg-zinc-900 dark:ring-1 dark:ring-zinc-700">
                {{ $this->form }}
            </div>

            <div class="mt-4 flex gap-2">
                @if ($buyerOrderId)
                    <a href="{{ route('admin.reports.buyer-orders.factory-allocation.print', ['buyerOrder' => $buyerOrderId]) }}"
                        target="_blank" rel="noopener noreferrer">
                        <flux:button type="button" variant="primary">
                            Print Allocation PDF
                        </flux:button>
                    </a>
                @else
                    <flux:button type="button" variant="primary" disabled>
                        Print Allocation PDF P
                    </flux:button>
                @endif

                <flux:button type="button" variant="ghost" href="{{ route('admin.reports.trade.index') }}">
                    Back
                </flux:button>

            </div>
        </div>
    </div>
</x-admin.master-layout>
