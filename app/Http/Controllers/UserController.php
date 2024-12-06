<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\SendCredsToUserNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $request->mergeIfMissing([
            'per_page' => 15,
        ]);

        $records = User::query()
            ->search($request->search)
            ->with(['roles'])
            ->role(['cashier', 'customer'])
            ->latest()
            ->paginate($request->per_page);

        return Inertia::render('Admin/Users', [
            'records' => $records,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|string|max:255|in:customer,cashier',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('password'),
        ]);

        $user->assignRole($request->role);

        $user->notify(new SendCredsToUserNotification);

        return redirect()->back();
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')
                ->ignore($user->id), ],
            'role' => 'required|string|max:255|in:customer,cashier',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        $user->removeRole($user->roles->first());

        $user->assignRole($request->role);

        return redirect()->back();
    }

    public function printPdf(Request $request)
    {
        $imagePath = public_path('assets/login_logo.png');
        $imageData = file_get_contents($imagePath);
        $base64 = base64_encode($imageData);
        $mimeType = mime_content_type($imagePath);
        $dataUrl = 'data:'.$mimeType.';base64,'.$base64;

        $customers = User::role(['customer'])->get();

        $pdf = Pdf::loadView('pdf.users',
            [
                'record' => $customers,
                'logo' => $dataUrl,
            ]);

        return $pdf->stream('users.pdf');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->back();
    }
}
