<?php

namespace App\Listeners;

use App\Enums\WebhookEvent;
use App\Events\ServerCreatedEvent;
use App\Services\WebhookService;

class DispatchServerCreatedWebhook
{
    /**
     * Handle the event.
     */
    public function handle(ServerCreatedEvent $event): void
    {
        app(WebhookService::class)->trigger(WebhookEvent::SERVER_CREATED, array_merge(
            WebhookService::serverPayload($event->server),
            ['user' => WebhookService::userPayload($event->user)]
        ));
    }
}
