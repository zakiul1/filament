<x-admin.master-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                    Buyer Order Summary
                </h2>

                @if ($record)
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                        Order: <span class="font-medium">{{ $record->order_number }}</span>
                        @if ($record->customer?->name)
                            — Customer: <span class="font-medium">{{ $record->customer->name }}</span>
                        @endif
                    </p>
                @else
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                        Select a buyer order to view summary & print.
                    </p>
                @endif
            </div>

            @if ($record)
                <div class="flex gap-2">
                    <flux:button variant="primary" type="button"
                        onclick="window.open('{{ route('admin.reports.buyer-orders.summary.print', $record) }}', '_blank')">
                        Print PDF
                    </flux:button>

                    <flux:button variant="ghost" type="button"
                        wire:click="$redirect('{{ route('admin.reports.buyer-orders.summary.select') }}', true)">
                        Change Order
                    </flux:button>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">

            @if (!$record)
                <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-zinc-700">
                    <div class="text-sm text-zinc-500 dark:text-zinc-400">
                        No order selected.
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div
                        class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-zinc-700">
                        <div class="text-sm text-zinc-500 dark:text-zinc-400">Total Styles</div>
                        <div class="mt-1 text-xl font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ $summary['total_styles'] ?? 0 }}
                        </div>
                    </div>

                    <div
                        class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-zinc-700">
                        <div class="text-sm text-zinc-500 dark:text-zinc-400">Total Order Qty</div>
                        <div class="mt-1 text-xl font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ number_format((float) ($summary['total_order_qty'] ?? 0), 2) }}
                        </div>
                    </div>

                    <div
                        class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-zinc-700">
                        <div class="text-sm text-zinc-500 dark:text-zinc-400">Allocated Qty</div>
                        <div class="mt-1 text-xl font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ number_format((float) ($summary['allocated_qty'] ?? 0), 2) }}
                        </div>
                    </div>

                    <div
                        class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-zinc-700">
                        <div class="text-sm text-zinc-500 dark:text-zinc-400">Remaining Qty</div>
                        <div class="mt-1 text-xl font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ number_format((float) ($summary['remaining_qty'] ?? 0), 2) }}
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-zinc-700">
                    <div>
                        <div class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
                            Factory Allocation Breakdown
                        </div>
                        <div class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                            Value is calculated using (allocated qty × item unit_price).
                        </div>
                    </div>

                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="text-left text-zinc-600 dark:text-zinc-300">
                                <tr class="border-b border-zinc-200 dark:border-zinc-700">
                                    <th class="py-2 pr-4">Factory</th>
                                    <th class="py-2 pr-4 text-right">Allocated Qty</th>
                                    <th class="py-2 pr-4 text-right">Value</th>
                                </tr>
                            </thead>
                            <tbody class="text-zinc-900 dark:text-zinc-100">
                                @forelse($factoryRows as $row)
                                    <tr class="border-b border-zinc-100 dark:border-zinc-800">
                                        <td class="py-2 pr-4">{{ $row['factory_name'] }}</td>
                                        <td class="py-2 pr-4 text-right">
                                            {{ number_format((float) $row['total_qty'], 2) }}
                                        </td>
                                        <td class="py-2 pr-4 text-right">
                                            {{ number_format((float) $row['total_value'], 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-6 text-center text-zinc-500 dark:text-zinc-400">
                                            No allocations found for this order yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-admin.master-layout>
