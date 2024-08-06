<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAsRead(Request $request, Notification $notification)
    {
        $notification->update([
            'read' => true,
        ]);

        return redirect()->back();
    }
}
