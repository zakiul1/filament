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
            @php
                $isClosed = (bool) $exportBundle->closed_at;
            @endphp

            <div
                class="flex items-start justify-between rounded-xl bg-white p-4 shadow-sm dark:bg-zinc-900 dark:ring-1 dark:ring-zinc-700">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-lg font-semibold">Export Bundle: {{ $exportBundle->bundle_no }}</h2>

                        @if ($isClosed)
                            <span
                                class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700 dark:bg-red-950 dark:text-red-200">
                                CLOSED
                            </span>
                        @endif
                    </div>

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

                    @if ($isClosed)
                        <p class="mt-1 text-sm text-red-600">
                            Closed: {{ $exportBundle->closed_at->format('Y-m-d H:i') }}
                            @if ($exportBundle->closedBy)
                                • By: {{ $exportBundle->closedBy->name }}
                            @endif
                        </p>

                        @if (!empty($exportBundle->close_notes))
                            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                                <span class="font-semibold">Notes:</span> {{ $exportBundle->close_notes }}
                            </p>
                        @endif
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-2 justify-end">

                    {{-- Print All ZIP --}}
                    @if ($allReady)
                        @if (!$isClosed)
                            <flux:button type="button" variant="ghost"
                                onclick="window.location.href='{{ route('admin.trade.export-bundles.print-all', ['exportBundle' => $exportBundle->id]) }}'">
                                Print All (ZIP)
                            </flux:button>
                        @else
                            <flux:button type="button" variant="ghost" disabled>
                                Print All (ZIP)
                            </flux:button>
                        @endif
                    @endif

                    {{-- Step 10: Close / Reopen --}}
                    @if (!$isClosed)
                        @if (auth()->user()?->hasRole('ADMIN') || auth()->user()?->hasRole('SUPER_ADMIN'))
                            {{-- Close Notes + Close Button --}}
                            <div class="flex items-center gap-2">
                                <input type="text"
                                    class="rounded-lg border px-3 py-2 text-sm dark:bg-zinc-800 dark:border-zinc-700"
                                    placeholder="Close notes (optional)" wire:model.defer="close_notes"
                                    {{ !$isSubmitted ? 'disabled' : '' }} />

                                @if ($isSubmitted)
                                    <flux:button wire:click="closeBundle" variant="primary" wire:loading.attr="disabled"
                                        wire:target="closeBundle">
                                        <span wire:loading.remove wire:target="closeBundle">Close Bundle</span>
                                        <span wire:loading wire:target="closeBundle">Closing...</span>
                                    </flux:button>
                                @else
                                    <flux:button variant="primary" disabled>
                                        Close Bundle
                                    </flux:button>
                                @endif
                            </div>
                        @endif
                    @else
                        @if (auth()->user()?->hasRole('SUPER_ADMIN'))
                            <flux:button wire:click="reopenBundle" variant="ghost" wire:loading.attr="disabled"
                                wire:target="reopenBundle">
                                <span wire:loading.remove wire:target="reopenBundle">Reopen</span>
                                <span wire:loading wire:target="reopenBundle">Reopening...</span>
                            </flux:button>
                        @endif
                    @endif

                    {{-- Lock / Unlock --}}
                    @if (!$isClosed)
                        @if (!$isLocked && !$isSubmitted)
                            <flux:button wire:click="lockBundle" variant="primary" wire:loading.attr="disabled"
                                wire:target="lockBundle">
                                <span wire:loading.remove wire:target="lockBundle">Finalize / Lock</span>
                                <span wire:loading wire:target="lockBundle">Locking...</span>
                            </flux:button>
                        @else
                            @if (auth()->user()?->hasRole('SUPER_ADMIN') && !$isSubmitted)
                                <flux:button wire:click="unlockBundle" variant="ghost" wire:loading.attr="disabled"
                                    wire:target="unlockBundle">
                                    <span wire:loading.remove wire:target="unlockBundle">Unlock</span>
                                    <span wire:loading wire:target="unlockBundle">Unlocking...</span>
                                </flux:button>
                            @endif
                        @endif
                    @endif

                    {{-- BANK SUBMISSION --}}
                    @if (!$isClosed && $isLocked)
                        @if (!$isSubmitted)
                            <div class="flex items-center gap-2">
                                <input type="text"
                                    class="rounded-lg border px-3 py-2 text-sm dark:bg-zinc-800 dark:border-zinc-700"
                                    placeholder="Submission Ref (optional)" wire:model.defer="submission_ref" />

                                <input type="file" class="text-sm" wire:model="bank_ack_file"
                                    accept="application/pdf" />

                                <flux:button wire:click="submitToBank" variant="primary" wire:loading.attr="disabled"
                                    wire:target="submitToBank,bank_ack_file">
                                    <span wire:loading.remove wire:target="submitToBank,bank_ack_file">Submit to
                                        Bank</span>
                                    <span wire:loading wire:target="submitToBank,bank_ack_file">Submitting...</span>
                                </flux:button>
                            </div>
                        @else
                            @if ($exportBundle->bank_ack_file_path)
                                <flux:button wire:click="downloadBankAck" variant="ghost" wire:loading.attr="disabled"
                                    wire:target="downloadBankAck">
                                    <span wire:loading.remove wire:target="downloadBankAck">Download Bank Ack</span>
                                    <span wire:loading wire:target="downloadBankAck">Preparing...</span>
                                </flux:button>
                            @endif

                            @if (auth()->user()?->hasRole('SUPER_ADMIN'))
                                <flux:button wire:click="unsubmitFromBank" variant="ghost" wire:loading.attr="disabled"
                                    wire:target="unsubmitFromBank">
                                    <span wire:loading.remove wire:target="unsubmitFromBank">Unsubmit</span>
                                    <span wire:loading wire:target="unsubmitFromBank">Reverting...</span>
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
                                    <flux:button wire:click="generatePackingList" variant="primary"
                                        wire:loading.attr="disabled" wire:target="generatePackingList">
                                        <span wire:loading.remove wire:target="generatePackingList">Generate</span>
                                        <span wire:loading wire:target="generatePackingList">Generating...</span>
                                    </flux:button>
                                @elseif ($key === \App\Support\Trade\ExportBundleDocKeys::NEGOTIATION_LETTER)
                                    <flux:button wire:click="generateNegotiationLetter" variant="primary"
                                        wire:loading.attr="disabled" wire:target="generateNegotiationLetter">
                                        <span wire:loading.remove
                                            wire:target="generateNegotiationLetter">Generate</span>
                                        <span wire:loading wire:target="generateNegotiationLetter">Generating...</span>
                                    </flux:button>
                                @elseif ($key === \App\Support\Trade\ExportBundleDocKeys::BOE_ONE)
                                    <flux:button wire:click="generateBoeOne" variant="primary"
                                        wire:loading.attr="disabled" wire:target="generateBoeOne">
                                        <span wire:loading.remove wire:target="generateBoeOne">Generate</span>
                                        <span wire:loading wire:target="generateBoeOne">Generating...</span>
                                    </flux:button>
                                @elseif ($key === \App\Support\Trade\ExportBundleDocKeys::BOE_TWO)
                                    <flux:button wire:click="generateBoeTwo" variant="primary"
                                        wire:loading.attr="disabled" wire:target="generateBoeTwo">
                                        <span wire:loading.remove wire:target="generateBoeTwo">Generate</span>
                                        <span wire:loading wire:target="generateBoeTwo">Generating...</span>
                                    </flux:button>
                                @endif
                            @endif

                            {{-- Print --}}
                            @if ($row && $row->documentable_id)
                                <flux:button type="button" variant="primary"
                                    wire:click="printDoc('{{ $key }}')" wire:loading.attr="disabled"
                                    wire:target="printDoc">
                                    <span wire:loading.remove wire:target="printDoc">Print</span>
                                    <span wire:loading wire:target="printDoc">Opening...</span>
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
                        {{-- Courier --}}
                        <div class="flex items-center gap-2">
                            <input type="text"
                                class="rounded-lg border px-3 py-2 text-sm dark:bg-zinc-800 dark:border-zinc-700"
                                placeholder="Courier Ref (optional)" wire:model.defer="courier_ref"
                                @if (!$isSubmitted) disabled @endif />

                            @if ($isSubmitted)
                                <flux:button wire:click="markCouriered" variant="primary"
                                    wire:loading.attr="disabled" wire:target="markCouriered">
                                    <span wire:loading.remove wire:target="markCouriered">Mark Couriered</span>
                                    <span wire:loading wire:target="markCouriered">Saving...</span>
                                </flux:button>
                            @else
                                <flux:button variant="primary" disabled>
                                    Mark Couriered
                                </flux:button>
                            @endif
                        </div>

                        {{-- Bank Accepted --}}
                        <div class="flex items-center gap-2">
                            <input type="text"
                                class="rounded-lg border px-3 py-2 text-sm dark:bg-zinc-800 dark:border-zinc-700"
                                placeholder="Bank Ref / Ack No (optional)" wire:model.defer="bank_ref"
                                @if (!$isSubmitted) disabled @endif />

                            @if ($isSubmitted)
                                <flux:button wire:click="markBankAccepted" variant="primary"
                                    wire:loading.attr="disabled" wire:target="markBankAccepted">
                                    <span wire:loading.remove wire:target="markBankAccepted">Mark Bank Accepted</span>
                                    <span wire:loading wire:target="markBankAccepted">Saving...</span>
                                </flux:button>
                            @else
                                <flux:button variant="primary" disabled>
                                    Mark Bank Accepted
                                </flux:button>
                            @endif
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
