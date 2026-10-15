<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CustomerSessionController extends Controller
{
    public function create(): View|RedirectResponse
    {
        return Auth::guard('customer')->check() ? redirect()->route('orders.index') : view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('orders.index');
        }
        if (is_string($request->input('email'))) {
            $request->merge(['email' => Str::lower(trim($request->input('email')))]);
        }
        $credentials = $request->validate(['email' => ['required', 'email', 'max:255'], 'password' => ['required', 'string']]);
        if (! Auth::guard('customer')->attempt([...$credentials, 'is_admin' => false])) {
            throw ValidationException::withMessages(['email' => 'Email atau password pembeli salah.']);
        }
        $request->session()->regenerate();
        Auth::guard('customer')->user()->claimGuestOrders($request->session()->get('order_references', []));
        $request->session()->forget('order_references');

        return redirect()->intended(route('orders.index'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('customer')->logout();
        $request->session()->forget(['checkout', 'order_references', 'url.intended']);
        $request->session()->regenerate(true);
        $request->session()->regenerateToken();

        return redirect()->route('customer.login');
    }
}
