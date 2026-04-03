@extends('layouts.main')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-y-4 sm:flex-row sm:items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('Vouchers') }}</h1>
        <ol class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
            <li>
                <a href="{{ route('home') }}" class="hover:text-accent-600 dark:hover:text-accent-400">{{ __('Dashboard') }}</a>
            </li>
            <li>/</li>
            <li>
                <a href="{{ route('admin.vouchers.index') }}" class="hover:text-accent-600 dark:hover:text-accent-400">{{ __('Vouchers') }}</a>
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
                        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center dark:border-gray-700">
                            <h5 class="text-lg font-medium text-gray-800 dark:text-white">
                                <i class="mr-2 fas fa-money-check-alt text-gray-500 dark:text-gray-400"></i>{{ __('Voucher details') }}
                            </h5>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('admin.vouchers.update', $voucher->id) }}" method="POST">
                                @csrf
                                @method('PATCH')

                                <div class="mb-4">
                                    <label for="memo" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">
                                        {{ __('Memo') }}
                                        <i class="fas fa-info-circle ml-1 text-gray-400 cursor-help dark:text-gray-500"
                                           data-toggle="popover" data-trigger="hover"
                                           data-content="Only admins can see this"></i>
                                    </label>
                                    <input value="{{ $voucher->memo }}" placeholder="{{ __('Summer break voucher') }}" id="memo" name="memo" type="text"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('memo') border-red-500 @enderror">
                                    @error('memo')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="credits" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">
                                        {{ $credits_display_name }} *
                                    </label>
                                    <input value="{{ Currency::formatForForm($voucher->credits) }}" placeholder="500" id="credits" name="credits" type="number" step="any" min="0" max="99999999"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('credits') border-red-500 @enderror">
                                    @error('credits')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">
                                        {{ __('Code') }} *
                                    </label>
                                    <div class="flex rounded-md shadow-sm">
                                        <input value="{{ $voucher->code }}" placeholder="SUMMER" id="code" name="code" type="text"
                                               class="block w-full rounded-l-md border-gray-300 focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('code') border-red-500 @enderror"
                                               required="required">
                                        <button class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-accent-500 text-white sm:text-sm hover:bg-accent-600 focus:outline-none focus:ring-2 focus:ring-accent-500 dark:border-gray-600"
                                                onclick="setRandomCode()" type="button">
                                            {{ __('Random') }}
                                        </button>
                                    </div>
                                    @error('code')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="uses" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">
                                        {{ __('Uses') }} *
                                        <i class="fas fa-info-circle ml-1 text-gray-400 cursor-help dark:text-gray-500"
                                           data-toggle="popover" data-trigger="hover"
                                           data-content="{{ __('A voucher can only be used one time per user. Uses specifies the number of different users that can use this voucher.') }}"></i>
                                    </label>
                                    <div class="flex rounded-md shadow-sm">
                                        <input value="{{ $voucher->uses }}" id="uses" min="1" max="2147483647" name="uses" type="number"
                                                   class="block w-full rounded-l-md border-gray-300 focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('expires_at') border-red-500 @enderror"
                                               required="required">
                                        <button class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-accent-500 text-white sm:text-sm hover:bg-accent-600 focus:outline-none focus:ring-2 focus:ring-accent-500 dark:border-gray-600"
                                                onclick="setMaxUses()" type="button">
                                            {{ __('Max') }}
                                        </button>
                                    </div>
                                    @error('uses')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-6">
                                    <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">
                                        {{ __('Expires at') }}
                                        <i class="fas fa-info-circle ml-1 text-gray-400 cursor-help dark:text-gray-500"
                                           data-toggle="popover" data-trigger="hover"
                                           data-content="Timezone: {{ Config::get('app.timezone') }}"></i>
                                    </label>
                                    <div class="relative" id="expires_at" data-target-input="nearest">
                                        <div class="flex rounded-md shadow-sm">
                                            <input value="{{ $voucher->expires_at ? $voucher->expires_at->format('d-m-Y H:i:s') : '' }}"
                                                   name="expires_at" placeholder="dd-mm-yyyy hh:mm:ss" type="text"
                                                   class="block w-full rounded-l-md border-gray-300 focus:border-accent-500 focus:ring-accent-500 sm:text-sm datetimepicker-input dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('expires_at') border-red-500 @enderror"
                                                   data-target="#expires_at"/>
                                            <div class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm cursor-pointer dark:bg-gray-600 dark:border-gray-500 dark:text-gray-300"
                                                 data-target="#expires_at" data-toggle="datetimepicker">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                        </div>
                                    </div>
                                    @error('expires_at')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit" class="rounded-md bg-accent-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 dark:hover:bg-accent-500">
                                        {{ __('Submit') }}
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <i class="fas"></i>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            $('#expires_at').datetimepicker({
                format: 'DD-MM-YYYY HH:mm:ss',
                icons: {
                    time: 'far fa-clock',
                    date: 'far fa-calendar',
                    up: 'fas fa-arrow-up',
                    down: 'fas fa-arrow-down',
                    previous: 'fas fa-chevron-left',
                    next: 'fas fa-chevron-right',
                    today: 'fas fa-calendar-check',
                    clear: 'far fa-trash-alt',
                    close: 'far fa-times-circle'
                }
            });
        })

        function setMaxUses() {
            let element = document.getElementById('uses')
            element.value = element.max;
            console.log(element.max)
        }

        function setRandomCode() {
            let element = document.getElementById('code')
            element.value = getRandomCode(36)
        }

        function getRandomCode(length) {
            let result = '';
            let characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-';
            let charactersLength = characters.length;
            for (let i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }
            return result;
        }
    </script>
@endsection