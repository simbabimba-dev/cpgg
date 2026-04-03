@extends('layouts.main')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-y-4 sm:flex-row sm:items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{__('Application API')}}</h1>
        <ol class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
            <li>
                <a href="{{route('home')}}" class="hover:text-accent-500 dark:hover:text-accent-400">{{__('Dashboard')}}</a>
            </li>
            <li>/</li>
            <li>
                <a href="{{route('admin.api.index')}}" class="hover:text-accent-500 dark:hover:text-accent-400">{{__('Application API')}}</a>
            </li>
            <li>/</li>
            <li>
                <span class="text-gray-700 dark:text-gray-300">{{__('Create')}}</span>
            </li>
        </ol>
    </div>
    <section class="content">
        <div class="w-full">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="col-span-1">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden dark:bg-gray-800">
                        <div class="p-6">
                            <form action="{{route('admin.api.store')}}" method="POST">
                                @csrf

                                <div class="mb-4">
                                    <label for="memo" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{__('Memo')}}</label>
                                    <input value="{{old('memo')}}" id="memo" name="memo" type="text"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('memo') border-red-500 @enderror">
                                    @error('memo')
                                    <p class="mt-1 text-xs text-red-500">{{$message}}</p>
                                    @enderror
                                </div>

                                <div class="flex justify-end mt-6">
                                    <button type="submit" class="rounded-md bg-accent-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 dark:hover:bg-accent-500">
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
    @endsection