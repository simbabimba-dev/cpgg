@extends('layouts.main')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-y-4 sm:flex-row sm:items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{__('Coupons')}}</h1>
        <ol class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
            <li>
                <a href="{{route('home')}}" class="hover:text-accent-500 dark:hover:text-accent-400">{{__('Dashboard')}}</a>
            </li>
            <li>/</li>
            <li>
                <a href="{{route('admin.coupons.index')}}" class="text-gray-700 hover:text-accent-500 dark:text-gray-300 dark:hover:text-accent-400">{{__('Coupons')}}</a>
            </li>
        </ol>
    </div>
    <section class="content">
        <div class="w-full">
            <div class="bg-white rounded-lg shadow-md overflow-hidden dark:bg-gray-800">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center dark:border-gray-700">
                    <h5 class="text-lg font-medium text-gray-800 dark:text-white">
                        <i class="fas fa-ticket-alt mr-2 text-gray-500 dark:text-gray-400"></i>
                        {{__('Coupons')}}
                    </h5>
                    <a href="{{route('admin.coupons.create')}}" class="inline-flex items-center rounded-md bg-accent-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 dark:hover:bg-accent-500">
                        <i class="fas fa-plus mr-1"></i>
                        {{__('Create new')}}
                    </a>
                </div>

                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table id="datatable" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{__('Status')}}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{__('Code')}}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{__('Value')}}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{__('Used / Max Uses')}}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{__('Expires')}}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{__('Created At')}}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{__('Actions')}}</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            {{-- DataTables will fill this --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        function submitResult() {
            return confirm("{{__('Are you sure you wish to delete?')}}") !== false;
        }

        $(document).ready(function() {
            $('#datatable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.3/i18n/{{ $locale_datatables }}.json'
                },
                processing: true,
                serverSide: true,
                stateSave: true,
                ajax: "{{route('admin.coupons.datatable')}}",
                columns: [
                    {data: 'status'},
                    {data: 'code'},
                    {data: 'value'},
                    {data: 'uses', sortable: false},
                    {data: 'expires_at'},
                    {data: 'created_at'},
                    {data: 'actions', sortable: false},
                ],
                fnDrawCallback: function( oSettings ) {
                    // Note: Popovers rely on Bootstrap JS. If removed, these won't work without a replacement like Tippy.js
                    $('[data-toggle="popover"]').popover();
                }
            });
        })
    </script>
@endsection