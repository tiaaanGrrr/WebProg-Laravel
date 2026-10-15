<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CustomerRegistrationController extends Controller
{
    public function create(): View|RedirectResponse
    {
        return Auth::guard('customer')->check() ? redirect()->route('orders.index') : view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('orders.index');
        }
        if (is_string($request->input('email'))) {
            $request->merge(['email' => Str::lower(trim($request->input('email')))]);
        }
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:72', 'confirmed'],
        ]);
        $user = DB::transaction(function () use ($data, $request): User {
            $user = new User($data);
            $user->is_admin = false;
            $user->save();
            $user->claimGuestOrders($request->session()->get('order_references', []));

            return $user;
        });
        Auth::guard('customer')->login($user);
        $request->session()->regenerate();
        $request->session()->forget('order_references');

        return redirect()->intended(route('orders.index'));
    }
}
