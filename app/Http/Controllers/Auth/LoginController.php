<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        if (Auth::attempt($this->credentials($request), $request->boolean('remember'))) {
            $user = Auth::user();

            // Block IMS accounts
            if (($user->source ?? 'cs') === 'ims') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'This account was created in the Inventory Management System. Please log in there instead.',
                ])->withInput($request->only('email', 'remember'));
            }

            // Block pending accounts
            if (($user->status ?? 'active') === 'pending') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account is pending approval by an administrator. Check your email for updates.',
                ])->withInput($request->only('email', 'remember'));
            }

            // Handle deactivated accounts — store user ID in session BEFORE logging out
            if (!$user->is_active) {
                $userId = $user->id;

                // Logout first clears session, so we regenerate after
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Start fresh session and store the reactivation user ID
                $request->session()->put('pending_reactivation_user_id', $userId);
                $request->session()->save();

                return redirect()->route('login')
                    ->with('show_reactivate_modal', true);
            }

            $request->session()->regenerate();
            return redirect()->intended($this->redirectPath());
        }

        return $this->sendFailedLoginResponse($request);
    }
}