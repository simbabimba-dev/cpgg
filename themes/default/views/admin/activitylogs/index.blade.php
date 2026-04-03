@extends('layouts.main')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-y-4 sm:flex-row sm:items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('Activity Logs')}}</h1>
        <ol class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
            <li>
                <a href="{{route('home')}}" class="hover:text-accent-500 dark:hover:text-accent-400">{{ __('Dashboard')}}</a>
            </li>
            <li>/</li>
            <li>
                <a href="{{route('admin.activitylogs.index')}}" class="text-gray-700 hover:text-accent-500 dark:text-gray-300 dark:hover:text-accent-400">{{ __('Activity Logs')}}</a>
            </li>
        </ol>
    </div>

    <section class="content">
        <div class="w-full">
            <div class="mb-6 grid grid-cols-1 lg:grid-cols-3">
                <div class="col-span-1">
                    @if($cronlogs)
                        <div class="border-l-4 border-green-500 bg-green-100 p-4 text-green-700 dark:bg-green-900/50 dark:text-green-300">
                            <h4 class="font-semibold">{{$cronlogs}}</h4>
                        </div>
                    @else
                        <div class="border-l-4 border-red-500 bg-red-100 p-4 text-red-700 dark:bg-red-900/50 dark:text-red-300">
                            <h4 class="font-semibold">{{ __('No recent activity from cronjobs')}}</h4>
                            <p class="mt-1 text-sm">
                                {{ __('Are cronjobs running?')}}
                                <a class="font-bold underline hover:text-red-900 dark:hover:text-red-200" target="_blank" href="https://CtrlPanel.gg/docs/Installation/getting-started#crontab-configuration">
                                    {{ __('Check the docs for it here')}}
                                </a>
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="overflow-hidden rounded-lg bg-white shadow-md dark:bg-gray-800">
                <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                    <h5 class="text-lg font-medium text-gray-800 dark:text-white">
                        <i class="fas fa-history mr-2 text-gray-500 dark:text-gray-400"></i>{{ __('Activity Logs')}}
                    </h5>
                </div>

                <div class="p-6">
                    <div class="mb-4 flex justify-end">
                        <div class="w-full md:w-1/3 lg:w-1/4">
                            <form method="get" action="{{route('admin.activitylogs.index')}}" class="flex gap-2">
                                @csrf
                                <input type="text"
                                       class="w-full rounded-md border border-gray-300 px-3 py-1.5 text-sm focus:border-accent-500 focus:outline-none focus:ring-1 focus:ring-accent-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                                       value="{{ request()->input('search') }}"
                                       name="search"
                                       placeholder="Search">
                                <button class="rounded-md bg-gray-100 px-3 py-1.5 text-gray-600 hover:bg-gray-200 hover:text-gray-800 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 dark:hover:text-white" type="submit">
                                    <i class="fa fa-search"></i>
                                </button>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            </form>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{ __('Causer') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{ __('Description') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{ __('Created at') }}</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                            @foreach($logs as $log)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 dark:text-gray-200">
                                        @if($log->causer)
                                            <a href='/admin/users/{{$log->causer_id}}' class="text-accent-600 hover:underline dark:text-accent-400">{{json_decode($log->causer)->name}}</a>
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400">System</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">
                                        <div class="flex flex-col gap-1">
                                            <span class="flex items-center font-medium">
                                                @if (str_starts_with($log->description, 'created'))
                                                    <i class="fas fa-plus mr-2 text-green-500 dark:text-green-400"></i>
                                                @elseif(str_starts_with($log->description, 'redeemed'))
                                                    <i class="fas fa-money-check-alt mr-2 text-green-500 dark:text-green-400"></i>
                                                @elseif(str_starts_with($log->description, 'deleted'))
                                                    <i class="fas fa-times mr-2 text-red-500 dark:text-red-400"></i>
                                                @elseif(str_starts_with($log->description, 'gained'))
                                                    <i class="fas fa-money-bill mr-2 text-green-500 dark:text-green-400"></i>
                                                @elseif(str_starts_with($log->description, 'updated'))
                                                    <i class="fas fa-pen mr-2 text-accent-500 dark:text-accent-400"></i>
                                                @endif
                                                {{ explode('\\', $log->subject_type)[2] ?? '' }} {{ ucfirst($log->description) }}
                                            </span>

                                            @php
                                                $properties = json_decode($log->properties, true);
                                            @endphp

                                            {{-- Handle Created Entries --}}
                                            @if ($log->description === 'created' && isset($properties['attributes']))
                                                <ul class="list-inside list-disc pl-4 text-xs text-gray-600 dark:text-gray-400">
                                                    @foreach ($properties['attributes'] as $attribute => $value)
                                                        @if (!is_null($value))
                                                            <li>
                                                                <strong class="text-gray-800 dark:text-gray-300">{{ ucfirst($attribute) }}:</strong>
                                                                {{ $attribute === 'created_at' || $attribute === 'updated_at' ? \Carbon\Carbon::parse($value)->toDayDateTimeString() : $value }}
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            @endif

                                            {{-- Handle Updated Entries --}}
                                            @if ($log->description === 'updated' && isset($properties['attributes'], $properties['old']))
                                                <ul class="list-inside list-disc pl-4 text-xs text-gray-600 dark:text-gray-400">
                                                    @foreach ($properties['attributes'] as $attribute => $newValue)
                                                        @if (array_key_exists($attribute, $properties['old']) && !is_null($newValue))
                                                            <li>
                                                                <strong class="text-gray-800 dark:text-gray-300">{{ ucfirst($attribute) }}:</strong>
                                                                {{ $attribute === 'created_at' || $attribute === 'updated_at' ?
                                                                    \Carbon\Carbon::parse($properties['old'][$attribute])->toDayDateTimeString() . ' → ' . \Carbon\Carbon::parse($newValue)->toDayDateTimeString()
                                                                    : $properties['old'][$attribute] . ' → ' . $newValue }}
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            @endif

                                            {{-- Handle Deleted Entries --}}
                                            @if ($log->description === 'deleted' && isset($properties['old']))
                                                <ul class="list-inside list-disc pl-4 text-xs text-gray-600 dark:text-gray-400">
                                                    @foreach ($properties['old'] as $attribute => $value)
                                                        @if (!is_null($value))
                                                            <li>
                                                                <strong class="text-gray-800 dark:text-gray-300">{{ ucfirst($attribute) }}:</strong>
                                                                {{ $attribute === 'created_at' || $attribute === 'updated_at' ? \Carbon\Carbon::parse($value)->toDayDateTimeString() : $value }}
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{$log->created_at->diffForHumans()}}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex justify-end">
                        {!! $logs->links() !!}
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection