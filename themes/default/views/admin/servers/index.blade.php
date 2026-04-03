@extends('layouts.main')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-y-4 sm:flex-row sm:items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('Servers') }}</h1>
        <ol class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
            <li>
                <a href="{{ route('home') }}" class="hover:text-accent-600 dark:hover:text-accent-400">{{ __('Dashboard') }}</a>
            </li>
            <li>/</li>
            <li>
                <a class="text-gray-700 hover:text-accent-500 dark:text-gray-300 dark:hover:text-accent-400"
                   href="{{ route('admin.servers.index') }}">{{ __('Servers') }}</a>
            </li>
        </ol>
    </div>
    <section class="content">
        <div class="w-full">
            <div class="bg-white rounded-lg shadow-md overflow-hidden dark:bg-gray-800">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center dark:border-gray-700">
                    <div class="text-lg font-medium text-gray-800 dark:text-white">
                        <span><i class="mr-2 fas fa-server text-gray-500 dark:text-gray-400"></i>{{ __('Servers') }}</span>
                    </div>
                    <a href="{{ route('admin.servers.sync') }}" class="inline-flex items-center rounded-md bg-accent-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 dark:hover:bg-accent-500">
                        <i class="mr-2 fas fa-sync"></i>{{ __('Sync') }}
                    </a>
                </div>
                <div class="p-6">
                    @include('admin.servers.table')
                </div>
            </div>
        </div>

        @include("modals.server_suspend_reason_modal")

        </section>
    @endsection