<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
            <x-app-logo />
        </a>

        <flux:navlist variant="outline">

            {{-- PLATFORM --}}
            <flux:navlist.group :heading="__('Platform')" class="grid">
                <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    wire:navigate>
                    {{ __('Dashboard') }}
                </flux:navlist.item>
            </flux:navlist.group>

            {{-- ADMINISTRATION (single entry, inner tabs handle the rest) --}}
            <flux:navlist.group :heading="__('Administration')" class="mt-4 grid">
                <flux:navlist.item icon="cog-6-tooth" :href="route('admin.users.index')" {{-- default tab = Users --}}
                    :current="request()->routeIs('admin.*')" wire:navigate>
                    {{ __('System Foundation') }}
                </flux:navlist.item>
            </flux:navlist.group>

            {{-- MASTER DATA --}}
            <flux:navlist.group :heading="__('Master Data')" class="mt-4 grid">
                <flux:navlist.item icon="globe-alt" :href="route('admin.master.countries.index')"
                    :current="request()->routeIs('admin.master.countries.*')" wire:navigate>
                    {{ __('Countries') }}
                </flux:navlist.item>

                <flux:navlist.item icon="banknotes" :href="route('admin.master.currencies.index')"
                    :current="request()->routeIs('admin.master.currencies.*')" wire:navigate>
                    {{ __('Currencies') }}
                </flux:navlist.item>

                <flux:navlist.item icon="academic-cap" :href="route('admin.master.ports.index')"
                    :current="request()->routeIs('admin.master.ports.*')" wire:navigate>
                    {{ __('Ports') }}
                </flux:navlist.item>

                <flux:navlist.item icon="truck" :href="route('admin.master.shipment-modes.index')"
                    :current="request()->routeIs('admin.master.shipment-modes.*')" wire:navigate>
                    {{ __('Shipment Modes') }}
                </flux:navlist.item>

                <flux:navlist.item icon="scale" :href="route('admin.master.incoterms.index')"
                    :current="request()->routeIs('admin.master.incoterms.*')" wire:navigate>
                    {{ __('Incoterms') }}
                </flux:navlist.item>

                <flux:navlist.item icon="receipt-percent" :href="route('admin.master.payment-terms.index')"
                    :current="request()->routeIs('admin.master.payment-terms.*')" wire:navigate>
                    {{ __('Payment Terms') }}
                </flux:navlist.item>

                <flux:navlist.item icon="building-library" :href="route('admin.master.banks.index')"
                    :current="request()->routeIs('admin.master.banks.*')" wire:navigate>
                    {{ __('Banks') }}
                </flux:navlist.item>

                <flux:navlist.item icon="building-office" :href="route('admin.master.bank-branches.index')"
                    :current="request()->routeIs('admin.master.bank-branches.*')" wire:navigate>
                    {{ __('Bank Branches') }}
                </flux:navlist.item>

                <flux:navlist.item icon="building-storefront" :href="route('admin.master.beneficiary-companies.index')"
                    :current="request()->routeIs('admin.master.beneficiary-companies.*')" wire:navigate>
                    {{ __('Beneficiary Companies') }}
                </flux:navlist.item>

                <flux:navlist.item icon="credit-card" :href="route('admin.master.beneficiary-bank-accounts.index')"
                    :current="request()->routeIs('admin.master.beneficiary-bank-accounts.*')" wire:navigate>
                    {{ __('Beneficiary Bank Accounts') }}
                </flux:navlist.item>

                <flux:navlist.item icon="paper-airplane" :href="route('admin.master.couriers.index')"
                    :current="request()->routeIs('admin.master.couriers.*')" wire:navigate>
                    {{ __('Couriers') }}
                </flux:navlist.item>

                <flux:navlist.item icon="user-group" :href="route('admin.master.customers.index')"
                    :current="request()->routeIs('admin.master.customers.*')" wire:navigate>
                    {{ __('Customers') }}
                </flux:navlist.item>

                <flux:navlist.item icon="building-office" :href="route('admin.master.customer-banks.index')"
                    :current="request()->routeIs('admin.master.customer-banks.*')" wire:navigate>
                    {{ __('Customer Banks') }}
                </flux:navlist.item>

                {{-- FACTORIES --}}
                <flux:navlist.item icon="building-office-2" :href="route('admin.master.factories.index')"
                    :current="request()->routeIs('admin.master.factories.*')" wire:navigate>
                    {{ __('Factories') }}
                </flux:navlist.item>

                <flux:navlist.item icon="bars-3" :href="route('admin.master.factory-categories.index')"
                    :current="request()->routeIs('admin.master.factory-categories.*')" wire:navigate>
                    {{ __('Factory Categories') }}
                </flux:navlist.item>

                <flux:navlist.item icon="list-bullet" :href="route('admin.master.factory-subcategories.index')"
                    :current="request()->routeIs('admin.master.factory-subcategories.*')" wire:navigate>
                    {{ __('Factory Subcategories') }}
                </flux:navlist.item>

                <flux:navlist.item icon="document-duplicate" :href="route('admin.master.factory-certificates.index')"
                    :current="request()->routeIs('admin.master.factory-certificates.*')" wire:navigate>
                    {{ __('Factory Certificates') }}
                </flux:navlist.item>
            </flux:navlist.group>

            <flux:navlist.group :heading="__('Commercial')" class="mt-4 grid">
                <flux:navlist.item icon="document-text" :href="route('admin.trade.proforma-invoices.index')"
                    :current="request()->routeIs('admin.trade.proforma-invoices.*')" wire:navigate>
                    {{ __('Proforma Invoices') }}
                </flux:navlist.item>
            </flux:navlist.group>


        </flux:navlist>


        <flux:spacer />

        <!-- Desktop User Menu -->
        <flux:dropdown class="hidden lg:block" position="bottom" align="start">
            <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()"
                icon:trailing="chevrons-up-down" />

            <flux:menu class="w-[220px]">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">
                                    {{ auth()->user()->name }}
                                </span>
                                <span class="truncate text-xs">
                                    {{ auth()->user()->email }}
                                </span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    <!-- MOBILE USER MENU -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">
                                    {{ auth()->user()->name }}
                                </span>
                                <span class="truncate text-xs">
                                    {{ auth()->user()->email }}
                                </span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    {{-- Required Scripts --}}
    @fluxScripts
    @livewire('notifications')
    @filamentScripts

</body>

</html>
