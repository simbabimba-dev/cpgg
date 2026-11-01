<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Webhook extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'secret',
        'events',
        'enabled',
        'last_triggered_at',
    ];

    protected $casts = [
        'events' => 'array',
        'enabled' => 'boolean',
        'last_triggered_at' => 'datetime',
    ];

    /**
     * Whether this webhook is subscribed to the given event.
     */
    public function hasEvent(string $event): bool
    {
        return in_array($event, $this->events ?? [], true);
    }

    /**
     * Mark the webhook as triggered without touching timestamps or events.
     */
    public function markAsTriggered(): void
    {
        $this->forceFill(['last_triggered_at' => now()])->saveQuietly();
    }
}