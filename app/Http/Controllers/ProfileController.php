<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Order;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['name'] = $data['first_name'].' '.$data['middle_name'].' '.$data['last_name'];

        $request->user()->fill($data);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return redirect()->back();
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function orders(Request $request)
    {
        $request->mergeIfMissing([
            'per_page' => 15,
        ]);

        $orders = Order::query()
            ->search($request->search5)
            ->with(['orderItems', 'orderItems.product'])
            ->where('customer_id', Auth::user()->id)
            ->latest()
            ->paginate($request->per_page);

        return Inertia::render('Customer/Orders', [
            'orders' => $orders,
            'filters' => $request->only('search'),
        ]);
    }

    public function showOrder(Order $order)
    {
        $record = $order->load([
            'orderItems',
            'orderItems.product',
            'orderItems.product.category',
            'employee',
            'customer',
        ]);

        return Inertia::render('Customer/Order', [
            'record' => $record,
        ]);
    }

    public function showInvoice(Order $order)
    {
        $record = $order->load([
            'orderItems',
            'orderItems.product',
            'orderItems.product.category',
            'employee',
            'customer',
        ]);

        return Inertia::render('Customer/Invoice', [
            'record' => $record,
        ]);
    }
}
