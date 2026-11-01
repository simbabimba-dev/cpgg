<?php

namespace App\Services;

use App\Enums\WebhookEvent;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Server;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Webhook;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WebhookService
{
    /**
     * Standard payload for a user event.
     */
    public static function userPayload(User $user): array
    {
        return $user->only([
            'id',
            'name',
            'email',
            'credits',
            'server_limit',
            'pterodactyl_id',
            'suspended',
            'created_at',
        ]);
    }

    /**
     * Standard payload for a server event.
     */
    public static function serverPayload(Server $server): array
    {
        return $server->only([
            'id',
            'name',
            'identifier',
            'pterodactyl_id',
            'user_id',
            'product_id',
            'suspended',
            'created_at',
        ]);
    }

    /**
     * Standard payload for a product event.
     */
    public static function productPayload(Product $product): array
    {
        return $product->only([
            'id',
            'name',
            'description',
            'price',
            'memory',
            'cpu',
            'disk',
            'disabled',
            'billing_period',
        ]);
    }

    /**
     * Standard payload for a ticket event.
     */
    public static function ticketPayload(Ticket $ticket): array
    {
        return $ticket->only([
            'id',
            'ticket_id',
            'title',
            'user_id',
            'ticketcategory_id',
            'priority',
            'status',
            'created_at',
        ]);
    }

    /**
     * Standard payload for a completed payment.
     */
    public static function paymentPayload(Payment $payment, ?User $user = null): array
    {
        $payload = $payment->only([
            'id',
            'user_id',
            'amount',
            'currency_code',
            'status',
            'created_at',
        ]);

        if ($user !== null) {
            $payload['user'] = self::userPayload($user);
        }

        return $payload;
    }
    /**
     * Deliver an event to every enabled webhook subscribed to it.
     *
     * Delivery failures never bubble up to the caller: a broken webhook must
     * not prevent the action that triggered it from completing.
     */
    public function trigger(WebhookEvent $event, array $data = []): void
    {
        try {
            $webhooks = Webhook::query()->where('enabled', true)->get();
        } catch (\Throwable $exception) {
            // The webhooks table may not exist yet (for example during an
            // installation). Silently skip delivery in that case.
            Log::debug('Unable to load webhooks: ' . $exception->getMessage());

            return;
        }

        foreach ($webhooks as $webhook) {
            if (!$webhook->hasEvent($event->value)) {
                continue;
            }

            $this->deliver($webhook, $event->value, $data);
        }
    }

    /**
     * Send a test payload to a single webhook, ignoring its subscriptions.
     */
    public function sendTest(Webhook $webhook): bool
    {
        return $this->deliver($webhook, 'webhook.test', [
            'message' => 'This is a test webhook sent from CtrlPanel.',
        ]);
    }

    /**
     * POST the payload to the webhook and return whether it was accepted.
     */
    public function deliver(Webhook $webhook, string $event, array $data): bool
    {
        $payload = [
            'event' => $event,
            'timestamp' => now()->toIso8601String(),
            'data' => $data,
        ];

        $body = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($body === false) {
            Log::warning('Unable to encode webhook payload.', [
                'webhook_id' => $webhook->id,
                'event' => $event,
            ]);

            return false;
        }

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'User-Agent' => 'CtrlPanel-Webhook/1.0',
            'X-CtrlPanel-Event' => $event,
            'X-CtrlPanel-Delivery' => (string) Str::uuid(),
        ];

        if (!empty($webhook->secret)) {
            $headers['X-CtrlPanel-Signature'] = 'sha256=' . hash_hmac('sha256', $body, $webhook->secret);
        }

        try {
            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->withBody($body, 'application/json')
                ->post($webhook->url);

            if ($response->failed()) {
                Log::warning('Webhook delivery failed.', [
                    'webhook_id' => $webhook->id,
                    'event' => $event,
                    'status' => $response->status(),
                ]);

                return false;
            }

            $webhook->markAsTriggered();

            return true;
        } catch (\Throwable $exception) {
            Log::warning('Webhook delivery failed.', [
                'webhook_id' => $webhook->id,
                'event' => $event,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}