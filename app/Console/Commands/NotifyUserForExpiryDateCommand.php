<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\Product;
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

        $nearlyExpiredProducts = Product::query()
            ->with('productionBatch')
            ->whereHas('productionBatch', function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('expiration_date', [$startOfMonth, $endOfMonth]);
            })
            ->each(function (Product $product) use ($users) {

                logger($product->productionBatch);

                $users->each(function (User $user) use ($product) {
                    $data = [
                        'batch_number' => $product->productionBatch->batch_number,
                        'product_name' => $product->name,
                        'product_id' => $product->id,
                        'expiry_date' => $product->productionBatch->expiration_date,
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
