@extends('layouts.main')
@section('content')
    <!-- CONTENT HEADER -->
    <div class="px-6 pt-6 pb-4">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">{{ __('Server Settings') }}</h1>
                </div>
                <nav class="flex gap-2 mt-3 text-sm text-gray-600 dark:text-gray-400">
                    <a href="{{ route('home') }}" class="hover:text-accent-500 transition-colors">{{ __('Dashboard') }}</a>
                    <span class="text-gray-400">/</span>
                    <a href="{{ route('servers.index') }}"
                        class="hover:text-accent-500 transition-colors">{{ __('Servers') }}</a>
                    <span class="text-gray-400">/</span>
                    <span class="text-gray-900 dark:text-white font-semibold">{{ $server->name }}</span>
                </nav>
            </div>
        </div>
    </div>
    <!-- END CONTENT HEADER -->

    <!-- MAIN CONTENT -->
    <section class="px-6 pb-6" x-data="{
        showUpgradeModal: false,
        showBillingPriorityModal: false
    }">
        <div class="max-w-7xl mx-auto space-y-6">
            <!-- STAT CARDS -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                <!-- Server Name -->
                <div
                    class="bg-gradient-to-br from-gray-100 to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-xl p-6 border border-gray-200 dark:border-gray-700/50 hover:border-accent-500/50 transition-all duration-300 shadow-sm hover:shadow-md group">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-widest">
                                {{ __('SERVER NAME') }}</p>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-2" id="domain_text">
                                {{ $server->name }}
                            </h3>
                        </div>
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-accent-500/30 to-accent-600/30 rounded-lg flex items-center justify-center shadow-lg group-hover:shadow-xl transition-shadow duration-300 flex-shrink-0 ml-4">
                            <i class="fas fa-fingerprint text-accent-600 dark:text-accent-400 text-lg"></i>
                        </div>
                    </div>
                </div>

                <!-- CPU -->
                <div
                    class="bg-gradient-to-br from-gray-100 to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-xl p-6 border border-gray-200 dark:border-gray-700/50 hover:border-red-500/50 transition-all duration-300 shadow-sm hover:shadow-md group">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-widest">
                                {{ __('CPU') }}</p>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                                @if ($server->product->cpu == 0)
                                    {{ __('Unlimited') }}
                                @else
                                    {{ $server->product->cpu / 100 }} {{ __('vCores') }}
                                @endif
                            </h3>
                        </div>
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-red-500/30 to-red-600/30 rounded-lg flex items-center justify-center shadow-lg group-hover:shadow-xl transition-shadow duration-300 flex-shrink-0 ml-4">
                            <i class="fas fa-microchip text-red-600 dark:text-red-400 text-lg"></i>
                        </div>
                    </div>
                </div>

                <!-- Memory -->
                <div
                    class="bg-gradient-to-br from-gray-100 to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-xl p-6 border border-gray-200 dark:border-gray-700/50 hover:border-green-500/50 transition-all duration-300 shadow-sm hover:shadow-md group">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-widest">
                                {{ __('MEMORY') }}</p>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                                @if ($server->product->memory == 0)
                                    {{ __('Unlimited') }}
                                @else
                                    {{ $server->product->memory }}MB
                                @endif
                            </h3>
                        </div>
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-green-500/30 to-green-600/30 rounded-lg flex items-center justify-center shadow-lg group-hover:shadow-xl transition-shadow duration-300 flex-shrink-0 ml-4">
                            <i class="fas fa-memory text-green-600 dark:text-green-400 text-lg"></i>
                        </div>
                    </div>
                </div>

                <!-- Storage -->
                <div
                    class="bg-gradient-to-br from-gray-100 to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-xl p-6 border border-gray-200 dark:border-gray-700/50 hover:border-yellow-500/50 transition-all duration-300 shadow-sm hover:shadow-md group">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-widest">
                                {{ __('STORAGE') }}</p>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                                @if ($server->product->disk == 0)
                                    {{ __('Unlimited') }}
                                @else
                                    {{ $server->product->disk }}MB
                                @endif
                            </h3>
                        </div>
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-yellow-500/30 to-yellow-600/30 rounded-lg flex items-center justify-center shadow-lg group-hover:shadow-xl transition-shadow duration-300 flex-shrink-0 ml-4">
                            <i class="fas fa-hard-drive text-yellow-600 dark:text-yellow-400 text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MAIN SECTIONS -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Server Information -->
                <div
                    class="lg:col-span-2 bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700/50 overflow-hidden shadow-sm">
                    <div
                        class="px-6 py-4 border-b border-gray-200 dark:border-gray-700/50 bg-gradient-to-r from-transparent to-transparent hover:from-gray-50 dark:hover:from-gray-800/30 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-accent-500/20 flex items-center justify-center">
                                <i class="fas fa-sliders text-accent-600 dark:text-accent-400"></i>
                            </div>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white flex-1">
                                {{ __('Server Information') }}</h2>
                            <div class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                <i class="fas fa-calendar-alt" data-tippy-content="{{ __('Created at') }}"></i>
                                {{ $server->created_at->isoFormat('LL') }}
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-6 space-y-3">
                        <div class="grid grid-cols-1 gap-y-4 md:grid-cols-2 md:gap-6">
                            <!-- Server ID -->
                            <div
                                class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700/30 md:border-b-0 md:pb-0">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                    <i class="fas fa-hashtag text-gray-400 w-4"></i>
                                    {{ __('Server ID') }}
                                </span>
                                <span
                                    class="text-sm text-gray-900 dark:text-white font-mono bg-gray-100 dark:bg-gray-700/50 px-3 py-1 rounded truncate max-w-48">{{ $server->id }}</span>
                            </div>

                            <!-- Pterodactyl ID -->
                            <div
                                class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700/30 md:border-b-0 md:pb-0">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                    <i class="fas fa-database text-gray-400 w-4"></i>
                                    {{ __('Pterodactyl ID') }}
                                </span>
                                <span
                                    class="text-sm text-gray-900 dark:text-white font-mono bg-gray-100 dark:bg-gray-700/50 px-3 py-1 rounded truncate max-w-48">{{ $server->identifier }}</span>
                            </div>

                            <!-- Hourly Price -->
                            <div
                                class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700/30 md:border-b-0 md:pb-0">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                    <i class="fas fa-hourglass-half text-gray-400 w-4"></i>
                                    {{ __('Hourly Price') }}
                                </span>
                                <span
                                    class="text-sm text-accent-600 dark:text-accent-400 font-semibold">{{ Currency::formatForDisplay($server->product->getHourlyPrice()) }}</span>
                            </div>

                            <!-- Monthly Price -->
                            <div
                                class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700/30 md:border-b-0 md:pb-0">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                    <i class="fas fa-calendar text-gray-400 w-4"></i>
                                    {{ __('Monthly Price') }}
                                </span>
                                <span
                                    class="text-sm text-accent-600 dark:text-accent-400 font-semibold">{{ Currency::formatForDisplay($server->product->getMonthlyPrice()) }}</span>
                            </div>

                            <!-- Location -->
                            <div
                                class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700/30 md:border-b-0 md:pb-0">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                    <i class="fas fa-map-pin text-gray-400 w-4"></i>
                                    {{ __('Location') }}
                                </span>
                                <span
                                    class="text-sm text-gray-900 dark:text-white font-medium truncate max-w-48">{{ $serverAttributes['relationships']['location']['attributes']['short'] }}</span>
                            </div>

                            <!-- Node -->
                            <div
                                class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700/30 md:border-b-0 md:pb-0">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                    <i class="fas fa-server text-gray-400 w-4"></i>
                                    {{ __('Node') }}
                                </span>
                                <span
                                    class="text-sm text-gray-900 dark:text-white font-medium truncate max-w-48">{{ $serverAttributes['relationships']['node']['attributes']['name'] }}</span>
                            </div>

                            <!-- Backups -->
                            <div
                                class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700/30 md:border-b-0 md:pb-0">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                    <i class="fas fa-copy text-gray-400 w-4"></i>
                                    {{ __('Backups') }}
                                </span>
                                <span
                                    class="text-sm text-gray-900 dark:text-white font-semibold">{{ $server->product->backups }}</span>
                            </div>

                            <!-- OOM Killer -->
                            <div
                                class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700/30 md:border-b-0 md:pb-0">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                    <i class="fas fa-shield-alt text-gray-400 w-4"></i>
                                    {{ __('OOM Killer') }}
                                </span>
                                <span
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $server->product->oom_killer ? 'bg-green-500/15 text-green-700 dark:text-green-400' : 'bg-red-500/15 text-red-700 dark:text-red-400' }}">
                                    <i
                                        class="fas {{ $server->product->oom_killer ? 'fa-check-circle' : 'fa-times-circle' }} text-xs"></i>
                                    {{ $server->product->oom_killer ? __('Enabled') : __('Disabled') }}
                                </span>
                            </div>

                            <!-- MySQL Database -->
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                    <i class="fas fa-database text-gray-400 w-4"></i>
                                    {{ __('MySQL Databases') }}
                                </span>
                                <span
                                    class="text-sm text-gray-900 dark:text-white font-semibold">{{ $server->product->databases }}</span>
                            </div>
                        </div>
                    </div>

                    <div
                        class="px-6 py-4 border-t border-gray-200 dark:border-gray-700/50 bg-gray-50 dark:bg-gray-900/30 flex justify-center gap-3">
                        @if ($server_enable_upgrade && Auth::user()->can('user.server.upgrade'))
                            <x-button variant="outline" @click="showUpgradeModal = true" class="gap-2">
                                <i class="fas fa-arrow-up text-sm"></i>
                                {{ __('Upgrade / Downgrade') }}
                            </x-button>
                        @endif
                        <x-button variant="danger" onclick="handleServerDelete()" class="gap-2">
                            <i class="fas fa-trash text-sm"></i>
                            {{ __('Delete Server') }}
                        </x-button>
                    </div>
                </div>

                <!-- Billing Priority Card -->
                <div
                    class="bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700/50 overflow-hidden shadow-sm flex flex-col">
                    <div
                        class="px-6 py-4 border-b border-gray-200 dark:border-gray-700/50 bg-gradient-to-r from-transparent to-transparent hover:from-gray-50 dark:hover:from-gray-800/30 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-accent-500/20 flex items-center justify-center">
                                <i class="fas fa-flag text-accent-600 dark:text-accent-400"></i>
                            </div>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Billing Priority') }}</h2>
                        </div>
                    </div>

                    <div class="px-6 py-6 flex-1">
                        <div class="space-y-4">
                            <div>
                                <p
                                    class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-2">
                                    {{ __('Current Priority') }}</p>
                                <div
                                    class="flex items-center gap-3 p-4 bg-accent-50 dark:bg-accent-900/20 rounded-lg border border-accent-200 dark:border-accent-800/30">
                                    <i class="fas fa-star text-accent-600 dark:text-accent-400 text-lg"></i>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">
                                            {{ $server->effective_billing_priority->label() }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $server->effective_billing_priority->description() }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700/50 bg-gray-50 dark:bg-gray-900/30">
                        <x-button variant="outline" @click="showBillingPriorityModal = true"
                            class="w-full gap-2 justify-center">
                            <i class="fas fa-edit text-sm"></i>
                            {{ __('Change Priority') }}
                        </x-button>
                    </div>
                </div>
            </div>
        </div>

        @if ($server_enable_upgrade && Auth::user()->can('user.server.upgrade'))
            <!-- Upgrade Modal -->
            <div x-cloak x-show="showUpgradeModal" x-transition.opacity.duration-200
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
                @click="showUpgradeModal = false" @keydown.escape.window="showUpgradeModal = false">
                <div class="w-full max-w-2xl mx-auto" @click.stop>
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl overflow-hidden">
                        <div
                            class="px-6 py-4 border-b border-gray-200 dark:border-gray-700/50 flex items-center justify-between bg-gradient-to-r from-gray-50 to-transparent dark:from-gray-800/50 dark:to-transparent">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-accent-500/20 flex items-center justify-center">
                                    <i class="fas fa-arrow-up text-accent-600 dark:text-accent-400"></i>
                                </div>
                                <h5 class="text-lg font-bold text-gray-900 dark:text-white">
                                    {{ __('Upgrade/Downgrade Server') }}</h5>
                            </div>
                            <button type="button"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                                @click="showUpgradeModal = false">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="px-6 py-6 space-y-4">
                            <div
                                class="p-4 bg-accent-50 dark:bg-accent-800/20 rounded-lg border border-accent-200 dark:border-accent-800/30">
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    <strong class="text-accent-700 dark:text-accent-400">{{ __('Current Product') }}:</strong>
                                    <span class="ml-1">{{ $server->product->name }}</span>
                                </p>
                            </div>
                            <form action="{{ route('servers.upgrade', ['server' => $server->id]) }}" method="POST"
                                class="space-y-4 upgrade-form">
                                @csrf
                                <div>
                                    <label for="product_upgrade"
                                        class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                        {{ __('Select New Product') }}
                                    </label>
                                    <select
                                        x-on:change="$el.value ? $refs.upgradeSubmit.disabled = false : $refs.upgradeSubmit.disabled = true"
                                        name="product_upgrade" id="product_upgrade"
                                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-accent-500 focus:border-transparent">
                                        <option value="">{{ __('Select the product') }}</option>
                                        @foreach ($products as $product)
                                            @if ($product->id != $server->product->id && $product->disabled == false)
                                                <option value="{{ $product->id }}"
                                                    @if ($product->doesNotFit) disabled @endif>
                                                    {{ $product->name }} [ {{ $credits_display_name }}
                                                    {{ $product->display_price }}
                                                    @if ($product->doesNotFit)
                                                        ] {{ __('Server can\'t fit on this node') }}
                                                    @else
                                                        @if ($product->minimum_credits != null)
                                                            / {{ __('Required') }}:
                                                            {{ $product->display_minimum_credits }}
                                                            {{ $credits_display_name }}
                                                        @endif ]
                                                    @endif
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div
                                    class="space-y-3 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/30 rounded-lg">
                                    <p class="text-sm text-amber-900 dark:text-amber-200 flex items-start gap-2">
                                        <i class="fas fa-exclamation-triangle flex-shrink-0 mt-0.5"></i>
                                        <span><strong>{{ __('Important') }}:</strong>
                                            {{ __('Upgrading/Downgrading will reset your billing cycle. Overpaid credits will be refunded.') }}</span>
                                    </p>
                                    <p class="text-sm text-amber-900 dark:text-amber-200 flex items-start gap-2">
                                        <i class="fas fa-info-circle flex-shrink-0 mt-0.5"></i>
                                        <span>{{ __('Your server will be automatically restarted once upgraded.') }}</span>
                                    </p>
                                </div>

                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <div class="flex justify-end gap-3 border-t border-gray-200 dark:border-gray-700/50 pt-4">
                                    <button type="button" @click="showUpgradeModal = false"
                                        class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-medium">
                                        {{ __('Cancel') }}
                                    </button>
                                    <x-button x-ref="upgradeSubmit" type="submit" class="upgrade-once" disabled>
                                        {{ __('Change Product') }}
                                    </x-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Billing Priority Modal -->
        <div x-cloak x-show="showBillingPriorityModal" x-transition.opacity.duration-200
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
            @click="showBillingPriorityModal = false" @keydown.escape.window="showBillingPriorityModal = false">
            <div class="w-full max-w-md mx-auto" @click.stop>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl overflow-hidden">
                    <div
                        class="px-6 py-4 border-b border-gray-200 dark:border-gray-700/50 flex items-center justify-between bg-gradient-to-r from-accent-50 to-transparent dark:from-accent-800/20 dark:to-transparent">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-accent-500/20 flex items-center justify-center">
                                <i class="fas fa-star text-accent-600 dark:text-accent-400"></i>
                            </div>
                            <h5 class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ __('Update Billing Priority') }}</h5>
                        </div>
                        <button type="button"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                            @click="showBillingPriorityModal = false">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="px-6 py-6">
                        <form action="{{ route('servers.updateBillingPriority', ['server' => $server->id]) }}"
                            method="POST" id="billing_priority_form" class="space-y-4">
                            @csrf
                            @method('PATCH')
                            <div>
                                <label for="billing_priority"
                                    class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                                    {{ __('Select Priority Level') }}
                                </label>
                                <select
                                    x-on:change="$el.value ? $refs.prioritySubmit.disabled = false : $refs.prioritySubmit.disabled = true"
                                    name="billing_priority" id="billing_priority"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-accent-500 focus:border-transparent">
                                    <option value="">{{ __('Select the billing priority') }}</option>
                                    @foreach (App\Enums\BillingPriority::cases() as $priority)
                                        <option value="{{ $priority->value }}" @selected($server->effective_billing_priority == $priority)>
                                            {{ $priority->label() }} - {{ $priority->description() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex justify-end gap-3 border-t border-gray-200 dark:border-gray-700/50 pt-4">
                                <button type="button" @click="showBillingPriorityModal = false"
                                    class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-medium">
                                    {{ __('Cancel') }}
                                </button>
                                <x-button x-ref="prioritySubmit" type="submit" class="billing_priority_submit" disabled>
                                    {{ __('Update') }}
                                </x-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        function handleServerDelete() {
            Swal.fire({
                title: "{{ __('Delete Server?') }}",
                html: "{!! __(
                    'This is an irreversible action. All files and data will be <strong>permanently removed</strong>. <strong>No funds will be refunded</strong>.',
                ) !!}",
                icon: 'warning',
                confirmButtonColor: '#dc2626',
                showCancelButton: true,
                confirmButtonText: "{{ __('Yes, delete it!') }}",
                cancelButtonText: "{{ __('No, abort!') }}",
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    fetch("{{ route('servers.destroy', ['server' => $server->id]) }}", {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(() => {
                        window.location.href = "{{ route('servers.index') }}";
                    }).catch((error) => {
                        Swal.fire({
                            title: "{{ __('Error') }}",
                            text: "{{ __('Something went wrong, please try again later.') }}",
                            icon: 'error',
                            confirmButtonColor: '#dc2626',
                        })
                    })
                    return
                }
            })
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.upgrade-form').forEach((form) => {
                form.addEventListener('submit', () => {
                    form.querySelectorAll('.upgrade-once').forEach((button) => {
                        button.setAttribute('disabled', 'disabled');
                    });
                });
            });

            const billingForm = document.getElementById('billing_priority_form');
            if (billingForm) {
                billingForm.addEventListener('submit', () => {
                    billingForm.querySelectorAll('.billing_priority_submit').forEach((button) => {
                        button.setAttribute('disabled', 'disabled');
                    });
                });
            }

            if (window.tippy) {
                tippy('[data-tippy-content]', {
                    allowHTML: true,
                    interactive: true,
                    animation: 'shift-away',
                    theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
                });
            }
        });
    </script>
@endsection
