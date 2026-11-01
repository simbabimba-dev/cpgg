<?php

namespace App\Listeners;

use App\Enums\WebhookEvent;
use App\Events\CouponUsedEvent;
use App\Services\WebhookService;

class DispatchCouponUsedWebhook
{
    /**
     * Handle the event.
     */
    public function handle(CouponUsedEvent $event): void
    {
        $payload = [
            'code' => $event->couponCode,
            'coupon_id' => $event->coupon->id,
        ];

        if ($event->user !== null) {
            $payload['user'] = WebhookService::userPayload($event->user);
        }

        app(WebhookService::class)->trigger(WebhookEvent::COUPON_USED, $payload);
    }
}