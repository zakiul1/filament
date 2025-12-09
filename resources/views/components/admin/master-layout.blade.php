@props([
    'title' => 'Master Data',
])

<div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8 space-y-6">
    <header class="space-y-1">
        <h1 class="text-2xl font-semibold tracking-tight">
            {{ $title }}
        </h1>

        <p class="text-sm text-zinc-500">
            Manage core master data used across the system.
        </p>
    </header>

    {{-- Filament-style tabs for Master Data --}}
    <x-filament::tabs>
        <x-filament::tabs.item tag="a" :href="route('admin.master.countries.index')" :active="request()->routeIs('admin.master.countries.*')">
            {{ __('Countries') }}
        </x-filament::tabs.item>

        <x-filament::tabs.item tag="a" :href="route('admin.master.currencies.index')" :active="request()->routeIs('admin.master.currencies.*')">
            {{ __('Currencies') }}
        </x-filament::tabs.item>

        <x-filament::tabs.item tag="a" :href="route('admin.master.ports.index')" :active="request()->routeIs('admin.master.ports.*')">
            {{ __('Ports') }}
        </x-filament::tabs.item>

        <x-filament::tabs.item tag="a" :href="route('admin.master.shipment-modes.index')" :active="request()->routeIs('admin.master.shipment-modes.*')">
            {{ __('Shipment Modes') }}
        </x-filament::tabs.item>

        <x-filament::tabs.item tag="a" :href="route('admin.master.incoterms.index')" :active="request()->routeIs('admin.master.incoterms.*')">
            {{ __('Incoterms') }}
        </x-filament::tabs.item>

        <x-filament::tabs.item tag="a" :href="route('admin.master.payment-terms.index')" :active="request()->routeIs('admin.master.payment-terms.*')">
            {{ __('Payment Terms') }}
        </x-filament::tabs.item>

        <x-filament::tabs.item tag="a" :href="route('admin.master.banks.index')" :active="request()->routeIs('admin.master.banks.*')">
            {{ __('Banks') }}
        </x-filament::tabs.item>

        <x-filament::tabs.item tag="a" :href="route('admin.master.bank-branches.index')" :active="request()->routeIs('admin.master.bank-branches.*')">
            {{ __('Bank Branches') }}
        </x-filament::tabs.item>
        <x-filament::tabs.item tag="a" :href="route('admin.master.beneficiary-companies.index')" :active="request()->routeIs('admin.master.beneficiary-companies.*')">
            {{ __('Beneficiary Companies') }}
        </x-filament::tabs.item>

        <x-filament::tabs.item tag="a" :href="route('admin.master.beneficiary-bank-accounts.index')" :active="request()->routeIs('admin.master.beneficiary-bank-accounts.*')">
            {{ __('Beneficiary Bank Accounts') }}
        </x-filament::tabs.item>
        <x-filament::tabs.item tag="a" :href="route('admin.master.couriers.index')" :active="request()->routeIs('admin.master.couriers.*')">
            {{ __('Couriers') }}
        </x-filament::tabs.item>

        <x-filament::tabs.item tag="a" :href="route('admin.master.customers.index')" :active="request()->routeIs('admin.master.customers.*')">
            {{ __('Customers') }}
        </x-filament::tabs.item>

        <x-filament::tabs.item tag="a" :href="route('admin.master.customer-banks.index')" :active="request()->routeIs('admin.master.customer-banks.*')">
            {{ __('Customer Banks') }}
        </x-filament::tabs.item>
        <x-filament::tabs.item tag="a" :href="route('admin.master.factories.index')" :active="request()->routeIs('admin.master.factories.*')">
            {{ __('Factories') }}
        </x-filament::tabs.item>




    </x-filament::tabs>



    <section class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        {{ $slot }}
    </section>
</div>
