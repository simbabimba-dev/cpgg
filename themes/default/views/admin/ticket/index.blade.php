@extends('layouts.main')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-y-4 sm:flex-row sm:items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('Ticket') }}</h1>
        <ol class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
            <li>
                <a href="{{ route('home') }}" class="hover:text-accent-600 dark:hover:text-accent-400">{{ __('Dashboard') }}</a>
            </li>
            <li>/</li>
            <li>
                <a href="{{ route('admin.ticket.index') }}" class="text-gray-700 hover:text-accent-600 dark:text-gray-300 dark:hover:text-accent-400">{{ __('Ticket List') }}</a>
            </li>
        </ol>
    </div>
    <section class="content">
        <div class="w-full">
            <div class="bg-white rounded-lg shadow-md overflow-hidden dark:bg-gray-800">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center dark:border-gray-700">
                    <h5 class="text-lg font-medium text-gray-800 dark:text-white">
                        <i class="mr-2 fas fa-ticket-alt text-gray-500 dark:text-gray-400"></i>{{ __('Ticket List') }}
                    </h5>
                    <a href="{{ route("admin.ticket.category.index") }}" class="inline-flex items-center rounded-md bg-accent-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 dark:hover:bg-accent-500">
                        <span class="mr-1">+</span> {{ __("Add Category") }}
                    </a>
                </div>

                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table id="datatable" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Category') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Title') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('User') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Priority') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Status') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Last Updated') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Actions') }}</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            </tbody>
                        </table>
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
                ajax: "{{ route('admin.ticket.datatable') }}",
                order: [[ 4, "desc" ]],
                columns: [
                    {data: 'category'},
                    {data: 'title'},
                    {data: 'user_id'},
                    {data: 'priority'},
                    {data: 'status'},
                    {data: 'updated_at', type: 'num', render: {_: 'display', sort: 'raw'}},
                    {data: 'actions', sortable: false},
                ],
                fnDrawCallback: function( oSettings ) {
                    $('[data-toggle="popover"]').popover();
                }
            });
        });
    </script>
@endsection