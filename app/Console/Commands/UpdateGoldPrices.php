<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoldPriceService;

class UpdateGoldPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gold:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch latest gold prices from global API and update system rates';

    /**
     * Execute the console command.
     */
    public function handle(GoldPriceService $service)
    {
        $this->info('Fetching latest gold prices...');
        
        if ($service->updatePricesFromApi()) {
            $this->info('Gold prices updated successfully.');
        } else {
            $this->error('Failed to update gold prices. Check logs for details.');
        }
    }
}
