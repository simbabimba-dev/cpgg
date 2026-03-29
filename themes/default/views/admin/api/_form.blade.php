@php
    $scopeGroups = collect($availableScopes)
        ->groupBy(static fn (string $scope) => explode('.', $scope)[0])
        ->map(function ($scopes, string $resource) {
            return [
                'resource' => $resource,
                'label' => \Illuminate\Support\Str::headline(\Illuminate\Support\Str::singular($resource)),
                'read' => $scopes->contains($resource . '.read') ? $resource . '.read' : null,
                'write' => $scopes->contains($resource . '.write') ? $resource . '.write' : null,
            ];
        })
        ->values();

    $initialScopes = old('scopes', $scopesValue ?? '');
@endphp

<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary">
            <div class="card-body">
                <span class="badge badge-info px-3 py-2 mb-3">
                    <i class="fas fa-key mr-1"></i>{{ $heroKicker }}
                </span>
                <h2 class="h3 mb-3">{{ $heroTitle }}</h2>
                <p class="text-muted mb-3">{{ $heroDescription }}</p>
                <div>
                    <span class="badge badge-light mr-2 mb-2">{{ __('Least-privilege by default') }}</span>
                    <span class="badge badge-light mr-2 mb-2">{{ __('Per-resource read/write controls') }}</span>
                    <span class="badge badge-light mb-2">{{ __('Token activity is tracked') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title mb-0">{{ $panelTitle }}</h3>
                <div class="text-muted small mt-1">{{ $panelCaption }}</div>
            </div>
            <div class="card-body">
                <form action="{{ $formAction }}" method="POST" id="apiScopeForm">
                    @csrf
                    @if (!empty($method))
                        @method($method)
                    @endif

                    <div class="form-group">
                        <label for="memo">{{ __('Description') }} <span class="text-muted small">{{ __('optional') }}</span></label>
                        <input
                            value="{{ old('memo', $memoValue ?? '') }}"
                            id="memo"
                            name="memo"
                            type="text"
                            maxlength="60"
                            class="form-control @error('memo') is-invalid @enderror"
                            placeholder="{{ __('Where will this token be used?') }}"
                        >
                        @error('memo')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <input type="hidden" name="scopes" id="scopes" value="{{ $initialScopes }}">

                    <div class="form-group">
                        <label class="d-block">{{ __('Access mode') }}</label>
                        <div class="row">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="custom-control custom-radio border rounded p-3 h-100">
                                    <input
                                        type="radio"
                                        id="scope_mode_scoped"
                                        name="scope_mode"
                                        value="scoped"
                                        class="custom-control-input"
                                        {{ trim((string) $initialScopes) !== '' ? 'checked' : '' }}
                                    >
                                    <label class="custom-control-label w-100" for="scope_mode_scoped">
                                        <span class="d-block font-weight-bold">{{ __('Scoped access') }}</span>
                                        <small class="text-muted">{{ __('Choose read or write access for each resource.') }}</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-control custom-radio border rounded p-3 h-100">
                                    <input
                                        type="radio"
                                        id="scope_mode_legacy"
                                        name="scope_mode"
                                        value="legacy"
                                        class="custom-control-input"
                                        {{ trim((string) $initialScopes) === '' ? 'checked' : '' }}
                                    >
                                    <label class="custom-control-label w-100" for="scope_mode_legacy">
                                        <span class="d-block font-weight-bold">{{ __('Legacy compatibility') }}</span>
                                        <small class="text-muted">{{ __('Leaves scopes empty for older integrations.') }}</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            {{ __('Use legacy mode only when an integration cannot yet send scoped requests.') }}
                        </small>
                    </div>

                    <div class="form-group" id="scopeMatrixSection">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
                            <div class="mb-2 mb-md-0">
                                <label class="d-block mb-1">{{ __('Resource scopes') }}</label>
                                <small class="text-muted">{{ __('Choose the narrowest access level each integration needs.') }}</small>
                            </div>
                            <div class="btn-group btn-group-sm" role="group" aria-label="{{ __('Scope presets') }}">
                                <button type="button" class="btn btn-outline-info" data-scope-preset="read">{{ __('Read all') }}</button>
                                <button type="button" class="btn btn-outline-success" data-scope-preset="write">{{ __('Read & Write all') }}</button>
                                <button type="button" class="btn btn-outline-secondary" data-scope-preset="clear">{{ __('Clear all') }}</button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead>
                                <tr>
                                    <th>{{ __('Resource') }}</th>
                                    <th class="text-center">{{ __('Read') }}</th>
                                    <th class="text-center">{{ __('Read & Write') }}</th>
                                    <th class="text-center">{{ __('None') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($scopeGroups as $group)
                                    <tr>
                                        <td class="align-middle">
                                            <div class="font-weight-bold">{{ $group['label'] }}</div>
                                            <small class="text-muted">{{ __('Controls access to :resource endpoints', ['resource' => strtolower($group['label'])]) }}</small>
                                        </td>
                                        <td class="align-middle text-center">
                                            <div class="custom-control custom-radio d-inline-block text-left">
                                                <input
                                                    type="radio"
                                                    id="scope_{{ $group['resource'] }}_read"
                                                    name="scope_selection_{{ $group['resource'] }}"
                                                    value="read"
                                                    class="custom-control-input"
                                                    data-resource="{{ $group['resource'] }}"
                                                    data-read-scope="{{ $group['read'] }}"
                                                    data-write-scope="{{ $group['write'] }}"
                                                >
                                                <label class="custom-control-label" for="scope_{{ $group['resource'] }}_read">{{ __('Read') }}</label>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                            <div class="custom-control custom-radio d-inline-block text-left">
                                                <input
                                                    type="radio"
                                                    id="scope_{{ $group['resource'] }}_write"
                                                    name="scope_selection_{{ $group['resource'] }}"
                                                    value="write"
                                                    class="custom-control-input"
                                                    data-resource="{{ $group['resource'] }}"
                                                    data-read-scope="{{ $group['read'] }}"
                                                    data-write-scope="{{ $group['write'] }}"
                                                >
                                                <label class="custom-control-label" for="scope_{{ $group['resource'] }}_write">{{ __('Read & Write') }}</label>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                            <div class="custom-control custom-radio d-inline-block text-left">
                                                <input
                                                    type="radio"
                                                    id="scope_{{ $group['resource'] }}_none"
                                                    name="scope_selection_{{ $group['resource'] }}"
                                                    value="none"
                                                    class="custom-control-input"
                                                    data-resource="{{ $group['resource'] }}"
                                                    data-read-scope="{{ $group['read'] }}"
                                                    data-write-scope="{{ $group['write'] }}"
                                                >
                                                <label class="custom-control-label" for="scope_{{ $group['resource'] }}_none">{{ __('None') }}</label>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        @error('scopes')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                        @enderror

                        <div class="alert alert-danger mt-3 mb-0 d-none" id="scopeFormError" role="alert"></div>
                    </div>

                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center border-top pt-3 mt-4">
                        <p class="text-muted mb-3 mb-md-0">
                            {{ __('Once credentials are issued, treat them like passwords and rotate them if an integration changes.') }}
                        </p>
                        <button type="submit" class="btn btn-success">
                            {{ $submitLabel }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title mb-0">{{ __('Quick Notes') }}</h3>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-3">
                        <i class="fas fa-check-circle text-info mr-2"></i>{{ __('Pick scoped access for anything new to keep blast radius smaller.') }}
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-stream text-info mr-2"></i>{{ __('Write access includes the matching read scope automatically.') }}
                    </li>
                    <li class="mb-0">
                        <i class="fas fa-history text-info mr-2"></i>{{ __('Legacy mode preserves compatibility for older tooling that still depends on empty scopes.') }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('apiScopeForm');
        if (!form) {
            return;
        }

        const hiddenScopesInput = document.getElementById('scopes');
        const formError = document.getElementById('scopeFormError');
        const modeInputs = Array.from(form.querySelectorAll('input[name="scope_mode"]'));
        const resourceInputs = Array.from(form.querySelectorAll('input[type="radio"][name^="scope_selection_"]'));
        const presetButtons = Array.from(form.querySelectorAll('[data-scope-preset]'));
        const initialScopes = (hiddenScopesInput.value || '')
            .split(',')
            .map(scope => scope.trim())
            .filter(Boolean);

        const groupedByResource = resourceInputs.reduce((groups, input) => {
            const resource = input.dataset.resource;
            groups[resource] = groups[resource] || [];
            groups[resource].push(input);
            return groups;
        }, {});

        const setError = function (message) {
            if (!formError) {
                return;
            }

            formError.textContent = message;
            formError.classList.toggle('d-none', !message);
        };

        const currentMode = function () {
            const selected = modeInputs.find(input => input.checked);
            return selected ? selected.value : 'scoped';
        };

        const syncHiddenScopes = function () {
            if (currentMode() === 'legacy') {
                hiddenScopesInput.value = '';
                setError('');
                return;
            }

            const scopes = [];

            Object.values(groupedByResource).forEach(group => {
                const selected = group.find(input => input.checked);
                if (!selected || selected.value === 'none') {
                    return;
                }

                if (selected.dataset.readScope) {
                    scopes.push(selected.dataset.readScope);
                }

                if (selected.value === 'write' && selected.dataset.writeScope) {
                    scopes.push(selected.dataset.writeScope);
                }
            });

            hiddenScopesInput.value = Array.from(new Set(scopes)).join(',');
        };

        const applyPreset = function (preset) {
            Object.values(groupedByResource).forEach(group => {
                const nextValue = preset === 'clear' ? 'none' : preset;
                const target = group.find(input => input.value === nextValue);
                if (target) {
                    target.checked = true;
                }
            });

            const scopedModeInput = modeInputs.find(input => input.value === 'scoped');
            if (scopedModeInput) {
                scopedModeInput.checked = true;
            }

            syncHiddenScopes();
        };

        Object.entries(groupedByResource).forEach(([resource, group]) => {
            const hasWrite = initialScopes.includes(resource + '.write');
            const hasRead = initialScopes.includes(resource + '.read');
            const preferred = group.find(input => input.value === (hasWrite ? 'write' : (hasRead ? 'read' : 'none')));
            if (preferred) {
                preferred.checked = true;
            }
        });

        modeInputs.forEach(input => {
            input.addEventListener('change', function () {
                const scopedMode = currentMode() === 'scoped';
                presetButtons.forEach(button => {
                    button.disabled = !scopedMode;
                });
                resourceInputs.forEach(radio => {
                    radio.disabled = !scopedMode;
                });
                syncHiddenScopes();
            });
        });

        resourceInputs.forEach(input => {
            input.addEventListener('change', syncHiddenScopes);
        });

        presetButtons.forEach(button => {
            button.addEventListener('click', function () {
                applyPreset(this.dataset.scopePreset);
            });
        });

        form.addEventListener('submit', function (event) {
            syncHiddenScopes();

            if (currentMode() === 'scoped' && !hiddenScopesInput.value.trim()) {
                event.preventDefault();
                setError('{{ __('Choose at least one scoped permission or switch to legacy compatibility mode.') }}');
                return;
            }

            setError('');
        });

        modeInputs.find(input => input.checked)?.dispatchEvent(new Event('change'));
        syncHiddenScopes();
    });
</script>
