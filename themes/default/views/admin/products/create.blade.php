@extends('layouts.main')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-y-4 sm:flex-row sm:items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('Products') }}</h1>
        <ol class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
            <li>
                <a href="{{ route('home') }}" class="hover:text-accent-600 dark:hover:text-accent-400">{{ __('Dashboard') }}</a>
            </li>
            <li>/</li>
            <li>
                <a href="{{ route('admin.products.index') }}" class="hover:text-accent-500 dark:hover:text-accent-400">{{ __('Products') }}</a>
            </li>
            <li>/</li>
            <li>
                <span class="text-gray-700 dark:text-gray-300">{{ __('Create') }}</span>
            </li>
        </ol>
    </div>
    <section class="content">
        <div class="w-full">
            <form action="{{ route('admin.products.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div class="col-span-1">
                        <div class="bg-white rounded-lg shadow-md overflow-hidden dark:bg-gray-800">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h5 class="text-lg font-medium text-gray-800 dark:text-white">{{ __('Product Details') }}</h5>
                            </div>
                            <div class="p-6">
                                
                                <div class="mb-4 flex flex-row-reverse">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="disabled" id="switch1" class="h-4 w-4 rounded border-gray-300 text-accent-600 focus:ring-accent-500 dark:bg-gray-700 dark:border-gray-600">
                                        <label class="ml-2 block text-sm text-gray-900 cursor-pointer dark:text-gray-300" for="switch1">
                                            {{ __('Disabled') }}
                                            <i class="fas fa-info-circle ml-1 text-gray-400 cursor-help dark:text-gray-500"
                                               data-toggle="popover" data-trigger="hover"
                                               data-content="{{ __('Will hide this option from being selected') }}"></i>
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{ __('Name') }}</label>
                                    <input value="{{ $product->name ?? old('name') }}" id="name"
                                           name="name" type="text"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('name') border-red-500 @enderror"
                                           required="required">
                                    @error('name')
                                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">
                                        {{ __('Description') }}
                                        <i class="fas fa-info-circle ml-1 text-gray-400 cursor-help dark:text-gray-500" data-toggle="popover" data-trigger="hover" data-content="{{ __('This is what the users sees') }}"></i>
                                    </label>
                                    <textarea id="description" name="description" type="text"
                                              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('description') border-red-500 @enderror"
                                              required="required">{{ $product->description ?? old('description') }}</textarea>
                                    @error('description')
                                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="billing_period" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">
                                            {{ __('Billing Period') }}
                                            <i class="fas fa-info-circle ml-1 text-gray-400 cursor-help dark:text-gray-500" data-toggle="popover" data-trigger="hover" data-content="{{ __('Period when the user will be charged for the given price') }}"></i>
                                        </label>
                                        <select id="billing_period" class="custom-select block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('billing_period') border-red-500 @enderror"
                                                name="billing_period" required autocomplete="off">
                                            <option value="hourly" @selected(old('billing_period', $product->billing_period ?? '') == 'hourly')>{{ __('Hourly') }}</option>
                                            <option value="daily" @selected(old('billing_period', $product->billing_period ?? '') == 'daily')>{{ __('Daily') }}</option>
                                            <option value="weekly" @selected(old('billing_period', $product->billing_period ?? '') == 'weekly')>{{ __('Weekly') }}</option>
                                            <option value="monthly" @selected(old('billing_period', $product->billing_period ?? '') == 'monthly')>{{ __('Monthly') }}</option>
                                            <option value="quarterly" @selected(old('billing_period', $product->billing_period ?? '') == 'quarterly')>{{ __('Quarterly') }}</option>
                                            <option value="half-annually" @selected(old('billing_period', $product->billing_period ?? '') == 'half-annually')>{{ __('Half Annually') }}</option>
                                            <option value="annually" @selected(old('billing_period', $product->billing_period ?? '') == 'annually')>{{ __('Annually') }}</option>
                                        </select>
                                        @error('billing_period')
                                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="default_billing_priority" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">
                                            {{ __('Default Billing Priority') }}
                                            <i class="fas fa-info-circle ml-1 text-gray-400 cursor-help dark:text-gray-500" data-toggle="popover" data-trigger="hover" data-content="{{ __('Defines the priority at which the servers in this product will be charged.') }}"></i>
                                        </label>
                                        <select id="default_billing_priority" class="custom-select block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('default_billing_priority') border-red-500 @enderror"
                                                name="default_billing_priority" required autocomplete="off">
                                            @foreach(App\Enums\BillingPriority::options() as $value => $label)
                                                <option value="{{ $value }}" @selected(old('default_billing_priority', $product->default_billing_priority->value ?? '') == $value || $value == App\Enums\BillingPriority::MEDIUM->value)>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('default_billing_priority')
                                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="price" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{ __('Price in') }} {{ $credits_display_name }}</label>
                                        <input value="{{ old('price', isset($product) ? Currency::formatForForm($product->price) : '') }}" id="price"
                                               name="price" step=".0001" type="number"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('price') border-red-500 @enderror"
                                               required="required">
                                        @error('price')
                                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="minimum_credits" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">
                                            {{ __('Minimum') }} {{ $credits_display_name }}
                                            <i class="fas fa-info-circle ml-1 text-gray-400 cursor-help dark:text-gray-500" data-toggle="popover" data-trigger="hover" data-content="{{ __('Setting to empty will use the value from configuration.') }}"></i>
                                        </label>
                                        <input value="{{ old('minimum_credits', (isset($product) && $product->minimum_credits) ? Currency::formatForForm($product->minimum_credits) : null) }}"
                                               id="minimum_credits" name="minimum_credits" type="number"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('minimum_credits') border-red-500 @enderror">
                                        @error('minimum_credits')
                                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="cpu" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{ __('Cpu') }}</label>
                                        <input value="{{ $product->cpu ?? old('cpu') }}" id="cpu" name="cpu"
                                               type="number" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('cpu') border-red-500 @enderror"
                                               required="required">
                                        @error('cpu')
                                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="disk" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{ __('Disk') }}</label>
                                        <input value="{{ $product->disk ?? (old('disk') ?? 1000) }}" id="disk"
                                               name="disk" type="number"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('disk') border-red-500 @enderror"
                                               required="required">
                                        @error('disk')
                                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="memory" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{ __('Memory') }}</label>
                                        <input value="{{ $product->memory ?? old('memory') }}" id="memory"
                                               name="memory" type="number"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('memory') border-red-500 @enderror"
                                               required="required">
                                        @error('memory')
                                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="io" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{ __('IO') }}</label>
                                        <input value="{{ $product->io ?? (old('io') ?? 500) }}" id="io"
                                               name="io" type="number"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('io') border-red-500 @enderror"
                                               required="required">
                                        @error('io')
                                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="swap" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{ __('Swap') }}</label>
                                        <input value="{{ $product->swap ?? old('swap') }}" id="swap"
                                               name="swap" type="number"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('swap') border-red-500 @enderror"
                                               required="required">
                                        @error('swap')
                                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="serverlimit" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">
                                            {{ __('Serverlimit') }}
                                            <i class="fas fa-info-circle ml-1 text-gray-400 cursor-help dark:text-gray-500" data-toggle="popover" data-trigger="hover" data-content="{{ __('The maximum amount of Servers that can be created with this Product per User') }}"></i>
                                        </label>
                                        <input value="{{ $product->serverlimit ?? (old('serverlimit') ?? 0) }}"
                                               id="serverlimit" name="serverlimit" type="number"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('serverlimit') border-red-500 @enderror"
                                               required="required">
                                        @error('serverlimit')
                                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <label for="allocations" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{ __('Allocations') }}</label>
                                        <input value="{{ $product->allocations ?? (old('allocations') ?? 0) }}"
                                               id="allocations" name="allocations" type="number"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('allocations') border-red-500 @enderror"
                                               required="required">
                                        @error('allocations')
                                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="databases" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{ __('Databases') }}</label>
                                        <input value="{{ $product->databases ?? (old('databases') ?? 1) }}"
                                               id="databases" name="databases" type="number"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('databases') border-red-500 @enderror"
                                               required="required">
                                        @error('databases')
                                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="backups" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{ __('Backups') }}</label>
                                        <input value="{{ $product->backups ?? (old('backups') ?? 1) }}"
                                               id="backups" name="backups" type="number"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('backups') border-red-500 @enderror"
                                               required="required">
                                        @error('backups')
                                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" value="1" id="oom_killer" name="oom_killer" class="h-4 w-4 rounded border-gray-300 text-accent-600 focus:ring-accent-500 dark:bg-gray-700 dark:border-gray-600">
                                        <label class="ml-2 block text-sm text-gray-900 cursor-pointer dark:text-gray-300" for="oom_killer">
                                            {{ __('OOM Killer') }}
                                            <i class="fas fa-info-circle ml-1 text-gray-400 cursor-help dark:text-gray-500" data-toggle="popover" data-trigger="hover" data-content="{{ __('Enable or Disable the OOM Killer for this Product.') }}"></i>
                                        </label>
                                    </div>
                                </div>

                                <div class="flex justify-end mt-6">
                                    <button type="submit" class="rounded-md bg-accent-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 dark:hover:bg-accent-500">
                                        {{ __('Submit') }}
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-span-1">
                        <div class="bg-white rounded-lg shadow-md overflow-hidden dark:bg-gray-800">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h5 class="text-lg font-medium text-gray-800 dark:text-white">
                                    {{ __('Product Linking') }}
                                    <i class="fas fa-info-circle ml-1 text-gray-400 cursor-help dark:text-gray-500" data-toggle="popover" data-trigger="hover" data-content="{{ __('Link your products to nodes and eggs to create dynamic pricing for each option') }}"></i>
                                </h5>
                            </div>
                            <div class="p-6">
                                <div class="mb-4">
                                    <label for="nodes" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{ __('Nodes') }}</label>
                                    <select id="nodes" class="custom-select block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('nodes') border-red-500 @enderror"
                                            name="nodes[]" multiple="multiple" autocomplete="off">
                                        @foreach ($locations as $location)
                                            <optgroup label="{{ $location->name }}">
                                                @foreach ($location->nodes as $node)
                                                    <option @if (isset($product) && $product->nodes->contains('id', $node->id)) selected @endif value="{{ $node->id }}">{{ $node->name }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                    @error('nodes')
                                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                    <div class="text-xs text-gray-500 mt-1 dark:text-gray-400">
                                        {{ __('This product will only be available for these nodes') }}
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="mb-2 flex items-center justify-between">
                                        <label for="eggs" class="block text-sm font-medium text-gray-700 mb-0 dark:text-gray-300">{{ __('Eggs') }}</label>
                                        <div>
                                            <button type="button" id="select-all-eggs" class="rounded-md bg-gray-200 px-2 py-1 text-xs font-medium text-gray-700 hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">{{ __('Select All') }}</button>
                                            <button type="button" id="deselect-all-eggs" class="ml-2 rounded-md bg-gray-200 px-2 py-1 text-xs font-medium text-gray-700 hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">{{ __('Deselect All') }}</button>
                                        </div>
                                    </div>
                                    <select id="eggs" class="custom-select block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('eggs') border-red-500 @enderror"
                                            name="eggs[]" multiple="multiple" autocomplete="off">
                                        @foreach ($nests as $nest)
                                            <optgroup label="{{ $nest->name }}">
                                                @foreach ($nest->eggs as $egg)
                                                    <option @if (isset($product) && $product->eggs->contains('id', $egg->id)) selected @endif value="{{ $egg->id }}">{{ $egg->name }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                    @error('eggs')
                                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                    <div class="text-xs text-gray-500 mt-1 dark:text-gray-400">
                                        {{ __('This product will only be available for these eggs') }}
                                    </div>
                                </div>

                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('No Eggs or Nodes shown?') }} <a href="{{ route('admin.overview.sync') }}" class="text-accent-600 hover:underline dark:text-accent-400">{{ __('Sync now') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('[data-toggle="popover"]').popover();
            $('.custom-select').select2({
                minimumResultsForSearch: -1
            });

            document.getElementById('select-all-eggs').addEventListener('click', function() {
                $('#eggs option').prop('selected', true);
                $('#eggs').trigger('change');
            });

            document.getElementById('deselect-all-eggs').addEventListener('click', function() {
                $('#eggs option').prop('selected', false);
                $('#eggs').trigger('change');
            });
        });
    </script>
@endsection