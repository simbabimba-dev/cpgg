@extends('layouts.main')

@section('content')
    <section class="content-header">
        <div class="mb-6 flex flex-col justify-between gap-y-4 sm:flex-row sm:items-center">
            <div class="col-sm-6">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ isset($role) ?  __('Edit role') : __('Create role') }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400 justify-end">
                    <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}" class="hover:text-accent-600 dark:hover:text-accent-400">{{ __('Dashboard') }}</a></li>
                    <li>/</li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}" class="hover:text-accent-500 dark:hover:text-accent-400">{{ __('Roles List') }}</a></li>
                    <li>/</li>
                    <li class="breadcrumb-item">
                        <a class="text-gray-700 dark:text-gray-300" href="{{ isset($role) ?  route('admin.roles.edit', $role->id) : route('admin.roles.create') }}">{{ isset($role) ?  __('Edit role') : __('Create role') }}</a>
                    </li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="w-full">
            <div class="bg-white rounded-lg shadow-md overflow-hidden dark:bg-gray-800">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center dark:border-gray-700">
                    <h5 class="text-lg font-medium text-gray-800 dark:text-white">
                        <i class="mr-2 fas fa-user-check text-gray-500 dark:text-gray-400"></i>{{ isset($role) ?  __('Edit role') : __('Create role') }}
                    </h5>
                </div>
                <div class="p-6">
                    <div class="col-12 p-0">
                        <form method="post" action="{{isset($role) ? route('admin.roles.update', $role->id) : route('admin.roles.store')}}">
                            @csrf
                            @isset($role)
                                @method('PATCH')
                            @endisset

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="col-span-1">
                                    <div class="mb-4">
                                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{ __('Name') }}</label>
                                        <input type="text" name="name" id="name" value="{{ isset($role) ? $role->name : null}}"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('name') border-red-500 @enderror">
                                        @error('name')
                                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="color" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{ __('Badge color') }}</label>
                                        <input type="color" name="color" id="color" value="{{ isset($role) ? $role->color : null}}"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm h-10 p-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('color') border-red-500 @enderror">
                                        @error('color')
                                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="power" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{ __('Power') }}</label>
                                        <input type="number" name="power" id="power" min="1" max="100" step="1" value="{{ isset($role) ? $role->power : 10}}"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('power') border-red-500 @enderror">
                                        @error('power')
                                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-span-1">
                                    <div class="mb-4">
                                        <label for="permissions" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{ __('Permissions') }}</label>
                                        <select name="permissions[]" id="permissions" multiple class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" style="height: 200px">
                                            @foreach($permissions as $permission)
                                                <option @if(isset($role) && $role->permissions->contains($permission)) selected @endif value="{{$permission->id}}">{{$permission->readable_name}}</option>
                                            @endforeach
                                        </select>
                                        @error('permissions')
                                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end">
                                <button name="submit" type="submit" class="rounded-md bg-accent-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 dark:hover:bg-accent-500">
                                    {{__('Submit')}}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            $('#permissions').select2({
                closeOnSelect: false
            });
        })
    </script>
@endsection