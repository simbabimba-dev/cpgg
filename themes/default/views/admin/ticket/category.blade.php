@extends('layouts.main')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-y-4 sm:flex-row sm:items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('Ticket Categories') }}</h1>
        <ol class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
            <li>
                <a href="{{ route('home') }}" class="hover:text-accent-600 dark:hover:text-accent-400">{{ __('Dashboard') }}</a>
            </li>
            <li>/</li>
            <li>
                <a href="{{ route('admin.ticket.category.index') }}" class="text-gray-700 hover:text-accent-600 dark:text-gray-300 dark:hover:text-accent-400">{{ __('Ticket Categories') }}</a>
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
                                <i class="fas fa-users mr-2 text-gray-500 dark:text-gray-400"></i>{{__('Categories')}}
                            </h5>
                        </div>
                        <div class="p-6">
                            <div class="overflow-x-auto">
                                <table id="datatable" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{__('ID')}}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{__('Name')}}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{__('Tickets')}}</th>
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

                <div class="lg:col-span-1 space-y-6">
                    
                    <div class="bg-white rounded-lg shadow-md overflow-hidden dark:bg-gray-800">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h5 class="text-lg font-medium text-gray-800 dark:text-white">{{__('Add Category')}}</h5>
                        </div>
                        <div class="p-6">
                            <form action="{{route('admin.ticket.category.store')}}" method="POST" class="ticket-form">
                                @csrf
                                <div class="mb-4">
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{__("Name")}}</label>
                                    <input id="name" type="text" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400" name="name" required>
                                </div>
                                <div class="flex justify-end">
                                    <button type="submit" class="inline-flex items-center rounded-md bg-accent-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 dark:hover:bg-accent-500">
                                        {{__('Submit')}}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md overflow-hidden dark:bg-gray-800">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h5 class="text-lg font-medium text-gray-800 dark:text-white">{{__('Edit Category')}}</h5>
                        </div>
                        <div class="p-6">
                            <form action="{{route('admin.ticket.category.update', '1')}}" method="POST" class="ticket-form">
                                @csrf
                                @method('PATCH')
                                <div class="mb-4">
                                    <select id="category" style="width:100%" class="custom-select block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('category') border-red-500 @enderror" name="category" required autocomplete="off">
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ __($category->name) }}</option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{__("New Name")}}</label>
                                    <input id="name" type="text" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400" name="name" required>
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit" class="inline-flex items-center rounded-md bg-accent-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 dark:hover:bg-accent-500">
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
                    url: '//cdn.datatables.net/plug-ins/1.11.3/i18n/{{config("SETTINGS::LOCALE:DATATABLES")}}.json'
                },
                processing: true,
                serverSide: true,
                stateSave: true,
                ajax: "{{route('admin.ticket.category.datatable')}}",
                columns: [
                    {data: 'id'},
                    {data: 'name'},
                    {data: 'tickets'},
                    {data: 'created_at', sortable: false},
                    {data: 'actions', sortable: false},
                ],
                fnDrawCallback: function( oSettings ) {
                    $('[data-toggle="popover"]').popover();
                }
            });
        });

        document.addEventListener('DOMContentLoaded', (event) => {
            $('.custom-select').select2();
        })
    </script>
@endsection