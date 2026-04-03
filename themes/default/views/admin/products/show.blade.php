@extends('layouts.main')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-y-4 sm:flex-row sm:items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('Products') }}</h1>
        <ol class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
            <li>
                <a href="{{ route('home') }}" class="hover:text-accent-500 dark:hover:text-accent-400">{{ __('Dashboard') }}</a>
            </li>
            <li>/</li>
            <li>
                <a href="{{ route('admin.users.index') }}" class="hover:text-accent-500 dark:hover:text-accent-400">{{ __('Products') }}</a>
            </li>
            <li>/</li>
            <li>
                <span class="text-gray-700 dark:text-gray-300">{{ __('Show') }}</span>
            </li>
        </ol>
    </div>
    <section class="content">
        <div class="w-full">
            <div class="mb-6 overflow-hidden rounded-lg bg-white shadow-md dark:bg-gray-800">
                <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                    <h5 class="text-lg font-medium text-gray-800 dark:text-white">
                        <i class="mr-2 fas fa-sliders-h text-gray-500 dark:text-gray-400"></i>{{ __('Product') }}
                    </h5>
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="rounded bg-accent-500 px-2 py-1 text-sm text-white hover:bg-accent-600" data-toggle="tooltip" title="Edit">
                            <i class="fas fa-pen"></i>
                        </a>
                        <form class="inline-block" onsubmit="return submitResult();" method="post" action="{{ route('admin.products.destroy', $product->id) }}">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                            <button class="rounded bg-red-500 px-2 py-1 text-sm text-white hover:bg-red-600" data-toggle="tooltip" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                        
                        <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-700">
                            <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('ID') }}</span>
                            <span class="truncate text-gray-600 dark:text-gray-400" style="max-width: 250px;">{{ $product->id }}</span>
                        </div>

                        <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-700">
                            <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('Name') }}</span>
                            <span class="truncate text-gray-600 dark:text-gray-400" style="max-width: 250px;">{{ $product->name }}</span>
                        </div>

                        <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-700">
                            <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('Price') }}</span>
                            <span class="truncate text-gray-600 dark:text-gray-400" style="max-width: 250px;">
                                <i class="mr-1 fas fa-coins"></i>{{ Currency::formatForDisplay($product->price) }}
                            </span>
                        </div>

                        <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-700">
                            <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('Minimum') }} {{ $credits_display_name }}</span>
                            <span class="truncate text-gray-600 dark:text-gray-400" style="max-width: 250px;">
                                <i class="mr-1 fas fa-coins"></i>{{ !$product->minimum_credits ? Currency::formatForDisplay($minimum_credits) : $product->display_minimum_credits }}
                            </span>
                        </div>

                        <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-700">
                            <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('Memory') }}</span>
                            <span class="truncate text-gray-600 dark:text-gray-400" style="max-width: 250px;">{{ $product->memory }}</span>
                        </div>

                        <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-700">
                            <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('CPU') }}</span>
                            <span class="truncate text-gray-600 dark:text-gray-400" style="max-width: 250px;">{{ $product->cpu }}</span>
                        </div>

                        <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-700">
                            <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('Swap') }}</span>
                            <span class="truncate text-gray-600 dark:text-gray-400" style="max-width: 250px;">{{ $product->swap }}</span>
                        </div>

                        <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-700">
                            <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('Disk') }}</span>
                            <span class="truncate text-gray-600 dark:text-gray-400" style="max-width: 250px;">{{ $product->disk }}</span>
                        </div>

                        <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-700">
                            <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('IO') }}</span>
                            <span class="truncate text-gray-600 dark:text-gray-400" style="max-width: 250px;">{{ $product->io }}</span>
                        </div>

                        <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-700">
                            <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('Databases') }}</span>
                            <span class="truncate text-gray-600 dark:text-gray-400" style="max-width: 250px;">{{ $product->databases }}</span>
                        </div>

                        <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-700">
                            <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('Allocations') }}</span>
                            <span class="truncate text-gray-600 dark:text-gray-400" style="max-width: 250px;">{{ $product->allocations }}</span>
                        </div>

                        <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-700">
                            <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('Created at') }}</span>
                            <span class="truncate text-gray-600 dark:text-gray-400" style="max-width: 250px;">
                                {{ $product->created_at ? $product->created_at->diffForHumans() : '' }}
                            </span>
                        </div>

                        <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-700">
                            <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('Description') }}</span>
                            <span class="truncate text-gray-600 dark:text-gray-400" style="max-width: 250px;">{{ $product->description }}</span>
                        </div>

                        <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-700">
                            <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('Updated at') }}</span>
                            <span class="truncate text-gray-600 dark:text-gray-400" style="max-width: 250px;">
                                {{ $product->updated_at ? $product->updated_at->diffForHumans() : '' }}
                            </span>
                        </div>

                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-lg bg-white shadow-md dark:bg-gray-800">
                <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                    <h5 class="text-lg font-medium text-gray-800 dark:text-white">
                        <i class="mr-2 fas fa-server text-gray-500 dark:text-gray-400"></i>{{ __('Servers') }}
                    </h5>
                </div>
                <div class="p-6">
                    @include('admin.servers.table' , ['filter' => '?product=' . $product->id])
                </div>
            </div>
        </div>
    </section>
    @endsection