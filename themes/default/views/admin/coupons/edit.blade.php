@extends('layouts.main')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-y-4 sm:flex-row sm:items-center">
        <h1 class="text-2xl font-bold text-gray-800">{{__('Coupon')}}</h1>
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
            <li>
                <a href="{{route('home')}}" class="hover:text-accent-500">{{__('Dashboard')}}</a>
            </li>
            <li>/</li>
            <li>
                <a href="{{route('admin.coupons.index')}}" class="hover:text-accent-500">{{__('Coupon')}}</a>
            </li>
            <li>/</li>
            <li>
                <span class="text-gray-700">{{__('Edit')}}</span>
            </li>
        </ol>
    </div>
    <section class="content">
        <div class="w-full">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="col-span-1">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h5 class="text-lg font-medium text-gray-800">
                                <i class="mr-2 fas fa-money-check-alt text-gray-500"></i>
                                {{__('Coupon details')}}
                            </h5>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST">
                                @csrf
                                @method('PATCH')

                                <div class="mb-4 flex flex-row-reverse">
                                    <div class="flex items-center">
                                        <input
                                            type="checkbox"
                                            id="random_codes"
                                            name="random_codes"
                                            class="h-4 w-4 rounded border-gray-300 text-accent-600 focus:ring-accent-500"
                                        >
                                        <label for="random_codes" class="ml-2 block text-sm text-gray-900 cursor-pointer">
                                            {{ __('Random Codes') }}
                                            <i
                                                data-toggle="popover"
                                                data-trigger="hover"
                                                data-content="{{__('Replace the creation of a single code with several at once with a custom field.')}}"
                                                class="fas fa-info-circle text-gray-400 ml-1 hover:text-accent-500 cursor-help">
                                            </i>
                                        </label>
                                    </div>
                                </div>

                                <div id="range_codes_element" style="display: none;" class="mb-4">
                                    <label for="range_codes" class="block text-sm font-medium text-gray-700 mb-1">
                                        {{ __('Range Codes') }}
                                        <i data-toggle="popover" data-trigger="hover" data-content="{{__('Generate a number of random codes.')}}" class="fas fa-info-circle text-gray-400 ml-1 cursor-help"></i>
                                    </label>
                                    <input
                                        type="number"
                                        id="range_codes"
                                        name="range_codes"
                                        step="any"
                                        min="1"
                                        max="100"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm @error('range_codes') border-red-500 @enderror"
                                    >
                                    @error('range_codes')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div id="coupon_code_element" class="mb-4">
                                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">
                                        {{ __('Coupon Code') }}
                                        <i data-toggle="popover" data-trigger="hover" data-content="{{__('The coupon code to be registered.')}}" class="fas fa-info-circle text-gray-400 ml-1 cursor-help"></i>
                                    </label>
                                    <input
                                        type="text"
                                        id="code"
                                        name="code"
                                        placeholder="SUMMER"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm @error('code') border-red-500 @enderror"
                                        value="{{ $coupon->code }}"
                                    >
                                    @error('code')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                                        {{ __('Coupon Type') }}
                                        <i data-toggle="popover" data-trigger="hover" data-content="{{__('The way the coupon should discount.')}}" class="fas fa-info-circle text-gray-400 ml-1 cursor-help"></i>
                                    </label>
                                    <select
                                        name="type"
                                        id="type"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm"
                                        required
                                    >
                                        <option value="percentage" @if($coupon->type == 'percentage') selected @endif>{{ __('Percentage') }}</option>
                                        <option value="amount" @if($coupon->type == 'amount') selected @endif>{{ __('Amount') }}</option>
                                    </select>
                                    @error('type')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="value" class="block text-sm font-medium text-gray-700 mb-1">
                                        {{ __('Coupon Value') }}
                                        <i data-toggle="popover" data-trigger="hover" data-content="{{__('The value that the coupon will represent.')}}" class="fas fa-info-circle text-gray-400 ml-1 cursor-help"></i>
                                    </label>
                                    <div class="flex rounded-md shadow-sm">
                                        <input
                                            name="value"
                                            id="value"
                                            type="number"
                                            step="any"
                                            min="1"
                                            class="block w-full rounded-none rounded-l-md border-gray-300 focus:border-accent-500 focus:ring-accent-500 sm:text-sm @error('value') border-red-500 @enderror"
                                            value="{{ $coupon->type == 'amount' ? Currency::formatForForm($coupon->value) : $coupon->value }}"
                                        >
                                        <span id="input_percentage" class="inline-flex items-center rounded-r-md border border-l-0 border-gray-300 bg-gray-50 px-3 text-gray-500 sm:text-sm">
                                            %
                                        </span>
                                    </div>
                                    @error('value')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="max_uses" class="block text-sm font-medium text-gray-700 mb-1">
                                        {{ __('Max uses') }}
                                        <i data-toggle="popover" data-trigger="hover" data-content="{{__('The maximum number of times the coupon can be used.')}}" class="fas fa-info-circle text-gray-400 ml-1 cursor-help"></i>
                                    </label>
                                    <input
                                        name="max_uses"
                                        id="max_uses"
                                        type="number"
                                        step="any"
                                        min="1"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm @error('max_uses') border-red-500 @enderror"
                                        value="{{ $coupon->max_uses }}"
                                    >
                                    @error('max_uses')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-1">
                                        {{ __('Expires at') }}
                                        <i data-toggle="popover" data-trigger="hover" data-content="{{__('The date when the coupon will expire (If no date is provided, the coupon never expires).')}}" class="fas fa-info-circle text-gray-400 ml-1 cursor-help"></i>
                                    </label>
                                    <div class="flex rounded-md shadow-sm date" id="expires_at" data-target-input="nearest">
                                        <input
                                            value="{{ $expired_at ?? '' }}"
                                            name="expires_at"
                                            placeholder="yyyy-mm-dd hh:mm:ss"
                                            type="text"
                                            class="block w-full rounded-none rounded-l-md border-gray-300 focus:border-accent-500 focus:ring-accent-500 sm:text-sm datetimepicker-input @error('expires_at') border-red-500 @enderror"
                                            data-target="#expires_at"
                                        />
                                        <div
                                            class="inline-flex items-center rounded-r-md border border-l-0 border-gray-300 bg-gray-50 px-3 text-gray-500 cursor-pointer hover:bg-gray-100"
                                            data-target="#expires_at"
                                            data-toggle="datetimepicker"
                                        >
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                    </div>
                                    @error('expires_at')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex justify-end mt-6">
                                    <button type="submit" class="rounded-md bg-accent-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2">
                                        {{__('Submit')}}
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
    <script>
        $(document).ready(function() {
            $('#expires_at').datetimepicker({
                format: 'Y-MM-DD HH:mm:ss',
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
            $('#random_codes').change(function() {
                if ($(this).is(':checked')) {
                    $('#coupon_code_element').prop('disabled', true).hide()
                    $('#range_codes_element').prop('disabled', false).show()

                    if ($('#code').val()) {
                        $('#code').prop('value', null)
                    }

                } else {
                    $('#coupon_code_element').prop('disabled', false).show()
                    $('#range_codes_element').prop('disabled', true).hide()

                    if ($('#range_codes').val()) {
                        $('#range_codes').prop('value', null)
                    }
                }
            })

            $('#type').change(function() {
                if ($(this).val() == 'percentage') {
                    $('#input_percentage').prop('disabled', false).show()
                } else {
                    $('#input_percentage').prop('disabled', true).hide()
                }
            })
        })
    </script>
@endsection