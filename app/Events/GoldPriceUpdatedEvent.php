<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Broadcast a real-time gold price update to ALL subscribers.
 * Triggered from GoldPriceService after successful API fetch.
 * Flutter clients listen on: public channel 'gold-prices'
 */
class GoldPriceUpdatedEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly array $prices,
        public readonly float $ouncePrice,
        public readonly ?float $usdEgp = null
    ) {}

    /**
     * Public channel — no auth required, all users can receive.
     */
    public function broadcastOn(): array
    {
        return [new Channel('gold-prices')];
    }

    public function broadcastAs(): string
    {
        return 'gold.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'prices'       => $this->prices,
            'ounce_price'  => $this->ouncePrice,
            'usd_egp'      => $this->usdEgp,
            'updated_at'   => now()->toIso8601String(),
        ];
    }
}
