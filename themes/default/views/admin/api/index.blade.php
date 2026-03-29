@extends('layouts.main')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{__('Application API')}}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route('home')}}">{{__('Dashboard')}}</a></li>
                        <li class="breadcrumb-item"><a class="text-muted" href="{{route('admin.api.index')}}">{{__('Application API')}}</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-outline card-primary">
                <div class="card-body">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start mb-4">
                        <div class="pr-lg-3">
                            <span class="badge badge-info px-3 py-2 mb-3">
                                <i class="fas fa-shield-alt mr-1"></i>{{ __('Application Credentials') }}
                            </span>
                            <h2 class="h3 mb-2">{{ __('Control token access with clearer scope boundaries') }}</h2>
                            <p class="text-muted mb-3">{{ __('Create narrowly-scoped API credentials, review how they were last used, and keep older integrations isolated when legacy compatibility is still required.') }}</p>
                        </div>
                        <a href="{{route('admin.api.create')}}" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i>{{__('Create new')}}
                        </a>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="border rounded p-3 h-100">
                                <h3 class="h6 mb-2">{{ __('Scoped by resource') }}</h3>
                                <p class="text-muted mb-0">{{ __('Choose per-resource read or write access instead of maintaining a raw comma-separated scope string.') }}</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="border rounded p-3 h-100">
                                <h3 class="h6 mb-2">{{ __('Operational clarity') }}</h3>
                                <p class="text-muted mb-0">{{ __('Tokens stay easy to scan with labeled scopes, usage history, and stronger visual grouping across the page.') }}</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="border rounded p-3 h-100">
                                <h3 class="h6 mb-2">{{ __('Compatibility retained') }}</h3>
                                <p class="text-muted mb-0">{{ __('Legacy unscoped tokens still stand out clearly, so they can be rotated on your own schedule.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Issued credentials') }}</h3>
                    <div class="card-tools text-muted small">{{ __('Review every token, what it can do, and when it was last used.') }}</div>
                </div>

                <div class="card-body table-responsive">
                    <table id="datatable" class="table table-hover table-striped">
                        <thead>
                        <tr>
                            <th>{{__('Token')}}</th>
                            <th>{{__('Memo')}}</th>
                            <th>{{__('Scopes')}}</th>
                            <th>{{__('Last used')}}</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <script>
        function submitResult() {
            return confirm("{{__('Are you sure you wish to delete?')}}") !== false;
        }

        document.addEventListener("DOMContentLoaded", function () {
            $('#datatable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.3/i18n/{{ $locale_datatables }}.json'
                },
                processing: true,
                serverSide: true,
                stateSave: true,
                ajax: "{{route('admin.api.datatable')}}",
                order: [[ 3, "desc" ]],
                pageLength: 10,
                columns: [
                    {data: 'token'},
                    {data: 'memo'},
                    {data: 'scopes'},
                    {data: 'last_used'},
                    {data: 'actions' , sortable : false},
                ],
                fnDrawCallback: function() {
                    $('[data-toggle="popover"]').popover();
                }
            });
        });
    </script>
@endsection
