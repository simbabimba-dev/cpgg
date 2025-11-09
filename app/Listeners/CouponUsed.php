<?php

namespace App\Listeners;

use App\Events\CouponUsedEvent;
use App\Settings\CouponSettings;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

class CouponUsed
{
    private $delete_coupon_on_expires;
    private $delete_coupon_on_uses_reached;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(CouponSettings $couponSettings)
    {
        $this->delete_coupon_on_expires = $couponSettings->delete_coupon_on_expires;
        $this->delete_coupon_on_uses_reached = $couponSettings->delete_coupon_on_uses_reached;
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\CouponUsedEvent  $event
     * @return void
     */
    public function handle(CouponUsedEvent $event)
    {
        // Automatically increments the coupon usage.
        $this->incrementUses($event);

        // Increment per-user usage by attaching to user_coupons pivot
        if ($event->user && $event->coupon) {
            try {
                $event->user->coupons()->attach($event->coupon->id, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (QueryException $e) {
                // Ignore duplicate key errors - this is expected when concurrent requests
                // both try to attach the same user-coupon relationship (idempotent).
                if (!str_contains($e->getMessage(), 'Duplicate entry')) {
                    throw $e;
                }
            }
        }

        if ($this->delete_coupon_on_expires) {
            if (!is_null($event->coupon->expires_at)) {
                if ($event->coupon->expires_at <= Carbon::now()->timestamp) {
                    $freshCoupon = $event->coupon->fresh();
                    if ($freshCoupon && $freshCoupon->expires_at <= Carbon::now()->timestamp) {
                        $freshCoupon->delete();
                    }
                }
            }
        }

        if ($this->delete_coupon_on_uses_reached) {
            // Use fresh coupon data to avoid race conditions with concurrent increments.
            $freshCoupon = $event->coupon->fresh();
            if ($freshCoupon && $freshCoupon->max_uses !== -1 && $freshCoupon->uses >= $freshCoupon->max_uses) {
                $freshCoupon->delete();
            }
        }
    }

    /**
     * Increments the use of a coupon.
     * Uses atomic increment to ensure thread-safety.
     *
     * @param \App\Events\CouponUsedEvent  $event
     */
    private function incrementUses(CouponUsedEvent $event)
    {
        $event->coupon->increment('uses');
    }
}
