<?php

namespace App\Jobs;

use App\Events\ServerCreatedEvent;
use App\Models\Server;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class HandlePostServerCreationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $userId;
    public string $serverId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId, string $serverId)
    {
        $this->userId = $userId;
        $this->serverId = $serverId;
    }

    /**
     * Execute the job.
     * Fires ServerCreatedEvent to trigger event-driven workflows (Discord roles, notifications, etc.)
     */
    public function handle(): void
    {
        $user = User::find($this->userId);
        $server = Server::find($this->serverId);

        if (! $user || ! $server) {
            Log::warning('Post server creation job: missing user or server', ['user' => $this->userId, 'server' => $this->serverId]);
            return;
        }

        // Fire the ServerCreatedEvent - listeners will handle Discord roles and other post-creation tasks
        event(new ServerCreatedEvent($user, $server));
    }
}
