<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminSessionController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (Auth::guard('web')->user()?->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);
        if (! Auth::guard('web')->attempt([...$credentials, 'is_admin' => true])) {
            throw ValidationException::withMessages(['email' => 'Email atau password admin salah.']);
        }
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->forget('url.intended');
        $request->session()->regenerate(true);
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
