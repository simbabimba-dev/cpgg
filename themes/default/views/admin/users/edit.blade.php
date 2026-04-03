@extends('layouts.main')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-y-4 sm:flex-row sm:items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{__('Users')}}</h1>
        <ol class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
            <li>
                <a href="{{route('home')}}" class="hover:text-accent-600 dark:hover:text-accent-400">{{__('Dashboard')}}</a>
            </li>
            <li>/</li>
            <li>
                <a href="{{route('admin.users.index')}}" class="hover:text-accent-600 dark:hover:text-accent-400">{{__('Users')}}</a>
            </li>
            <li>/</li>
            <li>
                <span class="text-gray-700 dark:text-gray-300">{{__('Edit')}}</span>
            </li>
        </ol>
    </div>
    <section class="content">
        <div class="w-full">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="col-span-1">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden dark:bg-gray-800">
                        <div class="p-6">
                            <form action="{{route('admin.users.update', $user->id)}}" method="POST">
                                @csrf
                                @method('PATCH')

                                <div class="mb-4">
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{__('Username')}}</label>
                                    <input value="{{$user->name}}" id="name" name="name" type="text"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('name') border-red-500 @enderror"
                                           required="required">
                                    @error('name')
                                        <p class="mt-1 text-xs text-red-500">{{$message}}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{__('Email')}}</label>
                                    <input value="{{$user->email}}" id="email" name="email" type="text"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('email') border-red-500 @enderror"
                                           required="required">
                                    @error('email')
                                        <p class="mt-1 text-xs text-red-500">{{$message}}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="pterodactyl_id" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{__('Pterodactyl ID')}}</label>
                                    <input value="{{$user->pterodactyl_id}}" id="pterodactyl_id" name="pterodactyl_id" type="number"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('pterodactyl_id') border-red-500 @enderror"
                                           required="required">
                                    @error('pterodactyl_id')
                                        <p class="mt-1 text-xs text-red-500">{{$message}}</p>
                                    @enderror
                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        {{__('This ID refers to the user account created on pterodactyls panel.')}} <br>
                                        <small>{{__('Only edit this if you know what youre doing :)')}}</small>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="credits" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{ $credits_display_name }}</label>
                                    <input value="{{ Currency::formatForForm($user->credits) }}" id="credits" name="credits" step="any" min="0" max="99999999" type="number"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('credits') border-red-500 @enderror"
                                           required="required">
                                    @error('credits')
                                        <p class="mt-1 text-xs text-red-500">{{$message}}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="server_limit" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{__('Server Limit')}}</label>
                                    <input value="{{$user->server_limit}}" id="server_limit" name="server_limit" min="0" max="1000000" type="number"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('server_limit') border-red-500 @enderror"
                                           required="required">
                                    @error('server_limit')
                                        <p class="mt-1 text-xs text-red-500">{{$message}}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="roles" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{__('Role')}}</label>
                                    <select id="roles" name="roles" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('role') border-red-500 @enderror" required="required">
                                        @foreach($roles as $role)
                                            <option style="color: {{$role->color}}" value="{{$role->id}}" @if(isset($user) && $user->roles->contains($role)) selected @endif>{{$role->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('role')
                                        <p class="mt-1 text-xs text-red-500">{{$message}}</p>
                                    @enderror
                                </div>

                                <div class="mb-6">
                                    <label for="referral_code" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{__('Referral-Code')}}</label>
                                    <input value="{{$user->referral_code}}" id="referral_code" name="referral_code" type="text"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('referral_code') border-red-500 @enderror"
                                           required="required">
                                    @error('referral_code')
                                        <p class="mt-1 text-xs text-red-500">{{$message}}</p>
                                    @enderror
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit" class="rounded-md bg-accent-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 dark:hover:bg-accent-500">
                                        {{__('Submit')}}
                                    </button>
                                </div>
                            
                        </div>
                    </div>
                </div>

                <div class="col-span-1">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden dark:bg-gray-800">
                        <div class="p-6">
                            
                                <div class="mb-4">
                                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{__('New Password')}}</label>
                                    <input id="new_password" name="new_password" type="password" placeholder="••••••"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('new_password') border-red-500 @enderror">
                                    @error('new_password')
                                        <p class="mt-1 text-xs text-red-500">{{$message}}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{__('Confirm Password')}}</label>
                                    <input id="new_password_confirmation" name="new_password_confirmation" type="password" placeholder="••••••"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('new_password_confirmation') border-red-500 @enderror">
                                    @error('new_password_confirmation')
                                        <p class="mt-1 text-xs text-red-500">{{$message}}</p>
                                    @enderror
                                </div>
                                <div class="flex justify-end">
                                     <button type="submit" class="rounded-md bg-accent-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 dark:hover:bg-accent-500">
                                        {{__('Update Password')}}
                                    </button>
                                </div>
                            </form> </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endsection