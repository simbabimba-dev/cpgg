<?php

namespace App\Http\Controllers\Admin;

use App\Enums\WebhookEvent;
use App\Http\Controllers\Controller;
use App\Models\Webhook;
use App\Services\WebhookService;
use App\Settings\LocaleSettings;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WebhookController extends Controller
{
    const READ_PERMISSION = 'admin.webhooks.read';
    const WRITE_PERMISSION = 'admin.webhooks.write';

    /**
     * Display a listing of the webhooks.
     *
     * @return Application|Factory|View
     */
    public function index(LocaleSettings $locale_settings)
    {
        $this->checkAnyPermission([self::READ_PERMISSION, self::WRITE_PERMISSION]);

        return view('admin.webhooks.index', [
            'locale_datatables' => $locale_settings->datatables,
        ]);
    }

    /**
     * Show the form for creating a new webhook.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $this->checkPermission(self::WRITE_PERMISSION);

        return view('admin.webhooks.create', [
            'eventGroups' => WebhookEvent::grouped(),
        ]);
    }

    /**
     * Store a newly created webhook.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->checkPermission(self::WRITE_PERMISSION);

        $data = $this->validatedData($request);
        $data['secret'] = ($data['secret'] ?? null) ?: null;

        $webhook = Webhook::create($data);

        return redirect()->route('admin.webhooks.index')
            ->with('success', __('Webhook ":name" has been created!', ['name' => $webhook->name]));
    }

    /**
     * Show the form for editing the specified webhook.
     *
     * @return Application|Factory|View
     */
    public function edit(Webhook $webhook)
    {
        $this->checkPermission(self::WRITE_PERMISSION);

        return view('admin.webhooks.edit', [
            'webhook' => $webhook,
            'eventGroups' => WebhookEvent::grouped(),
        ]);
    }

    /**
     * Update the specified webhook.
     */
    public function update(Request $request, Webhook $webhook): RedirectResponse
    {
        $this->checkPermission(self::WRITE_PERMISSION);

        $request->validate([
            'clear_secret' => ['nullable', 'boolean'],
        ]);

        $data = $this->validatedData($request);

        // An empty secret field means "keep the existing secret" unless the
        // admin explicitly asked to remove it.
        if ($request->boolean('clear_secret')) {
            $data['secret'] = null;
        } elseif (empty($data['secret'])) {
            unset($data['secret']);
        }

        $webhook->update($data);

        return redirect()->route('admin.webhooks.index')
            ->with('success', __('Webhook ":name" has been updated!', ['name' => $webhook->name]));
    }

    /**
     * Remove the specified webhook.
     */
    public function destroy(Webhook $webhook): RedirectResponse
    {
        $this->checkPermission(self::WRITE_PERMISSION);

        $webhook->delete();

        return redirect()->route('admin.webhooks.index')
            ->with('success', __('Webhook has been removed!'));
    }

    /**
     * Send a test payload to the specified webhook.
     */
    public function test(Webhook $webhook, WebhookService $webhookService): RedirectResponse
    {
        $this->checkPermission(self::WRITE_PERMISSION);

        if ($webhookService->sendTest($webhook)) {
            return redirect()->back()->with('success', __('Test payload sent to ":name"!', ['name' => $webhook->name]));
        }

        return redirect()->back()->with('error', __('Could not reach ":name". Check the URL and the panel logs.', ['name' => $webhook->name]));
    }

    /**
     * Datatable source for the webhook list.
     */
    public function datatable(): JsonResponse
    {
        $this->checkAnyPermission([self::READ_PERMISSION, self::WRITE_PERMISSION]);

        return datatables(Webhook::query())
            ->addColumn('events', function (Webhook $webhook) {
                $labels = collect($webhook->events ?? [])
                    ->map(fn (string $event) => WebhookEvent::tryFrom($event)?->label() ?? $event)
                    ->map(fn (string $label) => '<span class="badge badge-secondary mr-1 mb-1">' . e($label) . '</span>')
                    ->implode(' ');

                return $labels ?: '<span class="text-muted">' . __('No events') . '</span>';
            })
            ->addColumn('enabled', function (Webhook $webhook) {
                return $webhook->enabled
                    ? '<span class="badge badge-success">' . __('Enabled') . '</span>'
                    : '<span class="badge badge-secondary">' . __('Disabled') . '</span>';
            })
            ->addColumn('actions', function (Webhook $webhook) {
                return '
                    <form class="d-inline" method="post" action="'.route('admin.webhooks.test', $webhook->id).'">
                        '.csrf_field().'
                        <button data-content="'.__('Send test payload').'" data-toggle="popover" data-trigger="hover" data-placement="top" class="btn btn-sm btn-primary mr-1"><i class="fas fa-paper-plane"></i></button>
                    </form>
                    <a data-content="'.__('Edit').'" data-toggle="popover" data-trigger="hover" data-placement="top" href="'.route('admin.webhooks.edit', $webhook->id).'" class="btn btn-sm btn-info mr-1"><i class="fas fa-pen"></i></a>
                    <form class="d-inline" onsubmit="return submitResult(this);" method="post" action="'.route('admin.webhooks.destroy', $webhook->id).'">
                        '.csrf_field().'
                        '.method_field('DELETE').'
                        <button data-content="'.__('Delete').'" data-toggle="popover" data-trigger="hover" data-placement="top" class="btn btn-sm btn-danger mr-1"><i class="fas fa-trash"></i></button>
                    </form>
                ';
            })
            ->editColumn('last_triggered_at', function (Webhook $webhook) {
                return $webhook->last_triggered_at
                    ? $webhook->last_triggered_at->diffForHumans()
                    : '<span class="text-muted">' . __('Never') . '</span>';
            })
            ->editColumn('created_at', function (Webhook $webhook) {
                return $webhook->created_at ? $webhook->created_at->diffForHumans() : '';
            })
            ->rawColumns(['events', 'enabled', 'actions', 'last_triggered_at'])
            ->make(true);
    }

    /**
     * Validate and normalise the request data.
     *
     * @return array<string, mixed>
     */
    private function validatedData(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'url' => ['required', 'url:http,https', 'max:2048'],
            'secret' => ['nullable', 'string', 'max:255'],
            'events' => ['required', 'array', 'min:1'],
            'events.*' => ['string', Rule::enum(WebhookEvent::class)],
            'enabled' => ['nullable', 'boolean'],
        ]);

        $validated['events'] = array_values(array_unique($validated['events']));
        $validated['enabled'] = $request->boolean('enabled');

        return $validated;
    }
}