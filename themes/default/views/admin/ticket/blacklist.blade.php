@extends('layouts.main')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-y-4 sm:flex-row sm:items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('Ticket Blacklist') }}</h1>
        <ol class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
            <li>
                <a href="{{ route('home') }}" class="hover:text-accent-600 dark:hover:text-accent-400">{{ __('Dashboard') }}</a>
            </li>
            <li>/</li>
            <li>
                <a href="{{ route('admin.ticket.blacklist') }}" class="text-gray-700 hover:text-accent-500 dark:text-gray-300 dark:hover:text-accent-400">{{ __('Ticket Blacklist') }}</a>
            </li>
        </ol>
    </div>
    

    <section class="content">
        <div class="w-full">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden dark:bg-gray-800">
                        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center dark:border-gray-700">
                            <h5 class="text-lg font-medium text-gray-800 dark:text-white">
                                <i class="mr-2 fas fa-users text-gray-500 dark:text-gray-400"></i>{{__('Blacklist List')}}
                            </h5>
                        </div>
                        <div class="p-6">
                            <div class="overflow-x-auto">
                                <table id="datatable" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{__('User')}}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{__('Status')}}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{__('Reason')}}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{__('Created At')}}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{__('Actions')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden dark:bg-gray-800">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h5 class="text-lg font-medium text-gray-800 dark:text-white">
                                {{__('Add To Blacklist')}}
                                <i data-toggle="popover" data-trigger="hover" data-content="{{__('please make the best of it')}}" class="fas fa-info-circle ml-1 text-gray-400 cursor-help dark:text-gray-500"></i>
                            </h5>
                        </div>
                        <div class="p-6">
                            <form action="{{route('admin.ticket.blacklist.add')}}" method="POST" class="ticket-form">
                                @csrf
                                <div class="mb-4">
                                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">
                                        {{ __('User') }}:
                                        <i data-toggle="popover" data-trigger="hover" data-content="{{ __('Please note, the blacklist will make the user unable to make a ticket/reply again') }}" class="fas fa-info-circle ml-1 text-gray-400 cursor-help dark:text-gray-500"></i>
                                    </label>
                                    <select id="user_id" style="width:100%" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('user_id') border-red-500 @enderror" name="user_id" required autocomplete="off">
                                    </select>
                                    @error('user_id')
                                    <p class="mt-1 text-xs text-red-500">{{$message}}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{__("Reason")}}</label>
                                    <input id="reason" type="text" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400" name="reason" placeholder="Input Some Reason" required>
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit" class="ticket-once inline-flex items-center rounded-md bg-accent-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 dark:hover:bg-accent-500">
                                        {{__('Submit')}}
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
        document.addEventListener("DOMContentLoaded", function () {
            $('#datatable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.3/i18n/{{ $locale_datatables }}.json'
                },
                processing: true,
                serverSide: true,
                stateSave: true,
                ajax: "{{route('admin.ticket.blacklist.datatable')}}",
                columns: [
                    {data: 'user' , name : 'user.name'},
                    {data: 'status'},
                    {data: 'reason'},
                    {data: 'created_at'},
                    {data: 'actions', sortable: false},
                ],
                fnDrawCallback: function( oSettings ) {
                    $('[data-toggle="popover"]').popover();
                }
            });
        });
    </script>

    <script type="application/javascript">
        function initUserIdSelect(data) {
            function escapeHtml(str) {
                var div = document.createElement('div');
                div.appendChild(document.createTextNode(str));
                return div.innerHTML;
            }

            $('#user_id').select2({
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
                escapeMarkup: function (markup) { return markup; },
                minimumInputLength: 2,
                templateResult: function (data) {
                    if (data.loading) return escapeHtml(data.text);

                    return '<div class="flex items-center gap-3"> \
                        <img class="h-8 w-8 rounded-full border border-gray-200" src="' + escapeHtml(data.avatarUrl) + '?s=120" alt="User Image"> \
                        <div class="flex flex-col"> \
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">' + escapeHtml(data.name) +'</span> \
                            <span class="text-xs text-gray-500 dark:text-gray-400"><strong>' + escapeHtml(data.email) + '</strong></span> \
                        </div> \
                    </div>';
                },
                templateSelection: function (data) {
                    return '<div> \
                        <span class="flex items-center gap-2"> \
                            <img class="h-5 w-5 rounded-full" src="' + escapeHtml(data.avatarUrl) + '?s=120" alt="User Image"> \
                            <span>' + escapeHtml(data.name) + ' (<strong>' + escapeHtml(data.email) + '</strong>)</span> \
                        </span> \
                    </div>';
                }
            });
        }

        $(document).ready(function() {
            @if (old('user_id'))
            $.ajax({
                url: '/admin/users.json?user_id={{ old('user_id') }}',
                dataType: 'json',
            }).then(function (data) {
                initUserIdSelect([ data ]);
            });
            @else
            initUserIdSelect();
            @endif
        });
    </script>
@endsection