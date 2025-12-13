<x-admin.master-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                    Export Bundle: {{ $exportBundle->bundle_no }}
                </h2>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    Bundle Date: {{ optional($exportBundle->bundle_date)->format('d-M-Y') ?? '-' }}
                </p>
            </div>

            <div class="flex items-center gap-2">
                <flux:button type="button" variant="primary" wire:click="generateAllMissing">
                    Generate All Missing
                </flux:button>

                <flux:button type="button" variant="ghost" href="{{ route('admin.trade.export-bundles.index') }}">
                    Back
                </flux:button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8 space-y-4">

            {{-- Flash --}}
            @if (session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 p-3 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-red-700">
                    {{ session('error') }}
                </div>
            @endif

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

                    <div class="flex gap-2">
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
            </div>

            {{-- Documents registry --}}
            <div
                class="overflow-hidden rounded-xl bg-white p-4 shadow-sm dark:bg-zinc-900 dark:ring-1 dark:ring-zinc-700">
                <div class="text-base font-semibold text-zinc-900 dark:text-zinc-100 mb-3">
                    Documents
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ($docs as $doc)
                        @php
                            $label = match ($doc->document_type) {
                                'commercial_invoice' => 'Commercial Invoice',
                                'packing_list' => 'Packing List',
                                'negotiation_letter' => 'Negotiation / Submission Letter',
                                'boe_one' => 'Bill of Exchange (One)',
                                'boe_two' => 'Bill of Exchange (Two)',
                                default => ucfirst(str_replace('_', ' ', $doc->document_type)),
                            };

                            $number = $doc->document_id ? '#' . $doc->document_id : 'Not created yet';

                            // Route param names differ by document type
                            $routeParams = match ($doc->document_type) {
                                'commercial_invoice' => ['commercialInvoice' => $doc->document_id],
                                'packing_list' => ['packingList' => $doc->document_id],
                                'negotiation_letter' => ['negotiationLetter' => $doc->document_id],
                                'boe_one', 'boe_two' => ['billOfExchange' => $doc->document_id],
                                default => [],
                            };

                            $statusBadge =
                                $doc->status === 'ready'
                                    ? 'text-green-700 bg-green-50 border-green-200'
                                    : 'text-amber-700 bg-amber-50 border-amber-200';
                        @endphp

                        @if ($doc->document_type !== 'commercial_invoice')
                            <div class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $label }}</div>
                                        <div class="font-semibold">{{ $number }}</div>
                                    </div>

                                    <div class="rounded-md border px-2 py-1 text-xs {{ $statusBadge }}">
                                        {{ strtoupper($doc->status) }}
                                    </div>
                                </div>

                                <div class="mt-2 flex gap-2">
                                    @if ($doc->status === 'ready' && $doc->document_id && $doc->print_route)
                                        <a href="{{ route($doc->print_route, $routeParams) }}" target="_blank"
                                            rel="noopener noreferrer">
                                            <flux:button type="button" variant="primary">Print</flux:button>
                                        </a>
                                    @else
                                        @if ($doc->document_type === 'packing_list')
                                            <flux:button type="button" variant="primary"
                                                wire:click="generatePackingList">
                                                Generate
                                            </flux:button>
                                        @elseif ($doc->document_type === 'negotiation_letter')
                                            <flux:button type="button" variant="primary"
                                                wire:click="generateNegotiationLetter">
                                                Generate
                                            </flux:button>
                                        @elseif ($doc->document_type === 'boe_one')
                                            <flux:button type="button" variant="primary" wire:click="generateBoeOne">
                                                Generate
                                            </flux:button>
                                        @elseif ($doc->document_type === 'boe_two')
                                            <flux:button type="button" variant="primary" wire:click="generateBoeTwo">
                                                Generate
                                            </flux:button>
                                        @else
                                            <flux:button type="button" variant="primary" disabled>
                                                Generate
                                            </flux:button>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="mt-4 text-sm text-zinc-500 dark:text-zinc-400">
                    Tip: Use “Generate All Missing” to create everything in one click.
                </div>
            </div>

        </div>
    </div>
</x-admin.master-layout>
