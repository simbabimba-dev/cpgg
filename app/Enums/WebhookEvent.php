<?php

namespace App\Enums;

enum WebhookEvent: string
{
    // User events
    case USER_CREATED = 'user.created';
    case USER_UPDATED = 'user.updated';
    case USER_DELETED = 'user.deleted';
    case USER_SUSPENDED = 'user.suspended';
    case USER_UNSUSPENDED = 'user.unsuspended';

    // Server events
    case SERVER_CREATED = 'server.created';
    case SERVER_UPDATED = 'server.updated';
    case SERVER_DELETED = 'server.deleted';
    case SERVER_SUSPENDED = 'server.suspended';
    case SERVER_UNSUSPENDED = 'server.unsuspended';

    // Store and billing events
    case PAYMENT_COMPLETED = 'payment.completed';
    case COUPON_USED = 'coupon.used';
    case PRODUCT_CREATED = 'product.created';
    case PRODUCT_UPDATED = 'product.updated';
    case PRODUCT_DELETED = 'product.deleted';

    // Ticket events
    case TICKET_CREATED = 'ticket.created';
    case TICKET_BLACKLISTED = 'ticket.blacklisted';

    /**
     * Human readable label used in the admin interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::USER_CREATED => __('User created'),
            self::USER_UPDATED => __('User updated'),
            self::USER_DELETED => __('User deleted'),
            self::USER_SUSPENDED => __('User suspended'),
            self::USER_UNSUSPENDED => __('User unsuspended'),
            self::SERVER_CREATED => __('Server created'),
            self::SERVER_UPDATED => __('Server updated'),
            self::SERVER_DELETED => __('Server deleted'),
            self::SERVER_SUSPENDED => __('Server suspended'),
            self::SERVER_UNSUSPENDED => __('Server unsuspended'),
            self::PAYMENT_COMPLETED => __('Payment completed'),
            self::COUPON_USED => __('Coupon used'),
            self::PRODUCT_CREATED => __('Product created'),
            self::PRODUCT_UPDATED => __('Product updated'),
            self::PRODUCT_DELETED => __('Product deleted'),
            self::TICKET_CREATED => __('Ticket created'),
            self::TICKET_BLACKLISTED => __('Ticket blacklisted'),
        };
    }

    /**
     * Group label used to organise events in the admin interface.
     */
    public function group(): string
    {
        return match ($this) {
            self::USER_CREATED, self::USER_UPDATED, self::USER_DELETED,
            self::USER_SUSPENDED, self::USER_UNSUSPENDED => __('Users'),
            self::SERVER_CREATED, self::SERVER_UPDATED, self::SERVER_DELETED,
            self::SERVER_SUSPENDED, self::SERVER_UNSUSPENDED => __('Servers'),
            self::PAYMENT_COMPLETED, self::COUPON_USED, self::PRODUCT_CREATED,
            self::PRODUCT_UPDATED, self::PRODUCT_DELETED => __('Store & Billing'),
            self::TICKET_CREATED, self::TICKET_BLACKLISTED => __('Tickets'),
        };
    }

    /**
     * All supported event values.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Events keyed by group label, each holding value => label pairs.
     *
     * @return array<string, array<string, string>>
     */
    public static function grouped(): array
    {
        $grouped = [];

        foreach (self::cases() as $event) {
            $grouped[$event->group()][$event->value] = $event->label();
        }

        return $grouped;
    }
}
