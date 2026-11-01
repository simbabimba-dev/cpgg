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
                        <li class="breadcrumb-item"><a
                                href="{{route('admin.webhooks.index')}}">{{__('Webhooks')}}</a></li>
                        <li class="breadcrumb-item"><a class="text-muted"
                                                       href="{{route('admin.webhooks.edit', $webhook->id)}}">{{$webhook->name}}</a>
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
                        <div class="card-body">
                            <form action="{{route('admin.webhooks.update', $webhook->id)}}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <label for="name">{{__('Name')}}</label>
                                    <input value="{{old('name', $webhook->name)}}" id="name" name="name"
                                           type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           required="required">
                                    @error('name')
                                    <div class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="url">{{__('Payload URL')}}</label>
                                    <input value="{{old('url', $webhook->url)}}" id="url" name="url"
                                           type="url"
                                           class="form-control @error('url') is-invalid @enderror"
                                           required="required">
                                    @error('url')
                                    <div class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="secret">{{__('Secret')}} <small
                                            class="text-muted">({{__('optional')}})</small></label>
                                    <input value="{{old('secret')}}" id="secret" name="secret"
                                           type="text"
                                           autocomplete="off"
                                           placeholder="{{$webhook->secret ? __('Leave blank to keep the current secret') : ''}}"
                                           class="form-control @error('secret') is-invalid @enderror">
                                    <div class="text-muted">
                                        {{__('When set, every request is signed with an X-CtrlPanel-Signature header (HMAC-SHA256).')}}
                                    </div>
                                    @if($webhook->secret)
                                        <div class="custom-control custom-checkbox mt-2">
                                            <input type="checkbox" class="custom-control-input" id="clear_secret"
                                                   name="clear_secret" value="1" @checked(old('clear_secret'))>
                                            <label class="custom-control-label"
                                                   for="clear_secret">{{__('Remove the current secret')}}</label>
                                        </div>
                                    @endif
                                    @error('secret')
                                    <div class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>{{__('Events')}}</label>
                                    <div class="text-muted mb-2">{{__('Select the events that should be sent to this webhook.')}}</div>

                                    @foreach($eventGroups as $group => $events)
                                        <div class="mb-3">
                                            <h6 class="text-uppercase text-muted small mb-2">{{$group}}</h6>
                                            @foreach($events as $value => $label)
                                                <div class="custom-control custom-checkbox">
                                                    <input class="custom-control-input" type="checkbox"
                                                           id="event-{{str_replace('.', '-', $value)}}"
                                                           name="events[]" value="{{$value}}"
                                                           @checked(in_array($value, old('events', $webhook->events ?? []), true))>
                                                    <label class="custom-control-label"
                                                           for="event-{{str_replace('.', '-', $value)}}">{{$label}}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach

                                    @error('events')
                                    <div class="text-danger small">
                                        {{$message}}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="hidden" name="enabled" value="0">
                                        <input type="checkbox" class="custom-control-input" id="enabled"
                                               name="enabled" value="1"
                                               @checked(old('enabled', $webhook->enabled))>
                                        <label class="custom-control-label" for="enabled">{{__('Enabled')}}</label>
                                    </div>
                                </div>

                                <div class="form-group text-right">
                                    <a href="{{route('admin.webhooks.index')}}"
                                       class="btn btn-secondary">{{__('Cancel')}}</a>
                                    <button type="submit" class="btn btn-primary">{{__('Save changes')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
    <!-- END CONTENT -->

@endsection