@extends('layouts.main')

@section('content')
    <!-- CONTENT HEADER -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ __('Ticket') }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a class="text-muted"
                                                       href="{{ route('ticket.index') }}">{{ __('Ticket') }}</a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <!-- END CONTENT HEADER -->

    <!-- MAIN CONTENT -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between">
                                <h5 class="card-title"><i class="fas fa-ticket-alt mr-2"></i>{{__('My Ticket')}}</h5>
                                <a href="{{route('ticket.new')}}" class="inline-flex items-center gap-1 px-3 py-2 rounded-md text-sm font-medium bg-accent-600 text-white hover:bg-accent-700 shadow-sm @cannot("user.ticket.write")) opacity-50 cursor-not-allowed @endcannot">
                                    <i class="fas fa-plus mr-1"></i>{{__('New Ticket')}}</a>
                            </div>
                        </div>
                        <div class="card-body table-responsive">

                            <table id="datatable" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{__('Category')}}</th>
                                    <th>{{__('Title')}}</th>
                                    <th>{{__('Priority')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Last Updated')}}</th>
                                    <th>{{__('Actions')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">{{__('Ticket Information')}}
                                <!--
                                <i data-toggle="popover"
                                data-trigger="hover"
                                data-content="{{__('please make the best of it')}}"
                                class="fas fa-info-circle"></i></h5>
                                -->

                        </div>
                        <div class="card-body">
                            <p>{!! $ticketsettings->information !!}</p>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- END CONTENT -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            $('#datatable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.3/i18n/{{ $locale_datatables }}.json'
                },
                processing: true,
                serverSide: true,
                stateSave: true,
                ajax: "{{route('ticket.datatable')}}",
                order: [[ 4, "desc" ]],
                columns: [
                    {data: 'category'},
                    {data: 'title'},
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

