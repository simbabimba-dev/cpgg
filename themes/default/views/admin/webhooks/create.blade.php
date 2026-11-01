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
                                                       href="{{route('admin.webhooks.create')}}">{{__('Create')}}</a>
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
                            <form action="{{route('admin.webhooks.store')}}" method="POST">
                                @csrf

                                <div class="form-group">
                                    <label for="name">{{__('Name')}}</label>
                                    <input value="{{old('name')}}" id="name" name="name"
                                           type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           required="required">
                                    <div class="text-muted">
                                        {{__('A name to identify this webhook, for example "Discord alerts".')}}
                                    </div>
                                    @error('name')
                                    <div class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="url">{{__('Payload URL')}}</label>
                                    <input value="{{old('url')}}" id="url" name="url"
                                           type="url"
                                           placeholder="https://example.com/webhook"
                                           class="form-control @error('url') is-invalid @enderror"
                                           required="required">
                                    @error('url')
                                    <div class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="secret">{{__('Secret')}} <small class="text-muted">({{__('optional')}})</small></label>
                                    <input value="{{old('secret')}}" id="secret" name="secret"
                                           type="text"
                                           autocomplete="off"
                                           class="form-control @error('secret') is-invalid @enderror">
                                    <div class="text-muted">
                                        {{__('When set, every request is signed with an X-CtrlPanel-Signature header (HMAC-SHA256).')}}
                                    </div>
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
                                                           @checked(in_array($value, old('events', []), true))>
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
                                               name="enabled" value="1" @checked(old('enabled', true))>
                                        <label class="custom-control-label" for="enabled">{{__('Enabled')}}</label>
                                    </div>
                                </div>

                                <div class="form-group text-right">
                                    <a href="{{route('admin.webhooks.index')}}"
                                       class="btn btn-secondary">{{__('Cancel')}}</a>
                                    <button type="submit" class="btn btn-primary">{{__('Create webhook')}}</button>
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