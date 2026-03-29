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
                        <li class="breadcrumb-item"><a href="{{route('admin.api.index')}}">{{__('Application API')}}</a>
                        </li>
                        <li class="breadcrumb-item"><a class="text-muted"
                                                       href="{{route('admin.api.create')}}">{{__('Create')}}</a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @include('admin.api._form', [
                'heroKicker' => __('New Credentials'),
                'heroTitle' => __('Create an application token with intentional scope boundaries'),
                'heroDescription' => __('Shape each credential around the integration that will use it, so you are not handing out broad access when a narrow token will do.'),
                'formAction' => route('admin.api.store'),
                'method' => null,
                'panelTitle' => __('Credential details'),
                'panelCaption' => __('Pick a description and access level set, then create the token.'),
                'submitLabel' => __('Create Credentials'),
                'memoValue' => old('memo'),
                'scopesValue' => old('scopes'),
                'availableScopes' => $availableScopes,
            ])
        </div>
    </section>
@endsection
