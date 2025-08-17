<?php

namespace App\Listeners;

use App\Events\LowStockDetected;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendLowStockNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(LowStockDetected $event): void
    {
        //log to insure event listener is working
        Log::info('Low stock detected for item: ' . $event->stock->inventoryItem->name .
            ' in warehouse: ' . $event->stock->warehouse->name .
            '. Current quantity: ' . $event->stock->quantity);

        // Example of what you would do in a real application:
        // Mail::to('admin@example.com')->send(new LowStockMail($event->stock));
    }
}
