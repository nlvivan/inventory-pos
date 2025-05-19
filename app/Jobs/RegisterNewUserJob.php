<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RegisterNewUserJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected User $user)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        event(new Registered($this->user));
    }
}
