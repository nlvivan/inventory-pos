<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\Product;
use App\Models\User;
use Illuminate\Console\Command;

class NotifyUserProductLowStock extends Command
{
    protected $signature = 'app:notify-user-product-low-stock';

    protected $description = 'Notify admins when a product has low stock';

    public function handle()
    {
        $users = User::role('admin')->get();

        Product::with(['stock', 'productionBatch'])
            ->whereHas('stock', function ($query) {
                $query->whereColumn('stock', '<=', 'critical_stock');
            })
            ->chunk(100, function ($products) use ($users) {
                logger($products);
                foreach ($products as $product) {
                    // Defensive check (in case of missing relationships)
                    if (! $product->stock || ! $product->productionBatch) {
                        continue;
                    }

                    $data = [
                        'batch_number' => $product->productionBatch->batch_number,
                        'product_name' => $product->name,
                        'product_id' => $product->id,
                        'remaining_stock' => $product->stock->stock,
                    ];

                    foreach ($users as $user) {
                        Notification::create([
                            'type' => 'low_stock',
                            'user_id' => $user->id,
                            'data' => $data,
                            'read' => false,
                        ]);
                    }
                }
            });
    }
}
