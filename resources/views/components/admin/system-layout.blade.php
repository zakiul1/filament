@props([
    'title' => 'System Foundation',
])

<div class="  space-y-6">
    {{-- Page header --}}
    <header class="space-y-1">
        <h1 class="text-2xl font-semibold tracking-tight">
            {{ $title }}
        </h1>

        <p class="text-sm text-zinc-500">
            Manage your system foundation settings.
        </p>
    </header>

    {{-- Filament-style tabs --}}
    <x-filament::tabs>
        <x-filament::tabs.item tag="a" :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
            {{ __('Users') }}
        </x-filament::tabs.item>

        <x-filament::tabs.item tag="a" :href="route('admin.company.profile')" :active="request()->routeIs('admin.company.profile')">
            {{ __('Company Settings') }}
        </x-filament::tabs.item>

        <x-filament::tabs.item tag="a" :href="route('admin.signatories.index')" :active="request()->routeIs('admin.signatories.*')">
            {{ __('Signatories') }}
        </x-filament::tabs.item>

        <x-filament::tabs.item tag="a" :href="route('admin.document-sign-settings.index')" :active="request()->routeIs('admin.document-sign-settings.*')">
            {{ __('Document Sign Settings') }}
        </x-filament::tabs.item>
    </x-filament::tabs>

    {{-- Card with full-width content under the tabs --}}
    <section class="rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">

        {{ $slot }}
    </section>
</div>
