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
                <a href="{{ route('admin.ticket.index') }}" class="hover:text-accent-600 dark:hover:text-accent-400">{{ __('Ticket') }}</a>
            </li>
            <li>/</li>
            <li>
                <span class="text-gray-700 dark:text-gray-300">#{{ $ticket->ticket_id }}</span>
            </li>
        </ol>
    </div>
    <section class="content">
        <div class="w-full">
            <div class="grid grid-cols-1 gap-6">
                
                <div class="bg-white rounded-lg shadow-md overflow-hidden dark:bg-gray-800">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center dark:border-gray-700">
                        <h5 class="text-lg font-medium text-gray-800 dark:text-white">
                            <i class="fas fa-info-circle mr-2 text-gray-500 dark:text-gray-400"></i>#{{ $ticket->ticket_id }}
                        </h5>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            @if(!empty($server))
                                <div class="flex flex-col">
                                    <span class="text-gray-500 dark:text-gray-400">{{ __('Server') }}:</span>
                                    <a href="{{ $pterodactyl_url . '/admin/servers/view/' . $server->pterodactyl_id }}" target="__blank" class="font-medium text-accent-600 hover:underline dark:text-accent-400">
                                        {{ $server->name }}
                                    </a>
                                </div>
                            @endif
                            <div class="flex flex-col">
                                <span class="text-gray-500 dark:text-gray-400">{{ __('Title') }}:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $ticket->title }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-gray-500 dark:text-gray-400">{{ __('Category') }}:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $ticketcategory->name }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-gray-500 dark:text-gray-400">{{ __('Created') }}:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $ticket->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-gray-500 dark:text-gray-400 mb-1">{{ __('Status') }}:</span>
                                <div>
                                    @switch($ticket->status)
                                        @case("Open")
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">{{ __('Open') }}</span>
                                            @break
                                        @case("Reopened")
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">{{ __('Reopened') }}</span>
                                            @break
                                        @case("Closed")
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">{{ __('Closed') }}</span>
                                            @break
                                        @case("Answered")
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-accent-100 text-accent-800 dark:bg-accent-900 dark:text-accent-200">{{ __('Answered') }}</span>
                                            @break
                                        @case("Client Reply")
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">{{ __('Client Reply') }}</span>
                                            @break
                                    @endswitch
                                </div>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-gray-500 dark:text-gray-400 mb-1">{{ __('Priority') }}:</span>
                                <div>
                                    @switch($ticket->priority)
                                        @case("Low")
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">{{ __('Low') }}</span>
                                            @break
                                        @case("Medium")
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">{{ __('Medium') }}</span>
                                            @break
                                        @case("High")
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">{{ __('High') }}</span>
                                            @break
                                    @endswitch
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            @if($ticket->status == 'Closed')
                                <form action="{{ route('admin.ticket.changeStatus', ['ticket_id' => $ticket->ticket_id ]) }}" method="POST">
                                    @csrf
                                    @method("POST")
                                    <button type="submit" class="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:hover:bg-green-500">
                                        <i class="fas fa-redo mr-2"></i>{{ __('Reopen') }}
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.ticket.changeStatus', ['ticket_id' => $ticket->ticket_id ]) }}" method="POST">
                                    @csrf
                                    @method("POST")
                                    <button type="submit" class="inline-flex items-center rounded-md bg-yellow-500 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:hover:bg-yellow-400">
                                        <i class="fas fa-times mr-2"></i>{{ __('Close') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md overflow-hidden dark:bg-gray-800">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h5 class="text-lg font-medium text-gray-800 dark:text-white">
                            <i class="fas fa-comments mr-2 text-gray-500 dark:text-gray-400"></i>{{ __('Conversation') }}
                        </h5>
                    </div>
                    <div class="p-6 bg-gray-50 dark:bg-gray-900">
                        
                        <div class="mb-6 rounded-lg bg-white shadow dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 bg-gray-50 rounded-t-lg dark:bg-gray-700 dark:border-gray-600">
                                <div class="flex items-center">
                                    <img class="h-8 w-8 rounded-full" src="https://www.gravatar.com/avatar/{{ md5(strtolower($ticket->user->email)) }}?s=160" alt="User Image">
                                    <div class="ml-3">
                                        <a href="/admin/users/{{$ticket->user->id}}" class="text-sm font-medium text-gray-900 hover:underline dark:text-white">{{ $ticket->user->name }}</a>
                                        <div class="flex space-x-1 mt-0.5">
                                            @foreach ($ticket->user->roles as $role)
                                                <span style="background-color: {{$role->color}}" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium text-white shadow-sm">
                                                    {{$role->name}}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $ticket->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="px-4 py-4 text-gray-800 dark:text-gray-200 whitespace-pre-wrap leading-relaxed">
                                {{ $ticket->message }}
                            </div>
                        </div>

                        @foreach ($ticketcomments as $ticketcomment)
                            <div class="mb-6 rounded-lg bg-white shadow dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 bg-gray-50 rounded-t-lg dark:bg-gray-700 dark:border-gray-600">
                                    <div class="flex items-center">
                                        <img class="h-8 w-8 rounded-full" src="https://www.gravatar.com/avatar/{{ md5(strtolower($ticketcomment->user->email)) }}?s=160" alt="User Image">
                                        <div class="ml-3">
                                            <a href="/admin/users/{{$ticketcomment->user->id}}" class="text-sm font-medium text-gray-900 hover:underline dark:text-white">{{ $ticketcomment->user->name }}</a>
                                            <div class="flex space-x-1 mt-0.5">
                                                @foreach ($ticketcomment->user->roles as $role)
                                                    <span style="background-color: {{$role->color}}" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium text-white shadow-sm">
                                                        {{$role->name}}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $ticketcomment->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="px-4 py-4 text-gray-800 dark:text-gray-200 whitespace-pre-wrap leading-relaxed">
                                    {{ $ticketcomment->ticketcomment }}
                                </div>
                            </div>
                        @endforeach

                        <div class="mt-8">
                            <form action="{{ route('admin.ticket.reply')}}" method="POST">
                                @csrf
                                <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                                <div class="mb-4">
                                    <label for="ticketcomment" class="sr-only">{{ __('Reply') }}</label>
                                    <textarea rows="6" id="ticketcomment" name="ticketcomment"
                                              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-accent-500 focus:ring-accent-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 @error('ticketcomment') border-red-500 @enderror"
                                              placeholder="{{ __('Write a reply...') }}"></textarea>
                                    @error('ticketcomment')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="flex justify-end">
                                    <button type="submit" class="inline-flex items-center rounded-md bg-accent-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 dark:hover:bg-accent-500">
                                        {{ __('Submit Reply') }}
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection