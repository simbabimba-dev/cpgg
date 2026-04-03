@extends('layouts.main')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-y-4 sm:flex-row sm:items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{__('Admin Overview')}}</h1>
        <ol class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
            <li>
                <a href="{{route('home')}}" class="hover:text-accent-500 dark:hover:text-accent-400">{{__('Dashboard')}}</a>
            </li>
            <li>/</li>
            <li>
                <a href="{{route('admin.overview.index')}}" class="text-gray-700 hover:text-accent-500 dark:text-gray-300 dark:hover:text-accent-400">{{__('Admin Overview')}}</a>
            </li>
        </ol>
    </div>

    @if(Storage::get('latestVersion') && config("app.version") < Storage::get('latestVersion'))
        <div class="mb-4 border-l-4 border-red-500 bg-red-100 p-4 text-red-700 dark:bg-red-900/50 dark:text-red-300" role="alert">
            <div class="font-bold">
                <i class="fas fa-shield-alt mr-2"></i> {{__("Version Outdated:")}}
            </div>
            <p>
                {{__("You are running on")}} v{{config("app.version")}}-{{config("BRANCHNAME")}}.
                {{__("The latest Version is")}} v{{Storage::get('latestVersion')}}
            </p>
            <a href="https://ctrlpanel.gg/docs/category/updating" class="underline hover:text-red-800 dark:hover:text-red-200">{{__("Consider updating now")}}</a>
        </div>
    @endif
    <section class="content">
        <div class="w-full">

            <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                <a href="https://ctrlpanel.gg/docs" class="flex items-center justify-center rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600">
                    <i class="mr-2 fas fa-link"></i> {{__('Documentation')}}
                </a>
                <a href="https://github.com/Ctrlpanel-gg/panel" class="flex items-center justify-center rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600">
                    <i class="mr-2 fab fa-github"></i> {{__('Github')}}
                </a>
                <a href="https://ctrlpanel.gg/docs/contributing/donating" class="flex items-center justify-center rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600">
                    <i class="mr-2 fas fa-money-bill"></i> {{__('Support CtrlPanel')}}
                </a>
            </div>

            <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="overflow-hidden rounded-lg bg-white shadow-md dark:bg-gray-800">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 rounded-md bg-accent-500 p-3">
                                <i class="fas fa-server text-white"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{__('Servers')}}
                                        <i class="ml-1 fas fa-info-circle cursor-help" data-toggle="popover"
                                           data-trigger="hover" data-placement="top"
                                           data-html="true"
                                           data-content="{{ __("This shows the total active servers and the total servers. Total active servers are all servers which are not suspended") }}"></i>
                                    </dt>
                                    <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                        {{$counters['servers']->active}}/{{$counters['servers']->total}}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-white shadow-md dark:bg-gray-800">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 rounded-md bg-accent-600 p-3">
                                <i class="fas fa-users text-white"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{__('Users')}}
                                        <i class="ml-1 fas fa-info-circle cursor-help" data-toggle="popover"
                                           data-trigger="hover" data-placement="top"
                                           data-html="true"
                                           data-content="{{ __("This shows the total active Users and the total Users. Total active Users are all Users which are not suspended") }}"></i>
                                    </dt>
                                    <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                        {{$counters['users']->active}}/{{$counters['users']->total}}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-white shadow-md dark:bg-gray-800">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 rounded-md bg-yellow-500 p-3">
                                <i class="fas fa-coins text-white"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{__('Total')}} {{ $credits_display_name }}
                                    </dt>
                                    <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                        {{$counters['credits']}}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-white shadow-md dark:bg-gray-800">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 rounded-md bg-green-500 p-3">
                                <i class="fas fa-money-bill text-white"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{__('Payments')}}
                                    </dt>
                                    <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                        {{$counters['payments']->total}}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                
                <div class="flex flex-col rounded-lg bg-white shadow-md dark:bg-gray-800">
                    <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            <i class="mr-2 fas fa-kiwi-bird text-gray-500 dark:text-gray-400"></i>{{__('Pterodactyl')}}
                        </h3>
                        <a href="{{route('admin.overview.sync')}}" class="inline-flex items-center rounded-md bg-accent-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-accent-500 dark:hover:bg-accent-500">
                            <i class="mr-2 fas fa-sync"></i>{{__('Sync')}}
                        </a>
                    </div>
                    <div class="flex-1 p-6">
                        @if ($deletedNodesPresent)
                            <div class="mb-4 border-l-4 border-red-500 bg-red-100 p-4 text-red-700 dark:bg-red-900/50 dark:text-red-300">
                                <h5 class="font-bold"><i class="icon fas fa-exclamation-circle mr-1"></i>{{ __('Warning!') }}</h5>
                                <p class="text-sm">
                                    {{ __('Some nodes got deleted on pterodactyl only. Please click the sync button above.') }}
                                </p>
                            </div>
                        @endif
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{__('Resources')}}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{__('Count')}}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                                    <tr>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{__('Locations')}}</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{$counters['locations']}}</td>
                                    </tr>
                                    <tr>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{__('Nodes')}}</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{$nodes->count()}}</td>
                                    </tr>
                                    <tr>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{__('Nests')}}</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{$counters['nests']}}</td>
                                    </tr>
                                    <tr>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{__('Eggs')}}</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{$counters['eggs']}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="border-t border-gray-200 bg-gray-50 px-6 py-3 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-700 dark:text-gray-400">
                        <i class="mr-2 fas fa-sync"></i>{{__('Last updated :date', ['date' => $syncLastUpdate])}}
                    </div>
                </div>

                <div class="flex flex-col rounded-lg bg-white shadow-md dark:bg-gray-800">
                    <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            <i class="mr-2 fas fa-ticket-alt text-gray-500 dark:text-gray-400"></i>{{__('Latest tickets')}}
                        </h3>
                    </div>
                    <div class="flex-1 p-6">
                        @if(!$tickets->count())
                            <span class="text-lg font-bold text-gray-700 dark:text-gray-300">{{__('There are no tickets')}}.</span>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{__('Title')}}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{__('User')}}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{__('Status')}}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{__('Last updated')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                                        @foreach($tickets as $ticket_id => $ticket)
                                            <tr>
                                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                                    <a class="text-accent-600 hover:underline dark:text-accent-400" href="{{route('admin.ticket.show', ['ticket_id' => $ticket_id])}}">
                                                        #{{$ticket_id}} - {{$ticket->title}}
                                                    </a>
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                                    <a href="{{route('admin.users.show', $ticket->user_id)}}" class="text-gray-700 hover:underline dark:text-gray-200">
                                                        {{$ticket->user}}
                                                    </a>
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{$ticket->statusBadgeColor}}">
                                                        {{$ticket->status}}
                                                    </span>
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                    {{$ticket->last_updated}}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex flex-col rounded-lg bg-white shadow-md dark:bg-gray-800">
                    <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            <i class="mr-2 fas fa-server text-gray-500 dark:text-gray-400"></i>{{__('CtrlPanel.gg')}}
                        </h3>
                    </div>
                    <div class="flex-1 p-6">
                        </div>
                    <div class="border-t border-gray-200 bg-gray-50 px-6 py-3 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-700 dark:text-gray-400">
                        <i class="mr-2 fas fa-info"></i>{{__("Version")}} {{config("app.version")}} - {{config("BRANCHNAME")}}
                    </div>
                </div>

            </div>

            <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="flex flex-col rounded-lg bg-white shadow-md dark:bg-gray-800">
                    <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            <i class="mr-2 fas fa-server text-gray-500 dark:text-gray-400"></i>{{__('Individual nodes')}}
                        </h3>
                    </div>
                    <div class="flex-1 p-6">
                        @if ($perPageLimit)
                            <div class="mb-4 border-l-4 border-red-500 bg-red-100 p-4 text-red-700 dark:bg-red-900/50 dark:text-red-300">
                                <h5 class="font-bold"><i class="icon fas fa-exclamation-circle mr-1"></i>{{ __('Error!') }}</h5>
                                <p class="mb-2 text-sm">
                                    {{ __('You reached the Pterodactyl perPage limit. Please make sure to set it higher than your server count.') }}<br>
                                    {{ __('You can do that in settings.') }}<br><br>
                                    {{ __('Note') }}: {{ __('If this error persists even after changing the limit, it might mean a server was deleted on Pterodactyl, but not on CtrlPanel. Try clicking the button below.') }}
                                </p>
                                <a href="{{route('admin.servers.sync')}}" class="inline-flex items-center rounded-md bg-accent-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-accent-500 dark:hover:bg-accent-500">
                                    <i class="mr-2 fas fa-sync"></i>{{__('Sync servers')}}
                                </a>
                            </div>
                        @endif

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{__('ID')}}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{__('Node')}}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{__('Server count')}}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{__('Resource usage')}}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{ $credits_display_name . ' ' . __('Usage') ." (".__('per month').")"}}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                                    @foreach($nodes as $nodeID => $node)
                                        <tr>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{$nodeID}}</td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{$node->name}}</td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{$node->activeServers}}/{{$node->totalServers}}</td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{$node->usagePercent}}%</td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{Currency::formatForDisplay($node->activeEarnings)}}/{{Currency::formatForDisplay($node->totalEarnings)}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <td class="whitespace-nowrap px-6 py-3 text-right text-sm font-bold text-gray-900 dark:text-white" colspan="2">{{__('Total')}} ({{__('active')}}/{{__('total')}}):</td>
                                        <td class="whitespace-nowrap px-6 py-3 text-sm font-medium text-gray-900 dark:text-white">{{$counters['servers']->active}}/{{$counters['servers']->total}}</td>
                                        <td class="whitespace-nowrap px-6 py-3 text-sm font-medium text-gray-900 dark:text-white">{{$counters['totalUsagePercent']}}%</td>
                                        <td class="whitespace-nowrap px-6 py-3 text-sm font-medium text-gray-900 dark:text-white">{{Currency::formatForDisplay($counters['earnings']->active)}}/{{Currency::formatForDisplay($counters['earnings']->total)}}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <hr class="my-4 border-gray-200 dark:border-gray-700">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div class="rounded-lg bg-white shadow-md dark:bg-gray-800">
                        <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                             <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                <i class="mr-2 fas fa-file-invoice-dollar text-gray-500 dark:text-gray-400"></i>{{__('Latest payments')}}
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
                                @if($counters['payments']['lastMonth']->count())
                                    <div class="border-b border-gray-200 pb-6 xl:border-b-0 xl:border-r xl:pb-0 xl:pr-6 dark:border-gray-700">
                                        <div class="mb-4 text-center text-lg font-bold text-gray-800 dark:text-gray-200">
                                            {{__('Last month')}}:
                                            <i data-toggle="popover" data-trigger="hover" data-html="true"
                                               data-content="{{ __('Payments in this time window') }}:<br>{{$counters['payments']['lastMonth']->timeStart}} - {{$counters['payments']['lastMonth']->timeEnd}}"
                                               class="fas fa-info-circle cursor-help text-gray-400"></i>
                                        </div>
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                <thead class="bg-gray-50 dark:bg-gray-700">
                                                    <tr>
                                                        <th class="px-4 py-2 text-left text-xs font-bold uppercase text-gray-500 dark:text-gray-300">{{__('Currency')}}</th>
                                                        <th class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-300">{{__('Number of payments')}}</th>
                                                        <th class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-300">{{__('Total amount')}}</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                                    @foreach($counters['payments']['lastMonth'] as $currency => $income)
                                                        <tr>
                                                            <td class="px-4 py-2 text-sm dark:text-gray-200">{{$currency}}</td>
                                                            <td class="px-4 py-2 text-sm dark:text-gray-200">{{$income->count}}</td>
                                                            <td class="px-4 py-2 text-sm dark:text-gray-200">{{Currency::formatForDisplay($income->total)}}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif

                                <div>
                                    <div class="mb-4 text-center text-lg font-bold text-gray-800 dark:text-gray-200">
                                        {{__('This month')}}:
                                        <i data-toggle="popover" data-trigger="hover" data-html="true"
                                           data-content="{{ __('Payments in this time window') }}:<br>{{$counters['payments']['thisMonth']->timeStart}} - {{$counters['payments']['thisMonth']->timeEnd}}"
                                           class="fas fa-info-circle cursor-help text-gray-400"></i>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead class="bg-gray-50 dark:bg-gray-700">
                                                <tr>
                                                    <th class="px-4 py-2 text-left text-xs font-bold uppercase text-gray-500 dark:text-gray-300">{{__('Currency')}}</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-300">{{__('Number of payments')}}</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-300">{{__('Total amount')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                                @foreach($counters['payments']['thisMonth'] as $currency => $income)
                                                    <tr>
                                                        <td class="px-4 py-2 text-sm dark:text-gray-200">{{$currency}}</td>
                                                        <td class="px-4 py-2 text-sm dark:text-gray-200">{{$income->count}}</td>
                                                        <td class="px-4 py-2 text-sm dark:text-gray-200">{{Currency::formatForDisplay($income->total)}}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg bg-white shadow-md dark:bg-gray-800">
                         <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                             <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                <i class="mr-2 fas fa-hand-holding-usd text-gray-500 dark:text-gray-400"></i>{{__('Tax overview')}}
                            </h3>
                        </div>
                        <div class="p-6">
                            @if($counters['taxPayments']['lastYear']->count())
                                <div class="mb-4 text-center text-lg font-bold text-gray-800 dark:text-gray-200">
                                    {{__('Last year')}}:
                                    <i data-toggle="popover" data-trigger="hover" data-html="true"
                                       data-content="{{ __('Payments in this time window') }}:<br>{{$counters['taxPayments']['lastYear']->timeStart}} - {{$counters['taxPayments']['lastYear']->timeEnd}}"
                                       class="fas fa-info-circle cursor-help text-gray-400"></i>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-bold uppercase text-gray-500 dark:text-gray-300">{{__('Currency')}}</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-300">{{__('Number of payments')}}</th>
                                                <th class="px-4 py-2 text-left text-xs font-bold uppercase text-gray-500 dark:text-gray-300">{{__('Base amount')}}</th>
                                                <th class="px-4 py-2 text-left text-xs font-bold uppercase text-gray-500 dark:text-gray-300">{{__('Total taxes')}}</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-300">{{__('Total amount')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($counters['taxPayments']['lastYear'] as $currency => $income)
                                                <tr>
                                                    <td class="px-4 py-2 text-sm dark:text-gray-200">{{$currency}}</td>
                                                    <td class="px-4 py-2 text-sm dark:text-gray-200">{{$income->count}}</td>
                                                    <td class="px-4 py-2 text-sm dark:text-gray-200">{{Currency::formatForDisplay($income->price)}}</td>
                                                    <td class="px-4 py-2 text-sm dark:text-gray-200">{{Currency::formatForDisplay($income->taxes)}}</td>
                                                    <td class="px-4 py-2 text-sm dark:text-gray-200">{{Currency::formatForDisplay($income->total)}}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <hr class="my-6 border-gray-200 dark:border-gray-700">
                            @endif

                            <div class="mb-4 text-center text-lg font-bold text-gray-800 dark:text-gray-200">
                                {{__('This year')}}:
                                <i data-toggle="popover" data-trigger="hover" data-html="true"
                                   data-content="{{ __('Payments in this time window') }}:<br>{{$counters['taxPayments']['thisYear']->timeStart}} - {{$counters['taxPayments']['thisYear']->timeEnd}}"
                                   class="fas fa-info-circle cursor-help text-gray-400"></i>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-bold uppercase text-gray-500 dark:text-gray-300">{{__('Currency')}}</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-300">{{__('Number of payments')}}</th>
                                            <th class="px-4 py-2 text-left text-xs font-bold uppercase text-gray-500 dark:text-gray-300">{{__('Base amount')}}</th>
                                            <th class="px-4 py-2 text-left text-xs font-bold uppercase text-gray-500 dark:text-gray-300">{{__('Total taxes')}}</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-300">{{__('Total amount')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($counters['taxPayments']['thisYear'] as $currency => $income)
                                            <tr>
                                                <td class="px-4 py-2 text-sm dark:text-gray-200">{{$currency}}</td>
                                                <td class="px-4 py-2 text-sm dark:text-gray-200">{{$income->count}}</td>
                                                <td class="px-4 py-2 text-sm dark:text-gray-200">{{Currency::formatForDisplay($income->price)}}</td>
                                                <td class="px-4 py-2 text-sm dark:text-gray-200">{{Currency::formatForDisplay($income->taxes)}}</td>
                                                <td class="px-4 py-2 text-sm dark:text-gray-200">{{Currency::formatForDisplay($income->total)}}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <hr class="my-4 border-gray-200 dark:border-gray-700">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection