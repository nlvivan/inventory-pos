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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::role(['admin'])->get();

        Product::query()
            ->each(function (Product $product) use ($users) {

                if ($product->expiry_date <= now()->addWeek()) {

                    $users->each(function (User $user) use ($product) {
                        $data = [
                            'product_name' => $product->name,
                            'product_id' => $product->id,
                            'expiry_date' => $product->expiry_date,
                        ];

                        Notification::create([
                            'type' => 'expiry_date',
                            'user_id' => $user->id,
                            'data' => $data,
                            'read' => false,
                        ]);
                    });
                }
            });
    }
}
