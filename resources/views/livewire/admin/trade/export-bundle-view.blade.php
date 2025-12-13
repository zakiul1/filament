<x-admin.master-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                    Export Bundle: {{ $record->bundle_no }}
                </h2>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    Bundle Date: {{ optional($record->bundle_date)->format('d-M-Y') ?? '-' }}
                </p>
            </div>

            <flux:button type="button" variant="ghost" href="{{ route('admin.trade.export-bundles.index') }}">
                Back
            </flux:button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8 space-y-4">

            {{-- CI info --}}
            <div
                class="overflow-hidden rounded-xl bg-white p-4 shadow-sm dark:bg-zinc-900 dark:ring-1 dark:ring-zinc-700">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-sm text-zinc-500 dark:text-zinc-400">Commercial Invoice</div>
                        <div class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ $ci?->invoice_number ?? 'Not linked' }}
                        </div>
                        <div class="text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $ci?->customer?->name ?? '-' }}
                            @if ($ci?->invoice_date)
                                — {{ $ci->invoice_date->format('d-M-Y') }}
                            @endif
                        </div>
                    </div>

                    @if ($ci)
                        <a href="{{ route('admin.trade.commercial-invoices.print', ['commercialInvoice' => $ci->id]) }}"
                            target="_blank" rel="noopener noreferrer">
                            <flux:button type="button" variant="primary">
                                Print Commercial Invoice
                            </flux:button>
                        </a>
                    @else
                        <flux:button type="button" variant="primary" disabled>
                            Print Commercial Invoice
                        </flux:button>
                    @endif
                </div>
            </div>

            {{-- Documents --}}
            <div
                class="overflow-hidden rounded-xl bg-white p-4 shadow-sm dark:bg-zinc-900 dark:ring-1 dark:ring-zinc-700">
                <div class="text-base font-semibold text-zinc-900 dark:text-zinc-100 mb-3">
                    Documents
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                    {{-- Packing List --}}
                    <div class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                        <div class="text-sm text-zinc-500 dark:text-zinc-400">Packing List</div>
                        <div class="font-semibold">
                            {{ $packingList?->pl_number ?? 'Not created yet' }}
                        </div>

                        <div class="mt-2">
                            @if ($packingList)
                                <a href="{{ route('admin.trade.packing-lists.print', ['packingList' => $packingList->id]) }}"
                                    target="_blank" rel="noopener noreferrer">
                                    <flux:button type="button" variant="primary">Print Packing List</flux:button>
                                </a>
                            @else
                                <flux:button type="button" variant="primary" disabled>Print Packing List</flux:button>
                            @endif
                        </div>
                    </div>

                    {{-- Negotiation Letter --}}
                    <div class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                        <div class="text-sm text-zinc-500 dark:text-zinc-400">Negotiation / Submission Letter</div>
                        <div class="font-semibold">
                            {{ $negotiation?->letter_number ?? 'Not created yet' }}
                        </div>

                        <div class="mt-2">
                            @if ($negotiation)
                                <a href="{{ route('admin.trade.negotiation-letters.print', ['negotiationLetter' => $negotiation->id]) }}"
                                    target="_blank" rel="noopener noreferrer">
                                    <flux:button type="button" variant="primary">Print Negotiation Letter</flux:button>
                                </a>
                            @else
                                <flux:button type="button" variant="primary" disabled>Print Negotiation Letter
                                </flux:button>
                            @endif
                        </div>
                    </div>

                    {{-- BOE One --}}
                    <div class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                        <div class="text-sm text-zinc-500 dark:text-zinc-400">Bill of Exchange (One)</div>
                        <div class="font-semibold">
                            {{ $boeOne?->boe_number ?? 'Not created yet' }}
                        </div>

                        <div class="mt-2">
                            @if ($boeOne)
                                <a href="{{ route('admin.trade.bill-of-exchanges.print', ['billOfExchange' => $boeOne->id]) }}"
                                    target="_blank" rel="noopener noreferrer">
                                    <flux:button type="button" variant="primary">Print BOE One</flux:button>
                                </a>
                            @else
                                <flux:button type="button" variant="primary" disabled>Print BOE One</flux:button>
                            @endif
                        </div>
                    </div>

                    {{-- BOE Two --}}
                    <div class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                        <div class="text-sm text-zinc-500 dark:text-zinc-400">Bill of Exchange (Two)</div>
                        <div class="font-semibold">
                            {{ $boeTwo?->boe_number ?? 'Not created yet' }}
                        </div>

                        <div class="mt-2">
                            @if ($boeTwo)
                                <a href="{{ route('admin.trade.bill-of-exchanges.print', ['billOfExchange' => $boeTwo->id]) }}"
                                    target="_blank" rel="noopener noreferrer">
                                    <flux:button type="button" variant="primary">Print BOE Two</flux:button>
                                </a>
                            @else
                                <flux:button type="button" variant="primary" disabled>Print BOE Two</flux:button>
                            @endif
                        </div>
                    </div>

                </div>

                <div class="mt-4 text-sm text-zinc-500 dark:text-zinc-400">
                    Tip: If a document shows “Not created yet”, create it from Trade module first, then return here to
                    print.
                </div>
            </div>

        </div>
    </div>
</x-admin.master-layout>
