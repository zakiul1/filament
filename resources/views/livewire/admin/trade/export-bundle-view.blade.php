<x-admin.master-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold">Export Bundle: {{ $exportBundle->bundle_no }}</h2>
                <p class="mt-1 text-sm text-zinc-500">
                    Customer: {{ optional($exportBundle->commercialInvoice?->customer)->name ?? '—' }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-4 sm:px-6 lg:px-8">

            @foreach ($requiredKeys as $key)
                @php $row = $docs[$key] ?? null; @endphp

                <div
                    class="overflow-hidden rounded-xl bg-white p-4 shadow-sm dark:bg-zinc-900 dark:ring-1 dark:ring-zinc-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-semibold">{{ strtoupper(str_replace('_', ' ', $key)) }}</div>
                            <div class="text-sm text-zinc-500">
                                Status: {{ $row?->status ?? 'missing' }}
                                @if ($row?->printed_at)
                                    • Printed: {{ $row->printed_at->format('Y-m-d H:i') }}
                                    • Count: {{ $row->print_count }}
                                @endif
                            </div>
                        </div>

                        <div class="flex gap-2">
                            {{-- Generate buttons --}}
                            @if (!$row && $key === 'packing_list')
                                <flux:button wire:click="generatePackingList" variant="primary">Generate</flux:button>
                            @elseif(!$row && $key === 'negotiation_letter')
                                <flux:button wire:click="generateNegotiationLetter" variant="primary">Generate
                                </flux:button>
                            @elseif(!$row && $key === 'boe_one')
                                <flux:button wire:click="generateBoeOne" variant="primary">Generate</flux:button>
                            @elseif(!$row && $key === 'boe_two')
                                <flux:button wire:click="generateBoeTwo" variant="primary">Generate</flux:button>
                            @endif

                            {{-- Print buttons --}}
                            @if ($row)
                                @php
                                    // IMPORTANT: you must map doc_key -> print route names you already have
                                    $printRoutes = [
                                        'commercial_invoice' => 'admin.trade.commercial-invoices.print',
                                        'packing_list' => 'admin.trade.packing-lists.print',
                                        'negotiation_letter' => 'admin.trade.negotiation-letters.print',
                                        'boe_one' => 'admin.trade.bills-of-exchange.print',
                                        'boe_two' => 'admin.trade.bills-of-exchange.print',
                                    ];

                                    $routeName = $printRoutes[$key] ?? null;
                                    $docId = $row->documentable_id;
                                @endphp

                                @if ($routeName)
                                    <flux:button type="button" variant="ghost"
                                        onclick="window.open('{{ route($routeName, ['record' => $docId]) }}','_blank')"
                                        wire:click="markPrinted('{{ $key }}')">
                                        Print
                                    </flux:button>
                                @else
                                    <span class="text-sm text-red-500">Print route missing</span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</x-admin.master-layout>
