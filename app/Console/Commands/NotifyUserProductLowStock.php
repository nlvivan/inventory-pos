<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\Product;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class NotifyUserProductLowStock extends Command
{
    protected $signature = 'app:notify-user-product-low-stock';

    protected $description = 'Notify admins when a product has low stock';

    public function handle()
    {
        $users = User::role('admin')->get();

        Product::withSum('stocks', 'stock')
            ->having('stocks_sum_stock', '<', DB::raw('critical_stock'))
            ->each(function (Product $product) use ($users) {
                $users->each(function (User $user) use ($product) {
                    $data = [

                        'product_name' => $product->name,
                        'product_id' => $product->id,
                        'remaining_stock' => $product->stocks_sum_stock,
                    ];
                    Notification::create([
                        'type' => 'low_stock',
                        'user_id' => $user->id,
                        'data' => $data,
                        'read' => false,
                    ]);
                });
            });
    }
}
