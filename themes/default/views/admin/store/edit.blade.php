@extends('layouts.main')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-y-4 sm:flex-row sm:items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('Store') }}</h1>
        <ol class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
            <li>
                <a href="{{ route('home') }}" class="hover:text-accent-600 dark:hover:text-accent-400">{{ __('Dashboard') }}</a>
            </li>
            <li>/</li>
            <li>
                <a href="{{ route('admin.store.index') }}" class="hover:text-accent-500 dark:hover:text-accent-400">{{ __('Store') }}</a>
            </li>
            <li>/</li>
            <li>
                <span class="text-gray-700 dark:text-gray-300">{{ __('Edit') }}</span>
            </li>
        </ol>
    </div>
    <section class="content">
        <div class="w-full">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="col-span-1">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden dark:bg-gray-800">
                        <div class="p-6">
                            <form action="{{ route('admin.store.update', $shopProduct->id) }}" method="POST">
                                @csrf
                                @method('PATCH')

                                <div class="mb-4 flex flex-row-reverse">
                                    <div class="flex items-center">
                                        <input type="checkbox" @if ($shopProduct->disabled) checked @endif name="disabled" id="switch1"
                                               class="h-4 w-4 rounded border-gray-300 text-accent-600 focus:ring-accent-500 dark:bg-gray-700 dark:border-gray-600">
                                        <label class="ml-2 block text-sm text-gray-900 cursor-pointer dark:text-gray-300" for="switch1">
                                            {{ __('Disabled') }}
                                            <i class="fas fa-info-circle ml-1 text-gray-400 cursor-help dark:text-gray-500"
                                               data-toggle="popover" data-trigger="hover"
                                               data-content="{{ __('Will hide this option from being selected') }}"></i>
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{ __('Type') }}</label>
                                    <select required name="type" id="type" class="custom-select block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('name') border-red-500 @enderror">
                                        <option @if ($shopProduct->type == 'credits') selected @endif value="Credits">{{ $credits_display_name }}</option>
                                        <option @if ($shopProduct->type == 'Server slots') selected @endif value="Server slots">{{__("Server Slots")}}</option>
                                    </select>
                                    @error('name')
                                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="currency_code" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{ __('Currency code') }}</label>
                                    <select required name="currency_code" id="currency_code" class="custom-select block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('currency_code') border-red-500 @enderror">
                                        @foreach ($currencyCodes as $code)
                                            <option @if ($shopProduct->currency_code == $code) selected @endif value="{{ $code }}">
                                                {{ $code }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('currency_code')
                                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                    <div class="text-xs text-gray-500 mt-1 dark:text-gray-400">
                                        {{ __('Checkout the paypal docs to select the appropriate code') }} <a target="_blank" href="https://developer.paypal.com/docs/api/reference/currency-codes/" class="text-accent-600 hover:underline dark:text-accent-400">{{ __('Link') }}</a>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{ __('Price') }}</label>
                                    <input value="{{ Currency::formatForForm($shopProduct->price) }}" id="price" name="price" type="number" placeholder="10.00" step="any"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('price') border-red-500 @enderror"
                                           required="required">
                                    @error('price')
                                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{ __('Quantity') }}</label>
                                    <input value="{{ $shopProduct->type == 'Credits' ? Currency::formatForForm($shopProduct->quantity) : $shopProduct->quantity }}"
                                           id="quantity" name="quantity" type="number" placeholder="1000"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('quantity') border-red-500 @enderror"
                                           required="required">
                                    @error('quantity')
                                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                    <div class="text-xs text-gray-500 mt-1 dark:text-gray-400">
                                        {{ __('Amount given to the user after purchasing') }}
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="display" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{ __('Display') }}</label>
                                    <input value="{{ $shopProduct->display }}" id="display" name="display" type="text" placeholder="750 + 250"
                                           class="block w/full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('display') border-red-500 @enderror"
                                           required="required">
                                    @error('display')
                                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                    <div class="text-xs text-gray-500 mt-1 dark:text-gray-400">
                                        {{ __('This is what the user sees at store and checkout') }}
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{ __('Description') }}</label>
                                    <input value="{{ $shopProduct->description }}" id="description" name="description" type="text" placeholder="{{ __('Adds 1000 credits to your account') }}"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('description') border-red-500 @enderror"
                                           required="required">
                                    @error('description')
                                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                    <div class="text-xs text-gray-500 mt-1 dark:text-gray-400">
                                        {{ __('This is what the user sees at checkout') }}
                                    </div>
                                </div>

                                <div class="flex justify-end mt-6">
                                    <button type="submit" class="rounded-md bg-accent-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 dark:hover:bg-accent-500">
                                        {{ __('Submit') }}
                                    </button>
                                </div>

                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection