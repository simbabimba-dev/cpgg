@extends('layouts.main')

@section('content')
    <!-- CONTENT HEADER -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{__('Webhooks')}}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route('home')}}">{{__('Dashboard')}}</a></li>
                        <li class="breadcrumb-item"><a class="text-muted"
                                                       href="{{route('admin.webhooks.index')}}">{{__('Webhooks')}}</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <!-- END CONTENT HEADER -->

    <!-- MAIN CONTENT -->
    <section class="content">
        <div class="container-fluid">

            <div class="card">

                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h5 class="card-title"><i class="fas fa-plug mr-2"></i>{{__('Webhooks')}}</h5>
                        <a href="{{route('admin.webhooks.create')}}" class="btn btn-sm btn-primary"><i
                                class="fas fa-plus mr-1"></i>{{__('Create new')}}</a>
                    </div>
                </div>

                <div class="card-body table-responsive">
                    <table id="datatable" class="table table-striped">
                        <thead>
                        <tr>
                            <th>{{__('Name')}}</th>
                            <th>{{__('Payload URL')}}</th>
                            <th>{{__('Events')}}</th>
                            <th>{{__('Status')}}</th>
                            <th>{{__('Last triggered')}}</th>
                            <th>{{__('Created at')}}</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
        <!-- END CUSTOM CONTENT -->

    </section>
    <!-- END CONTENT -->

    <script>
        function submitResult(form) {
            Swal.fire({
                title: '{{ __('Are you sure?') }}',
                text: '{{ __('This action will permanently delete the webhook') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __('Yes, delete it!') }}',
                cancelButtonText: '{{ __('Cancel') }}',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });

            return false;
        }

        document.addEventListener("DOMContentLoaded", function () {
            $('#datatable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.3/i18n/{{ $locale_datatables }}.json'
                },
                processing: true,
                serverSide: true,
                stateSave: true,
                ajax: "{{route('admin.webhooks.datatable')}}",
                order: [[ 0, "asc" ]],
                columns: [
                    {data: 'name'},
                    {data: 'url'},
                    {data: 'events', sortable: false},
                    {data: 'enabled', sortable: false},
                    {data: 'last_triggered_at', sortable: false},
                    {data: 'created_at'},
                    {data: 'actions', sortable: false},
                ],
                fnDrawCallback: function (oSettings) {
                    $('[data-toggle="popover"]').popover();
                }
            });
        });
    </script>

@endsection