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
                <span class="text-gray-700 dark:text-gray-300">{{ __('Notifications') }}</span>
            </li>
        </ol>
    </div>
    <section class="content">
        <div class="w-full">
            <div class="bg-white rounded-lg shadow-md overflow-hidden dark:bg-gray-800">
                <div class="p-6">
                    <form action="{{ route('admin.users.notifications.notify') }}" method="POST">
                        @csrf
                        @method('POST')

                        <div class="mb-6">
                            <div class="flex items-center mb-4">
                                <input id="all" name="all" type="checkbox" value="1"
                                       class="h-4 w-4 rounded border-gray-300 text-accent-600 focus:ring-accent-500 dark:bg-gray-700 dark:border-gray-600"
                                       onchange="toggleClass('users-form', 'hidden')">
                                <label for="all" class="ml-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('All') }}</label>
                            </div>
                            @error('all')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror

                            <div id="users-form" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{ __('Users') }}</label>
                                    <select id="users" name="users[]" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" multiple></select>
                                    @error('users')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{ __('Roles') }}</label>
                                    <select id="roles" name="roles[]" onchange="toggleClass('users', 'hidden')" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" multiple>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300">{{ __('Send via') }}</label>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input value="database" id="database" name="via[]" type="checkbox"
                                           class="h-4 w-4 rounded border-gray-300 text-accent-600 focus:ring-accent-500 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="database" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">{{ __('Database') }}</label>
                                </div>
                                <div class="flex items-center">
                                    <input value="mail" id="mail" name="via[]" type="checkbox"
                                           class="h-4 w-4 rounded border-gray-300 text-accent-600 focus:ring-accent-500 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="mail" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">{{ __('Email') }}</label>
                                </div>
                            </div>
                            @error('via')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{ __('Title') }}</label>
                            <input value="{{ old('title') }}" id="title" name="title" type="text"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('title') border-red-500 @enderror">
                            @error('title')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="content" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{ __('Content') }}</label>
                            <textarea id="content" name="content" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('content') border-red-500 @enderror">{{ old('content') }}</textarea>
                            @error('content')
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
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            // Summernote
            $('#content').summernote({
                height: 100,
                toolbar: [
                    [ 'style', [ 'style' ] ],
                    [ 'font', [ 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear'] ],
                    [ 'fontname', [ 'fontname' ] ],
                    [ 'fontsize', [ 'fontsize' ] ],
                    [ 'color', [ 'color' ] ],
                    [ 'para', [ 'ol', 'ul', 'paragraph', 'height' ] ],
                    [ 'table', [ 'table' ] ],
                    [ 'insert', [ 'link'] ],
                    [ 'view', [ 'undo', 'redo', 'fullscreen', 'codeview', 'help' ] ]
                ]
            })

            function initUserSelect(data) {
                $('#roles').select2();
                $('#users').select2({
                    ajax: {
                        url: '/admin/users.json',
                        dataType: 'json',
                        delay: 250,

                        data: function (params) {
                            return {
                                filter: { email: params.term },
                                page: params.page,
                            };
                        },

                        processResults: function (data, params) {
                            return { results: data };
                        },

                        cache: true,
                    },
                    data: data,
                    minimumInputLength: 2,
                    templateResult: function (data) {
                        if (data.loading) return data.text;
                        const $container = $(
                            "<div class='clearfix select2-result-users flex items-center'>" +
                                "<div class='select2-result-users__avatar flex items-center'><img class='h-8 w-8 rounded-full border border-gray-200' src='" + data.avatarUrl + "?s=40' /></div>" +
                                "<div class='select2-result-users__meta ml-3'>" +
                                    "<div class='select2-result-users__username text-base font-medium'></div>" +
                                    "<div class='select2-result-users__email text-sm text-gray-500'></div>" +
                                "</div>" +
                            "</div>"
                        );

                        $container.find(".select2-result-users__username").text(data.name);
                        $container.find(".select2-result-users__email").text(data.email);

                        return $container;
                    },
                    templateSelection: function (data) {
                            $container = $('<div class="flex items-center"> \
                                            <span> \
                                                <img class="h-6 w-6 rounded-full border border-gray-200" src="' + data.avatarUrl + '?s=120" alt="User Image"> \
                                            </span> \
                                            <span class="select2-selection-users__username px-2"></span> \
                                    </div>');
                            $container.find(".select2-selection-users__username").text(data.name);
                            return $container;
                        }
                    })
                }
                initUserSelect()
            })

        function toggleClass(id, className) {
            document.getElementById(id).classList.toggle(className)
        }
    </script>
@endsection