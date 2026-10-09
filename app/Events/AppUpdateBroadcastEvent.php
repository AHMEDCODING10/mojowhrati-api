<?php

namespace App\Events;

use App\Models\AppUpdate;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppUpdateBroadcastEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly AppUpdate $appUpdate) {}

    public function broadcastOn(): array
    {
        return [new Channel('app-updates')];
    }

    public function broadcastAs(): string
    {
        return 'app.update.published';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->appUpdate->id,
            'version_number' => $this->appUpdate->version_number,
            'release_notes' => $this->appUpdate->release_notes,
            'target_audience' => $this->appUpdate->target_audience,
            'download_url' => $this->appUpdate->download_url,
            'play_store_url' => $this->appUpdate->play_store_url,
            'is_force_update' => $this->appUpdate->is_force_update,
            'published_at' => now()->toIso8601String(),
        ];
    }
}
