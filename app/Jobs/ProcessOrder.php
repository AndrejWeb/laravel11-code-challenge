<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;

class ProcessOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $adminEmail = config('mail.to');
        $subject = 'Order Processed';
        $message = 'Order with ID ' . $this->order->id . ' and total ' . $this->order->total . ' has been processed.';

        // Send email
        Mail::raw($message, function ($message) use ($adminEmail, $subject) {
            $message->to($adminEmail)->subject($subject);
        });

        // Update the order status to processed
        $this->order->update(['status' => 'processed']);
    }
}
