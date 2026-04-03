@extends('layouts.main')

@section('content')
    <!-- CONTENT HEADER -->
    <div class="px-6 pt-6 pb-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">{{ __('Servers') }}</h1>
                <nav class="flex gap-2 mt-2 text-sm text-gray-600 dark:text-gray-400">
                    <a href="{{ route('home') }}" class="hover:text-accent-500 transition-colors">{{ __('Dashboard') }}</a>
                    <span>/</span>
                    <span class="text-gray-900 dark:text-white font-semibold">{{ __('Servers') }}</span>
                </nav>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <section class="content px-6 pb-6">
        <!-- ACTION BUTTONS -->
        <div class="flex flex-col md:flex-row gap-3 mb-6">
            <a
                @if (Auth::user()->Servers->count() >= Auth::user()->server_limit) onclick="return false" class="opacity-50 cursor-not-allowed" title="Server limit reached!" @else href="{{ route('servers.create') }}" @endcan
               @cannot('user.server.create') onclick="return false" class="opacity-50 cursor-not-allowed" title="No Permission!" @endcannot
                class="inline-flex items-center gap-2 px-6 py-2 bg-gradient-to-r from-accent-600 to-accent-500 hover:from-accent-500 hover:to-accent-600 text-white font-semibold rounded-lg transition-all duration-200 @if (Auth::user()->Servers->count() >= Auth::user()->server_limit || !Auth::user()->can('user.server.create')) opacity-50 cursor-not-allowed @endif">
                <i class="fa fa-plus"></i>
                {{ __('Create Server') }}
            </a>
            @if (Auth::user()->Servers->count() > 0 && !empty($phpmyadmin_url))
                <a href="{{ $phpmyadmin_url }}" target="_blank"
                    class="inline-flex items-center gap-2 px-6 py-2 bg-gradient-to-r from-accent-600 to-accent-500 hover:from-accent-500 hover:to-accent-600 text-white font-semibold rounded-lg transition-all duration-200">
                    <i class="fas fa-database"></i>
                    {{ __('Database') }}
                </a>
            @endif
        </div>

        <!-- SERVERS LIST -->
        @if (count($servers) === 0)
            <div
                class="flex items-center justify-center py-16 px-4 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900/50 dark:to-gray-800/50 rounded-xl border border-dashed border-gray-300 dark:border-gray-600">
                <div class="text-center">
                    <i class="fas fa-server text-4xl text-gray-400 dark:text-gray-600 mb-3"></i>
                    <p class="text-gray-600 dark:text-gray-400 text-lg font-medium">{{ __('No servers found') }}</p>
                    <p class="text-gray-500 dark:text-gray-500 text-sm mt-1">
                        {{ __('Create your first server to get started') }}</p>
                </div>
            </div>
        @else
            <div
                class="bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700/50 overflow-hidden shadow-sm">
                @foreach ($servers as $server)
                    @if ($server->location && $server->node && $server->nest && $server->egg)
                        <div
                            class="px-6 py-5 border-b border-gray-200 dark:border-gray-700/50 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-800/80 transition-colors duration-200 group">
                            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 items-center">
                                <!-- Server Name & Status -->
                                <div class="col-span-1">
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="w-10 h-10 rounded-lg bg-gradient-to-br from-accent-500/20 to-accent-600/20 flex items-center justify-center flex-shrink-0 group-hover:from-accent-500/30 group-hover:to-accent-600/30 transition-colors">
                                            <i class="fas fa-server text-accent-600 dark:text-accent-400"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h5 class="text-sm font-bold text-gray-900 dark:text-white truncate">
                                                {{ $server->name }}</h5>
                                            <div class="mt-2 flex items-center gap-2">
                                                @if ($server->suspended)
                                                    <span
                                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-red-500/15 text-red-600 dark:text-red-400 border border-red-500/20">
                                                        <i class="fas fa-circle-xmark text-xs"></i>
                                                        {{ __('Suspended') }}
                                                    </span>
                                                @elseif($server->canceled)
                                                    <span
                                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-500/15 text-yellow-700 dark:text-yellow-400 border border-yellow-500/20">
                                                        <i class="fas fa-triangle-exclamation text-xs"></i>
                                                        {{ __('Canceled') }}
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-green-500/15 text-green-700 dark:text-green-400 border border-green-500/20">
                                                        <i class="fas fa-check-circle text-xs"></i>
                                                        {{ __('Active') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Location & Software -->
                                <div class="col-span-1">
                                    <div class="space-y-3">
                                        <div>
                                            <p
                                                class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                                {{ __('Location') }}</p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                                    {{ $server->location }}</p>
                                                <i data-tippy-content="<div class='font-semibold mb-2'>{{ __('Node Information') }}</div><div class='text-sm'>{{ __('Node') }}: <strong>{{ $server->node }}</strong></div>"
                                                    data-tippy-interactive="true"
                                                    class="fas fa-circle-info text-gray-400 dark:text-gray-500 cursor-help text-xs hover:text-gray-600 dark:hover:text-gray-400 transition-colors"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <p
                                                class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                                {{ __('Software') }}</p>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white mt-1">
                                                {{ $server->nest }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Plan & Specs -->
                                <div class="col-span-1">
                                    <div class="space-y-3">
                                        <div>
                                            <p
                                                class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                                {{ __('Plan') }}</p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                                    {{ $server->product->name }}</p>
                                                <i data-tippy-content="<div class='font-semibold mb-2'>{{ __('Resource Specifications') }}</div><div class='text-sm space-y-1'><div><strong>{{ __('CPU') }}:</strong> {{ $server->product->cpu / 100 }} {{ __('vCores') }}</div><div><strong>{{ __('RAM') }}:</strong> {{ $server->product->memory }} MB</div><div><strong>{{ __('Disk') }}:</strong> {{ $server->product->disk }} MB</div><div><strong>{{ __('Backups') }}:</strong> {{ $server->product->backups }}</div><div><strong>{{ __('Databases') }}:</strong> {{ $server->product->databases }}</div><div><strong>{{ __('Allocations') }}:</strong> {{ $server->product->allocations }}</div><div><strong>{{ __('OOM Killer') }}:</strong> {{ $server->product->oom_killer ? __('Enabled') : __('Disabled') }}</div><div><strong>{{ __('Billing') }}:</strong> {{ $server->product->billing_period }}</div></div>"
                                                    data-tippy-interactive="true"
                                                    class="fas fa-circle-info text-gray-400 dark:text-gray-500 cursor-help text-xs hover:text-gray-600 dark:hover:text-gray-400 transition-colors"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Price & Billing -->
                                <div class="col-span-1">
                                    <div>
                                        <p
                                            class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                            {{ __('Price') }}</p>
                                        <div class="mt-2 space-y-1">
                                            <div class="text-2xl font-bold text-accent-600 dark:text-accent-400">
                                                {{ $server->product->display_price }}
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">per
                                                {{ $server->product->billing_period }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="col-span-1 flex flex-col gap-2">
                                    <div class="flex gap-2">
                                        <a href="{{ $pterodactyl_url }}/server/{{ $server->identifier }}" target="_blank"
                                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2.5 bg-accent-600 hover:bg-accent-700 text-white font-medium rounded-lg transition-all duration-200 text-xs shadow-sm hover:shadow-md"
                                            data-tippy-content="{{ __('Manage Server') }}">
                                            <i class="fas fa-gamepad"></i>
                                            <span class="hidden sm:inline">{{ __('Manage') }}</span>
                                        </a>
                                        <a href="{{ route('servers.show', ['server' => $server->id]) }}"
                                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2.5 bg-slate-600 hover:bg-slate-700 text-white font-medium rounded-lg transition-all duration-200 text-xs shadow-sm hover:shadow-md"
                                            data-tippy-content="{{ __('Server Settings') }}">
                                            <i class="fas fa-sliders"></i>
                                            <span class="hidden sm:inline">{{ __('Settings') }}</span>
                                        </a>
                                    </div>
                                    <div class="flex gap-2">
                                        <button onclick="handleServerCancel('{{ $server->id }}');"
                                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg transition-all duration-200 text-xs disabled:opacity-50 disabled:cursor-not-allowed shadow-sm hover:shadow-md"
                                            {{ $server->suspended || $server->canceled ? 'disabled' : '' }}
                                            data-tippy-content="{{ __('Cancel Server') }}">
                                            <i class="fas fa-pause text-xs"></i>
                                            <span class="hidden sm:inline">{{ __('Cancel') }}</span>
                                        </button>
                                        <button onclick="handleServerDelete('{{ $server->id }}');"
                                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-all duration-200 text-xs shadow-sm hover:shadow-md"
                                            data-tippy-content="{{ __('Delete Server') }}">
                                            <i class="fas fa-trash text-xs"></i>
                                            <span class="hidden sm:inline">{{ __('Delete') }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
        <!-- END CUSTOM CONTENT -->
    </section>
    <!-- END CONTENT -->

    <script>
        const handleServerCancel = (serverId) => {
            // Handle server cancel with sweetalert
            Swal.fire({
                title: "{{ __('Cancel Server?') }}",
                text: "{{ __('This will cancel your current server to the next billing period. It will get suspended when the current period runs out.') }}",
                icon: 'warning',
                confirmButtonColor: '#d9534f',
                showCancelButton: true,
                confirmButtonText: "{{ __('Yes, cancel it!') }}",
                cancelButtonText: "{{ __('No, abort!') }}",
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    // Delete server
                    fetch("{{ route('servers.cancel', '') }}" + '/' + serverId, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(() => {
                        window.location.reload();
                    }).catch((error) => {
                        Swal.fire({
                            title: "{{ __('Error') }}",
                            text: "{{ __('Something went wrong, please try again later.') }}",
                            icon: 'error',
                            confirmButtonColor: '#d9534f',
                        })
                    })
                    return
                }
            })
        }

        const handleServerDelete = (serverId) => {
            Swal.fire({
                title: "{{ __('Delete Server?') }}",
                html: "{!! __(
                    'This is an irreversible action, all files of this server will be removed. <strong>No funds will get refunded</strong>. We recommend deleting the server when server is suspended.',
                ) !!}",
                icon: 'warning',
                confirmButtonColor: '#d9534f',
                showCancelButton: true,
                confirmButtonText: "{{ __('Yes, delete it!') }}",
                cancelButtonText: "{{ __('No, abort!') }}",
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    // Delete server
                    fetch("{{ route('servers.destroy', '') }}" + '/' + serverId, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(() => {
                        window.location.reload();
                    }).catch((error) => {
                        Swal.fire({
                            title: "{{ __('Error') }}",
                            text: "{{ __('Something went wrong, please try again later.') }}",
                            icon: 'error',
                            confirmButtonColor: '#d9534f',
                        })
                    })
                    return
                }
            });

        }

        document.addEventListener('DOMContentLoaded', () => {
            // Tippy tooltips are initialized globally in app.js
        });

        $(function() {
            // Tooltips are now handled by Tippy.js
        })
    </script>
@endsection
