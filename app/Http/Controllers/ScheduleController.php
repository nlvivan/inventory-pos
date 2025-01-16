<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $schedules = Schedule::query()
            ->paginate();

        return Inertia::render('Admin/Schedule', [
            'records' => $schedules,
            'filters' => $request->only(['search']),
        ]);
    }

    public function update(Request $request, Schedule $schedule)
    {
        $request->validate([
            'is_enabled' => 'required|boolean',
        ]);

        $schedule->update([
            'is_enabled' => $request->is_enabled,
        ]);

        return redirect()->back();
    }
}
