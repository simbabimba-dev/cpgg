@extends('layouts.main')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-y-4 sm:flex-row sm:items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{__('Partner')}}</h1>
        <ol class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
            <li>
                <a href="{{route('home')}}" class="hover:text-accent-500 dark:hover:text-accent-400">{{__('Dashboard')}}</a>
            </li>
            <li>/</li>
            <li>
                <a href="{{route('admin.partners.index')}}" class="hover:text-accent-500 dark:hover:text-accent-400">{{__('Partner')}}</a>
            </li>
            <li>/</li>
            <li>
                <span class="text-gray-700 dark:text-gray-300">{{__('Create')}}</span>
            </li>
        </ol>
    </div>
    <section class="content">
        <div class="w-full">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="col-span-1">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden dark:bg-gray-800">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h5 class="text-lg font-medium text-gray-800 dark:text-white">
                                <i class="fas fa-handshake mr-2 text-gray-500 dark:text-gray-400"></i>{{__('Partner details')}}
                            </h5>
                        </div>
                        <div class="p-6">
                            <form action="{{route('admin.partners.store')}}" method="POST">
                                @csrf

                                <div class="mb-4">
                                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{ __('User') }}:</label>
                                    <select id="user_id" style="width:100%" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('user_id') border-red-500 @enderror" name="user_id" required autocomplete="off">
                                    </select>
                                    @error('user_id')
                                    <p class="mt-1 text-xs text-red-500">{{$message}}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="partner_discount" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{__('Partner discount')}}
                                        <i data-toggle="popover" data-trigger="hover"
                                           data-content="{{__('The discount in percent given to the partner when purchasing credits.')}}"
                                           class="fas fa-info-circle text-gray-400 ml-1 cursor-help dark:text-gray-500"></i>
                                    </label>
                                    <input value="{{old('partner_discount')}}" placeholder="{{__('Discount in percent')}}" id="partner_discount" name="partner_discount"
                                           type="number" step="any" min="0" max="100"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('partner_discount') border-red-500 @enderror">
                                    @error('partner_discount')
                                    <p class="mt-1 text-xs text-red-500">{{$message}}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="registered_user_discount" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{__('Registered user discount')}}
                                        <i data-toggle="popover" data-trigger="hover"
                                           data-content="{{__('The discount in percent given to all users registered using the partners referral link when purchasing credits.')}}"
                                           class="fas fa-info-circle text-gray-400 ml-1 cursor-help dark:text-gray-500"></i>
                                    </label>
                                    <div class="flex rounded-md shadow-sm">
                                        <input value="{{old('registered_user_discount')}}" placeholder="Discount in percent" id="registered_user_discount" name="registered_user_discount"
                                               type="number" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('registered_user_discount') border-red-500 @enderror"
                                               required="required">
                                    </div>
                                    @error('registered_user_discount')
                                    <p class="mt-1 text-xs text-red-500">{{$message}}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="referral_system_commission" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">{{__('Referral system commission')}}
                                        <i data-toggle="popover" data-trigger="hover"
                                           data-content="{{__('Override value for referral system commission. You can set it to -1 to get the default commission from settings.')}}"
                                           class="fas fa-info-circle text-gray-400 ml-1 cursor-help dark:text-gray-500"></i>
                                    </label>
                                    <input value="{{old('referral_system_commission')}}" placeholder="{{__('Commission in percent')}}" id="referral_system_commission" name="referral_system_commission"
                                           type="number" step="any" min="-1" max="100"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('referral_system_commission') border-red-500 @enderror">
                                    @error('referral_system_commission')
                                    <p class="mt-1 text-xs text-red-500">{{$message}}</p>
                                    @enderror
                                </div>

                                <div class="flex justify-end mt-6">
                                    <button type="submit" class="rounded-md bg-accent-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 dark:hover:bg-accent-500">
                                        {{__('Submit')}}
                                    </button>
                                </div>

                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            $('#expires_at').datetimepicker({
                format: 'DD-MM-yyyy HH:mm:ss',
                icons: {
                    time: 'far fa-clock',
                    date: 'far fa-calendar',
                    up: 'fas fa-arrow-up',
                    down: 'fas fa-arrow-down',
                    previous: 'fas fa-chevron-left',
                    next: 'fas fa-chevron-right',
                    today: 'fas fa-calendar-check',
                    clear: 'far fa-trash-alt',
                    close: 'far fa-times-circle'
                }
            });
        })
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
                            filter: { name: params.term },
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

                    return '<div class="user-block"> \
                        <img class="img-circle img-bordered-xs" src="' + escapeHtml(data.avatarUrl) + '?s=120" alt="User Image"> \
                        <span class="username"> \
                            <a href="#">' + escapeHtml(data.name) +'</a> \
                        </span> \
                        <span class="description"><strong>' + escapeHtml(data.email) + '</strong>' + '</span> \
                    </div>';
                },
                templateSelection: function (data) {
                    return '<div> \
                        <span> \
                            <img class="img-rounded img-bordered-xs" src="' + escapeHtml(data.avatarUrl) + '?s=120" style="height:28px;margin-top:-4px;" alt="User Image"> \
                        </span> \
                        <span style="padding-left:5px;"> \
                            ' + escapeHtml(data.name) + ' (<strong>' + escapeHtml(data.email) + '</strong>) \
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