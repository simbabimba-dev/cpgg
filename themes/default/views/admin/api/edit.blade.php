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
                                                       href="{{route('admin.api.edit'  , $applicationApi->token)}}">{{__('Edit')}}</a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @include('admin.api._form', [
                'heroKicker' => __('Update Credentials'),
                'heroTitle' => __('Refine an existing token without losing the audit trail'),
                'heroDescription' => __('Adjust the description or scope profile so the integration keeps exactly the access it needs and nothing more.'),
                'formAction' => route('admin.api.update', $applicationApi->token),
                'method' => 'PATCH',
                'panelTitle' => __('Credential details'),
                'panelCaption' => __('Review the current access profile before saving changes.'),
                'submitLabel' => __('Save Changes'),
                'memoValue' => old('memo', $applicationApi->memo),
                'scopesValue' => old('scopes', implode(',', $applicationApi->normalizedScopes())),
                'availableScopes' => $availableScopes,
            ])
        </div>
    </section>
@endsection
