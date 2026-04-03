@extends('layouts.main')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-y-4 sm:flex-row sm:items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('Users') }}</h1>
        <ol class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
            <li>
                <a href="{{ route('home') }}" class="hover:text-accent-600 dark:hover:text-accent-400">{{ __('Dashboard') }}</a>
            </li>
            <li>/</li>
            <li>
                <a href="{{ route('admin.users.index') }}" class="hover:text-accent-600 dark:hover:text-accent-400">{{ __('Users') }}</a>
            </li>
            <li>/</li>
            <li>
                <span class="text-gray-700 dark:text-gray-300">{{ __('Show') }}</span>
            </li>
        </ol>
    </div>
    <section class="content">
        <div class="w-full">
            @if ($user->discordUser)
                <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gray-800 text-white rounded-lg shadow-md overflow-hidden">
                        <div class="flex justify-between items-center p-4">
                            <div>
                                <h3 class="text-xl font-bold">{{ $user->discordUser->username }} <sup class="text-xs">{{ $user->discordUser->locale }}</sup></h3>
                                <p class="text-sm text-gray-400">{{ $user->discordUser->id }}</p>
                            </div>
                            <img src="{{ $user->discordUser->getAvatar() }}" alt="avatar" class="w-16 h-16 rounded-full border-2 border-gray-600">
                        </div>
                        <div class="bg-gray-700 p-2 text-center text-sm">
                            <i class="fab fa-discord mr-1"></i> Discord
                        </div>
                    </div>
                </div>
            @endif

            <div class="mb-6 bg-white rounded-lg shadow-md overflow-hidden dark:bg-gray-800">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h5 class="text-lg font-medium text-gray-800 dark:text-white">
                        <i class="fas fa-users mr-2 text-gray-500 dark:text-gray-400"></i>{{ __('Users') }}
                    </h5>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-700">
                            <span class="font-medium text-gray-600 dark:text-gray-400">{{ __('ID') }}</span>
                            <span class="text-gray-800 dark:text-white">{{ $user->id }}</span>
                        </div>

                        <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-700">
                            <span class="font-medium text-gray-600 dark:text-gray-400">{{ __('Role') }}</span>
                            <div class="flex flex-wrap gap-1 justify-end">
                                @foreach ($user->roles as $role)
                                    <span style="background-color: {{$role->color}}" class="px-2 py-1 text-xs text-white rounded">{{$role->name}}</span>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-700">
                            <span class="font-medium text-gray-600 dark:text-gray-400">{{ __('Pterodactyl ID') }}</span>
                            <span class="text-gray-800 dark:text-white">{{ $user->pterodactyl_id }}</span>
                        </div>

                        <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-700">
                            <span class="font-medium text-gray-600 dark:text-gray-400">{{ __('Email') }}</span>
                            <span class="text-gray-800 dark:text-white truncate max-w-[200px]">{{ $user->email }}</span>
                        </div>

                        <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-700">
                            <span class="font-medium text-gray-600 dark:text-gray-400">{{ __('Server limit') }}</span>
                            <span class="text-gray-800 dark:text-white">{{ $user->Servers()->count() }} / {{ $user->server_limit }}</span>
                        </div>

                        <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-700">
                            <span class="font-medium text-gray-600 dark:text-gray-400">{{ __('Name') }}</span>
                            <span class="text-gray-800 dark:text-white">{{ $user->name }}</span>
                        </div>

                        <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-700">
                            <span class="font-medium text-gray-600 dark:text-gray-400">{{ __('Verified') }} {{ __('Email') }}</span>
                            <span class="text-gray-800 dark:text-white">{{ $user->email_verified_at ? 'True' : 'False' }}</span>
                        </div>

                        <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-700">
                            <span class="font-medium text-gray-600 dark:text-gray-400">{{ $credits_display_name }}</span>
                            <span class="text-gray-800 dark:text-white"><i class="fas fa-coins mr-1 text-yellow-500"></i>{{ Currency::formatForDisplay($user->credits) }}</span>
                        </div>

                        <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-700">
                            <span class="font-medium text-gray-600 dark:text-gray-400">{{ __('Verified') }} {{ __('Discord') }}</span>
                            <span class="text-gray-800 dark:text-white">{{ $user->discordUser ? 'True' : 'False' }}</span>
                        </div>

                        <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-700">
                            <span class="font-medium text-gray-600 dark:text-gray-400">{{ __('IP') }}</span>
                            <span class="text-gray-800 dark:text-white">{{ $user->ip }}</span>
                        </div>

                        <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-700">
                            <span class="font-medium text-gray-600 dark:text-gray-400">{{ __('Usage') }}</span>
                            <span class="text-gray-800 dark:text-white"><i class="fas fa-coins mr-1 text-yellow-500"></i>{{ Currency::formatForDisplay($user->creditUsage()) }}</span>
                        </div>

                        <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-700">
                            <span class="font-medium text-gray-600 dark:text-gray-400">{{ __('Referred by') }}</span>
                            <span class="text-gray-800 dark:text-white">{{ $user->referredBy() != Null ? $user->referredBy()->name : "None" }}</span>
                        </div>

                        <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-700">
                            <span class="font-medium text-gray-600 dark:text-gray-400">{{ __('Created at') }}</span>
                            <span class="text-gray-800 dark:text-white">{{ $user->created_at->diffForHumans() }}</span>
                        </div>

                        <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-700">
                            <span class="font-medium text-gray-600 dark:text-gray-400">{{ __('Last seen') }}</span>
                            <span class="text-gray-800 dark:text-white">{{ $user->last_seen ? $user->last_seen->diffForHumans() : 'Null' }}</span>
                        </div>

                    </div>
                </div>
            </div>

            <div class="mb-6 bg-white rounded-lg shadow-md overflow-hidden dark:bg-gray-800">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h5 class="text-lg font-medium text-gray-800 dark:text-white">
                        <i class="fas fa-server mr-2 text-gray-500 dark:text-gray-400"></i>{{ __('Servers') }}
                    </h5>
                </div>
                <div class="p-6">
                    @include('admin.servers.table', ['filter' => '?user=' . $user->id])
                </div>
            </div>

            <div class="mb-6 bg-white rounded-lg shadow-md overflow-hidden dark:bg-gray-800">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h5 class="text-lg font-medium text-gray-800 dark:text-white">
                        <i class="fas fa-user-check mr-2 text-gray-500 dark:text-gray-400"></i>{{ __('Referrals') }}
                        <span class="text-sm font-normal text-gray-500 ml-2">({{ __('referral-code') }}: {{ $user->referral_code }})</span>
                    </h5>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($referrals as $referral)
                            <div class="bg-gray-50 rounded p-3 flex justify-between items-center dark:bg-gray-700">
                                <span class="text-sm text-gray-600 dark:text-gray-300">ID: {{ $referral->id }}</span>
                                <a href="{{ route('admin.users.show', $referral->id) }}" class="text-accent-600 hover:underline flex items-center dark:text-accent-400">
                                    <i class="fas fa-user-check mr-1"></i> {{ $referral->name }}
                                </a>
                                <span class="text-xs text-gray-500 flex items-center dark:text-gray-400">
                                    <i class="fas fa-clock mr-1"></i> {{ $referral->created_at->diffForHumans() }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </section>
    @endsection