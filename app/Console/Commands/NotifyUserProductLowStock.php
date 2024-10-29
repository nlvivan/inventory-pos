<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\Product;
use App\Models\User;
use Illuminate\Console\Command;

class NotifyUserProductLowStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify-user-product-low-stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::role(['admin'])->get();

        Product::query()
            ->each(function (Product $product) use ($users) {

                if ($product->stock->stock <= $product->stock->critical_stock) {

                    $users->each(function (User $user) use ($product) {
                        $data = [
                            'product_name' => $product->name,
                            'product_id' => $product->id,
                            'remaining_stock' => $product->stock->stock,
                        ];

                        Notification::create([
                            'type' => 'low_stock',
                            'user_id' => $user->id,
                            'data' => $data,
                            'read' => false,
                        ]);
                    });
                }
            });
    }
}
