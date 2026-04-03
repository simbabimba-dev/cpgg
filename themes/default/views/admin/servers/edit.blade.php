@extends('layouts.main')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-y-4 sm:flex-row sm:items-center">
        <div class="w-full">
            <div class="mb-4 border-l-4 border-red-500 bg-red-100 p-4 text-red-700 dark:bg-red-900/50 dark:text-red-300">
                <h5 class="font-bold flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i> {{ __('ATTENTION!') }}
                </h5>
                <p>
                    {{ __('Only edit these settings if you know exactly what you are doing ') }}
                    <br>
                    {{ __('You usually do not need to change anything here') }}
                </p>
            </div>
            
            <div class="flex flex-col justify-between gap-y-4 sm:flex-row sm:items-center">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('Edit Server') }}</h1>
                <ol class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                    <li>
                        <a href="{{ route('home') }}" class="hover:text-accent-500 dark:hover:text-accent-400">{{ __('Dashboard') }}</a>
                    </li>
                    <li>/</li>
                    <li>
                        <a href="{{ route('admin.servers.index') }}" class="hover:text-accent-500 dark:hover:text-accent-400">{{ __('Servers') }}</a>
                    </li>
                    <li>/</li>
                    <li>
                        <span class="text-gray-700 dark:text-gray-300">{{ __('Edit') }}</span>
                    </li>
                </ol>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="w-full">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="col-span-1">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden dark:bg-gray-800">
                        <div class="p-6">
                            <form action="{{ route('admin.servers.update', $server->id) }}" method="POST">
                                @csrf
                                @method('PATCH')

                                <div class="mb-4">
                                    <label for="identifier" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">
                                        {{ __('Server identifier') }}
                                        <i class="fas fa-info-circle ml-1 text-gray-400 cursor-help dark:text-gray-500"
                                           data-toggle="popover" data-trigger="hover"
                                           data-content="{{ __('Change the server identifier on CtrlPanel to match a pterodactyl server.') }}"></i>
                                    </label>
                                    <input value="{{ $server->identifier }}" id="identifier" name="identifier"
                                           type="text" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('identifier') border-red-500 @enderror"
                                           required="required">
                                    @error('identifier')
                                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">
                                        {{ __('Server owner') }}
                                        <i class="fas fa-info-circle ml-1 text-gray-400 cursor-help dark:text-gray-500"
                                           data-toggle="popover" data-trigger="hover"
                                           data-content="{{ __('Change the current server owner on CtrlPanel and pterodactyl.') }}"></i>
                                    </label>
                                    <select name="user_id" id="user_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('user_id') border-red-500 @enderror">
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}" @if ($user->id == $server->user_id) selected @endif>
                                                {{ $user->name }} ({{ $user->email }}) ({{ $user->id }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="flex justify-end mt-6">
                                    <button type="submit" class="rounded-md bg-accent-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 dark:hover:bg-accent-500">
                                        {{ __('Submit') }}
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('[data-toggle="popover"]').popover();
        });
    </script>
@endsection