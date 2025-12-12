<x-admin.master-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                    Trade Reports
                </h2>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    Buyer Order / Allocation / Summary reports.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <a href="{{ route('admin.reports.buyer-orders.summary.select') }}"
                    class="block rounded-xl bg-white p-4 shadow-sm ring-1 ring-zinc-200 hover:ring-zinc-300 dark:bg-zinc-900 dark:ring-zinc-700 dark:hover:ring-zinc-600">
                    <div class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Buyer Order Summary</div>
                    <div class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                        Summary totals + factory allocation breakdown + PDF.
                    </div>
                </a>

                {{-- You can add more reports cards here later --}}
                {{-- <a href="#" class="...">Shipment Summary</a> --}}
                <a href="{{ route('admin.reports.buyer-orders.factory-allocation.select') }}"
                    class="block rounded-xl bg-white p-4 shadow-sm ring-1 ring-zinc-200 hover:ring-zinc-300 dark:bg-zinc-900 dark:ring-zinc-700 dark:hover:ring-zinc-600">
                    <div class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Factory Allocation Report
                    </div>
                    <div class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                        Print factory allocation PDF by buyer order.
                    </div>
                </a>

            </div>

        </div>
    </div>
</x-admin.master-layout>
