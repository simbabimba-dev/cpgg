<?php

namespace App\Listeners;

use App\Enums\WebhookEvent;
use App\Events\PaymentEvent;
use App\Services\WebhookService;

class DispatchPaymentWebhook
{
    /**
     * Handle the event.
     */
    public function handle(PaymentEvent $event): void
    {
        app(WebhookService::class)->trigger(WebhookEvent::PAYMENT_COMPLETED, array_merge(
            WebhookService::paymentPayload($event->payment, $event->user),
            [
                'shop_product' => [
                    'id' => $event->shopProduct->id,
                    'type' => $event->shopProduct->type,
                    'description' => $event->shopProduct->description,
                ],
            ]
        ));
    }
}
