@extends('layouts.main')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-y-4 sm:flex-row sm:items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{__('Useful Links')}}</h1>
        <ol class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
            <li>
                <a href="{{route('home')}}" class="hover:text-accent-600 dark:hover:text-accent-400">{{__('Dashboard')}}</a>
            </li>
            <li>/</li>
            <li>
                <a href="{{route('admin.usefullinks.index')}}" class="hover:text-accent-500 dark:hover:text-accent-400">{{__('Useful Links')}}</a>
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
                            <form action="{{route('admin.usefullinks.store')}}" method="POST">
                                @csrf

                                <div class="mb-4">
                                    <label for="icon" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{__('Icon class name')}}</label>
                                    <input value="{{old('icon')}}" id="icon" name="icon" type="text" placeholder="fas fa-user"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('icon') border-red-500 @enderror"
                                           required="required">
                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        {{__('You can find available free icons')}} <a target="_blank" href="https://fontawesome.com/v5.15/icons?d=gallery&p=2" class="text-accent-600 hover:underline dark:text-accent-400">here</a>
                                    </div>
                                    @error('icon')
                                        <p class="mt-1 text-xs text-red-500">{{$message}}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{__('Title')}}</label>
                                    <input value="{{old('title')}}" id="title" name="title" type="text"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('title') border-red-500 @enderror"
                                           required="required">
                                    @error('title')
                                        <p class="mt-1 text-xs text-red-500">{{$message}}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="link" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{__('Link')}}</label>
                                    <input value="{{old('link')}}" id="link" name="link" type="text"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('link') border-red-500 @enderror"
                                           required="required">
                                    @error('link')
                                        <p class="mt-1 text-xs text-red-500">{{$message}}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{__('Description')}}</label>
                                    <textarea id="description" name="description" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('description') border-red-500 @enderror">{{old('description')}}</textarea>
                                    @error('description')
                                        <p class="mt-1 text-xs text-red-500">{{$message}}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="position" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{__('Position')}}</label>
                                    <select id="position" style="width:100%" class="custom-select block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('position') border-red-500 @enderror" name="position[]" required multiple autocomplete="off">
                                        @foreach ($positions as $position)
                                            <option id="{{$position->value}}" value="{{ $position->value }}">
                                                {{ __($position->value) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('position')
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
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            $('.custom-select').select2();
            // Summernote
            $('#description').summernote({
                height: 100,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ol', 'ul', 'paragraph', 'height']],
                    ['table', ['table']],
                    ['insert', ['link']],
                    ['view', ['undo', 'redo', 'fullscreen', 'codeview', 'help']]
                ]
            })
        })
    </script>
@endsection