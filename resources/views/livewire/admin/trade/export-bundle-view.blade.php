<x-admin.master-layout>
    @php
        $allReady = true;

        foreach ($requiredKeys as $k) {
            $r = $docs[$k] ?? null;
            if (!$r || !$r->documentable_id) {
                $allReady = false;
                break;
            }
        }

        $isLocked = (bool) $exportBundle->locked_at;
        $isSubmitted = (bool) $exportBundle->submitted_at;

        // ✅ safe timeline collection
        $timeline = collect($events ?? []);
    @endphp

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-4 sm:px-6 lg:px-8">

            {{-- TOP BAR --}}
            <div
                class="flex items-start justify-between rounded-xl bg-white p-4 shadow-sm dark:bg-zinc-900 dark:ring-1 dark:ring-zinc-700">
                <div>
                    <h2 class="text-lg font-semibold">Export Bundle: {{ $exportBundle->bundle_no }}</h2>
                    <p class="mt-1 text-sm text-zinc-500">
                        Customer: {{ optional($exportBundle->commercialInvoice?->customer)->name ?? '—' }}
                    </p>

                    @if ($isLocked)
                        <p class="mt-1 text-sm text-green-600">
                            Locked: {{ $exportBundle->locked_at->format('Y-m-d H:i') }}
                        </p>
                    @endif

                    @if ($isSubmitted)
                        <p class="mt-1 text-sm text-blue-600">
                            Submitted: {{ $exportBundle->submitted_at->format('Y-m-d H:i') }}
                            @if ($exportBundle->submittedBy)
                                • By: {{ $exportBundle->submittedBy->name }}
                            @endif
                        </p>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-2 justify-end">

                    {{-- Print All ZIP --}}
                    @if ($allReady)
                        <flux:button type="button" variant="ghost"
                            onclick="window.location.href='{{ route('admin.trade.export-bundles.print-all', ['exportBundle' => $exportBundle->id]) }}'">
                            Print All (ZIP)
                        </flux:button>
                    @endif

                    {{-- Lock / Unlock --}}
                    @if (!$isLocked && !$isSubmitted)
                        <flux:button wire:click="lockBundle" variant="primary">
                            Finalize / Lock
                        </flux:button>
                    @else
                        @if (auth()->user()?->hasRole('SUPER_ADMIN') && !$isSubmitted)
                            <flux:button wire:click="unlockBundle" variant="ghost">
                                Unlock
                            </flux:button>
                        @endif
                    @endif

                    {{-- BANK SUBMISSION --}}
                    @if ($isLocked)
                        @if (!$isSubmitted)
                            <div class="flex items-center gap-2">
                                <input type="text"
                                    class="rounded-lg border px-3 py-2 text-sm dark:bg-zinc-800 dark:border-zinc-700"
                                    placeholder="Submission Ref (optional)" wire:model.defer="submission_ref" />

                                <input type="file" class="text-sm" wire:model="bank_ack_file"
                                    accept="application/pdf" />

                                <flux:button wire:click="submitToBank" variant="primary">
                                    Submit to Bank
                                </flux:button>
                            </div>
                        @else
                            @if ($exportBundle->bank_ack_file_path)
                                <flux:button wire:click="downloadBankAck" variant="ghost">
                                    Download Bank Ack
                                </flux:button>
                            @endif

                            @if (auth()->user()?->hasRole('SUPER_ADMIN'))
                                <flux:button wire:click="unsubmitFromBank" variant="ghost">
                                    Unsubmit
                                </flux:button>
                            @endif
                        @endif
                    @endif

                </div>
            </div>

            {{-- LOCK ERRORS --}}
            @if (!empty($lockErrors))
                <div
                    class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-800 dark:border-red-800 dark:bg-red-950 dark:text-red-200">
                    <div class="mb-2 font-semibold">Cannot Lock Bundle. Fix these first:</div>
                    <ul class="list-disc space-y-1 pl-6">
                        @foreach ($lockErrors as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- SUBMIT ERRORS --}}
            @if (!empty($submitErrors))
                <div
                    class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-900 dark:border-amber-800 dark:bg-amber-950 dark:text-amber-200">
                    <div class="mb-2 font-semibold">Cannot Submit to Bank. Fix these first:</div>
                    <ul class="list-disc space-y-1 pl-6">
                        @foreach ($submitErrors as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- DOCUMENT LIST --}}
            @foreach ($requiredKeys as $key)
                @php
                    $row = $docs[$key] ?? null;
                    $canGenerate = !$row || !$row->documentable_id;
                    $disableGenerate = $isLocked || $isSubmitted;
                @endphp

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
                            {{-- Generate --}}
                            @if ($canGenerate && !$disableGenerate)
                                @if ($key === \App\Support\Trade\ExportBundleDocKeys::PACKING_LIST)
                                    <flux:button wire:click="generatePackingList" variant="primary">Generate
                                    </flux:button>
                                @elseif ($key === \App\Support\Trade\ExportBundleDocKeys::NEGOTIATION_LETTER)
                                    <flux:button wire:click="generateNegotiationLetter" variant="primary">Generate
                                    </flux:button>
                                @elseif ($key === \App\Support\Trade\ExportBundleDocKeys::BOE_ONE)
                                    <flux:button wire:click="generateBoeOne" variant="primary">Generate</flux:button>
                                @elseif ($key === \App\Support\Trade\ExportBundleDocKeys::BOE_TWO)
                                    <flux:button wire:click="generateBoeTwo" variant="primary">Generate</flux:button>
                                @endif
                            @endif

                            {{-- Print --}}
                            @if ($row && $row->documentable_id)
                                <flux:button type="button" variant="primary"
                                    wire:click="printDoc('{{ $key }}')">
                                    Print
                                </flux:button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- ✅ STEP 7: BANK PROCESS / TIMELINE --}}
            <div
                class="overflow-hidden rounded-xl bg-white p-4 shadow-sm dark:bg-zinc-900 dark:ring-1 dark:ring-zinc-700">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div>
                        <div class="text-lg font-semibold">Step 7: Bank Process / Timeline</div>
                        <div class="text-sm text-zinc-500">
                            Track courier dispatch and bank acceptance for this bundle.
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 justify-end">
                        <div class="flex items-center gap-2">
                            <input type="text"
                                class="rounded-lg border px-3 py-2 text-sm dark:bg-zinc-800 dark:border-zinc-700"
                                placeholder="Courier Ref (optional)" wire:model.defer="courier_ref"
                                @if (!$isSubmitted) disabled @endif />

                            <flux:button wire:click="markCouriered" variant="primary" :disabled="!$isSubmitted">
                                Mark Couriered
                            </flux:button>
                        </div>

                        <div class="flex items-center gap-2">
                            <input type="text"
                                class="rounded-lg border px-3 py-2 text-sm dark:bg-zinc-800 dark:border-zinc-700"
                                placeholder="Bank Ref / Ack No (optional)" wire:model.defer="bank_ref"
                                @if (!$isSubmitted) disabled @endif />

                            <flux:button wire:click="markBankAccepted" variant="primary" :disabled="!$isSubmitted">
                                Mark Bank Accepted
                            </flux:button>
                        </div>
                    </div>
                </div>

                @if (!$isSubmitted)
                    <div class="mt-3 text-sm text-zinc-500">
                        Submit the bundle to bank first to enable Step 7 actions.
                    </div>
                @endif

                {{-- ✅ Timeline list --}}
                <div class="mt-4">
                    <div class="font-semibold mb-2">Timeline</div>

                    @if ($timeline->isEmpty())
                        <div class="text-sm text-zinc-500">No events yet.</div>
                    @else
                        <div class="space-y-2">
                            @foreach ($timeline as $ev)
                                <div class="rounded-lg border p-3 text-sm dark:border-zinc-700">
                                    <div class="flex items-center justify-between gap-3 flex-wrap">
                                        <div class="font-semibold">
                                            {{ strtoupper(str_replace('_', ' ', (string) $ev->event)) }}
                                        </div>
                                        <div class="text-zinc-500">
                                            {{ optional($ev->event_at)->format('Y-m-d H:i') ?? '—' }}
                                        </div>
                                    </div>

                                    <div class="mt-1 text-zinc-600 dark:text-zinc-400">
                                        @if (!empty($ev->ref))
                                            <span class="font-semibold">Ref:</span> {{ $ev->ref }}
                                        @endif

                                        @if (!empty($ev->notes))
                                            <span class="ml-2 font-semibold">Notes:</span> {{ $ev->notes }}
                                        @endif

                                        @if (!empty($ev->user))
                                            <span class="ml-2">• By: {{ $ev->user->name }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('open-print', (data) => {
                    if (data?.url && data.url !== '#') {
                        window.open(data.url, '_blank');
                    }
                });
            });
        </script>
    @endpush
</x-admin.master-layout>
