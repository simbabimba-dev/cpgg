@extends('layouts.main')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-y-4 sm:flex-row sm:items-center">
        <div class="col-sm-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('Settings') }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400 justify-end">
                <li class="breadcrumb-item"><a href="" class="hover:text-accent-600 dark:hover:text-accent-400">{{ __('Dashboard') }}</a></li>
                <li>/</li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.settings.index') }}" class="text-gray-700 hover:text-accent-600 dark:text-gray-300 dark:hover:text-accent-400">{{ __('Settings') }}</a>
                </li>
            </ol>
        </div>
    </div>
    @if (!file_exists(base_path() . '/install.lock'))
        <div class="mb-4 border-l-4 border-red-500 bg-red-100 p-4 text-red-700 dark:bg-red-900/50 dark:text-red-300">
            <h4 class="font-bold">{{ __('The installer is not locked!') }}</h4>
            <p>{{ __('please create a file called "install.lock" in your dashboard Root directory. Otherwise no settings will be loaded!') }}</p>
            <a href="/install?step=7">
                <button class="mt-2 rounded border border-red-500 px-4 py-2 text-sm font-medium text-red-500 hover:bg-red-50 dark:border-red-400 dark:text-red-400 dark:hover:bg-red-900">{{ __('or click here') }}</button>
            </a>
        </div>
    @endif

    <section class="content">
        <div class="w-full">
            <div class="bg-white rounded-lg shadow-md overflow-hidden dark:bg-gray-800">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center dark:border-gray-700">
                    <h5 class="text-lg font-medium text-gray-800 dark:text-white">
                        <i class="mr-2 fas fa-tools text-gray-500 dark:text-gray-400"></i>{{ __('Settings') }}
                    </h5>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                        <div class="md:col-span-3 lg:col-span-2">
                            <nav>
                                <ul class="nav nav-pills flex flex-col space-y-1" role="tablist">
                                    @can("admin.icons.edit")
                                        <li>
                                            <a href="#icons" class="nav-link flex items-center px-4 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white" data-toggle="pill" role="tab">
                                                <i class="nav-icon fas fa-image mr-3"></i>
                                                {{ __('Images / Icons') }}
                                            </a>
                                        </li>
                                    @endcan
                                    @foreach ($settings as $category => $options)
                                        @if (!str_contains($options['settings_class'], 'Extension'))
                                            @canany(['settings.' . strtolower($category) . '.read', 'settings.' . strtolower($category) . '.write'])
                                                <li>
                                                    <a href="#{{ $category }}"
                                                       class="nav-link flex items-center px-4 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white {{ $loop->first ? 'active bg-accent-50 text-accent-700 dark:bg-accent-800 dark:text-white' : '' }}"
                                                       data-toggle="pill" role="tab">
                                                        <i class="nav-icon mr-3 {{ $options['category_icon'] ?? 'fas fa-cog' }}"></i>
                                                        {{ $category }}
                                                    </a>
                                                </li>
                                            @endcanany
                                        @endif
                                    @endforeach
                                </ul>

                                <button class="mt-4 w-full text-left px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700"
                                        type="button" data-toggle="collapse" data-target="#collapseExtensions" aria-expanded="false" aria-controls="collapseExtensions">
                                    {{ __('Extension Settings') }}
                                </button>

                                <div class="collapse mt-2" id="collapseExtensions">
                                    <ul class="nav nav-pills flex flex-col space-y-1" role="tablist">
                                        @foreach ($settings as $category => $options)
                                            @if (str_contains($options['settings_class'], 'Extension'))
                                                @canany(['settings.' . strtolower($category) . '.read', 'settings.' . strtolower($category) . '.write'])
                                                    <li>
                                                        <a href="#{{ $category }}" class="nav-link flex items-center px-4 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white" data-toggle="pill" role="tab">
                                                            <i class="nav-icon fas mr-3 {{ $options['category_icon'] ?? 'fas fa-cog' }}"></i>
                                                            {{ $category }}
                                                        </a>
                                                    </li>
                                                @endcanany
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </nav>
                        </div>

                        <div class="md:col-span-9 lg:col-span-10">
                            <div class="tab-content">
                                <div class="tab-pane fade" id="icons" role="tabpanel">
                                    <form method="POST" enctype="multipart/form-data" class="mb-3" action="{{ route('admin.settings.updateIcons') }}">
                                        @csrf
                                        @method('POST')
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                            
                                            <div class="flex flex-col items-center">
                                                @error('favicon')
                                                <p class="text-red-500 text-sm mb-2">{{ $message }}</p>
                                                @enderror
                                                <div class="w-full bg-white dark:bg-gray-700 rounded-lg shadow p-4">
                                                    <h3 class="text-center text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('FavIcon') }}</h3>
                                                    <img src="{{ $images['favicon'] }}" class="mx-auto block mb-4 w-12" alt="Favicon">
                                                    <input type="file" accept="image/x-icon" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-accent-50 file:text-accent-700 hover:file:bg-accent-100 dark:text-gray-300 dark:file:bg-gray-600 dark:file:text-gray-200" name="favicon" id="favicon">
                                                </div>
                                            </div>

                                            <div class="flex flex-col items-center">
                                                @error('icon')
                                                <p class="text-red-500 text-sm mb-2">{{ $message }}</p>
                                                @enderror
                                                <div class="w-full bg-white dark:bg-gray-700 rounded-lg shadow p-4">
                                                    <h3 class="text-center text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Icon') }}</h3>
                                                    <img src="{{ $images['icon'] }}" class="mx-auto block mb-4 w-12" alt="Icon">
                                                    <input type="file" accept="image/png,image/jpeg,image/jpg" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-accent-50 file:text-accent-700 hover:file:bg-accent-100 dark:text-gray-300 dark:file:bg-gray-600 dark:file:text-gray-200" name="icon" id="icon">
                                                </div>
                                            </div>

                                            <div class="flex flex-col items-center">
                                                @error('logo')
                                                <p class="text-red-500 text-sm mb-2">{{ $message }}</p>
                                                @enderror
                                                <div class="w-full bg-white dark:bg-gray-700 rounded-lg shadow p-4">
                                                    <h3 class="text-center text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Login-page Logo') }}</h3>
                                                    <img src="{{ $images['logo'] }}" class="mx-auto block mb-4 w-12" alt="Logo">
                                                    <input type="file" accept="image/png,image/jpeg,image/jpg" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-accent-50 file:text-accent-700 hover:file:bg-accent-100 dark:text-gray-300 dark:file:bg-gray-600 dark:file:text-gray-200" name="logo" id="logo">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-6 flex justify-end">
                                            <button type="submit" class="rounded-md bg-accent-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 dark:hover:bg-accent-500">{{ __('Save') }}</button>
                                        </div>
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    </form>
                                </div>

                                @foreach ($settings as $category => $options)
                                    @canany(['settings.' . strtolower($category) . '.read', 'settings.' . strtolower($category) . '.write'])
                                        <div class="tab-pane fade {{ $loop->first ? 'active show' : '' }}" id="{{ $category }}" role="tabpanel">
                                            <form action="{{ route('admin.settings.update') }}" method="POST">
                                                @csrf
                                                @method('POST')
                                                <input type="hidden" name="settings_class" value="{{ $options['settings_class'] }}">
                                                <input type="hidden" name="category" value="{{ $category }}">
                                                
                                                @foreach ($options as $key => $value)
                                                    @if ($key == 'category_icon' || $key == 'settings_class' || $key == 'position')
                                                        @continue
                                                    @endif
                                                    
                                                    <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                                                        <div class="md:col-span-1">
                                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 flex justify-between items-center" for="{{ $key }}">
                                                                {{ $value['label'] }}
                                                                @if ($value['description'])
                                                                    <i class="fas fa-info-circle text-gray-400 cursor-help" data-toggle="popover"
                                                                       data-trigger="hover" data-placement="top" data-html="true"
                                                                       data-content="{{ $value['description'] }}"></i>
                                                                @endif
                                                            </label>
                                                        </div>
                                                        <div class="md:col-span-2">
                                                            @switch($value)
                                                                @case($value['type'] == 'string')
                                                                    <input type="text" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                                           name="{{ $key }}" value="{{ $value['value'] }}">
                                                                    @break

                                                                @case($value['type'] == 'password')
                                                                    <input type="password" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                                           name="{{ $key }}" value="{{ $value['value'] }}">
                                                                    @break

                                                                @case($value['type'] == 'boolean')
                                                                    <div class="flex items-center">
                                                                        <input type="checkbox" name="{{ $key }}" value="{{ $value['value'] }}"
                                                                               class="h-4 w-4 rounded border-gray-300 text-accent-600 focus:ring-accent-500 dark:bg-gray-700 dark:border-gray-600"
                                                                               {{ $value['value'] ? 'checked' : '' }}>
                                                                    </div>
                                                                    @break

                                                                @case($value['type'] == 'number')
                                                                    <input type="number" step="{{ $value['step'] ?? '1' }}"
                                                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                                           name="{{ $key }}" value="{{ isset($value['converted_value']) ? $value['converted_value'] : $value['value'] }}">
                                                                    @break

                                                                @case($value['type'] == 'select')
                                                                    <select id="{{ $key }}" class="custom-select block w/full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                                            name="{{ $key }}">
                                                                        @if ($value['identifier'] == 'display')
                                                                            @foreach ($value['options'] as $option => $display)
                                                                                <option value="{{ $display }}" {{ $value['value'] == $display ? 'selected' : '' }}>
                                                                                    {{ __($display) }}
                                                                                </option>
                                                                            @endforeach
                                                                        @else
                                                                            @foreach ($value['options'] as $option => $display)
                                                                                <option value="{{ $option }}" {{ $value['value'] == $option ? 'selected' : '' }}>
                                                                                    {{ __($display) }}
                                                                                </option>
                                                                            @endforeach
                                                                        @endif
                                                                    </select>
                                                                    @break

                                                                @case($value['type'] == 'multiselect')
                                                                    <select id="{{ $key }}" class="custom-select block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                                            name="{{ $key }}[]" multiple>
                                                                        @foreach ($value['options'] as $option)
                                                                            <option value="{{ $option }}" {{ strpos($value['value'], $option) !== false ? 'selected' : '' }}>
                                                                                {{ __($option) }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    @break

                                                                @case($value['type'] == 'textarea')
                                                                    <textarea class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                                              name="{{ $key }}" rows="3">{{ $value['value'] }}</textarea>
                                                                    @break
                                                            @endswitch

                                                            @error($key)
                                                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                @endforeach

                                                <div class="mt-6 flex justify-end">
                                                    <button type="submit" class="rounded-md bg-accent-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 dark:hover:bg-accent-500">
                                                        {{ __('Save') }}
                                                    </button>
                                                    <button type="reset" class="ml-2 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-500">
                                                        Reset
                                                    </button>
                                                </div>
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            </form>
                                        </div>
                                    @endcanany
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        const tabPaneHash = window.location.hash;
        if (tabPaneHash) {
            $('.nav-pills a[href="' + tabPaneHash + '"]').tab('show');
        }

        $('.nav-pills a').click(function(e) {
            $(this).tab('show');
            window.location.hash = this.hash;
        });

        document.addEventListener('DOMContentLoaded', (event) => {
            $('.custom-select').select2({
                width: '100%',
            });
        })

        tinymce.init({
            selector: 'textarea',
            promotion: false,
            skin: "oxide-dark",
            content_css: "dark",
            branding: false,
            height: 500,
            width: '100%',
            plugins: ['image', 'link'],
        });
    </script>
@endsection