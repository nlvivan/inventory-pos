<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\ProductionBatch;
use App\Models\User;
use Illuminate\Console\Command;

class NotifyUserForExpiryDateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify-user-for-expiry-date-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify users when a product’s production batch is nearing its expiration date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Fetch users with the 'admin' role
        $users = User::role(['admin'])->get();

        $startOfMonth = date('Y-m-01');
        $endOfMonth = date('Y-m-t');

        ProductionBatch::query()
            ->with(['product'])
            ->whereBetween('expiration_date', [$startOfMonth, $endOfMonth])
            ->each(function (ProductionBatch $productionBatch) use ($users) {

                $users->each(function (User $user) use ($productionBatch) {
                    $data = [
                        'batch_number' => $productionBatch->batch_number,
                        'product_name' => $productionBatch->product->name,
                        'product_id' => $productionBatch->product->id,
                        'expiry_date' => $productionBatch->expiration_date,
                    ];

                    // Create a notification for each admin
                    Notification::create([
                        'type' => 'expiry_date',
                        'user_id' => $user->id,
                        'data' => $data,
                        'read' => false,
                    ]);
                });
            });
    }
}
