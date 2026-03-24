<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\UserType;
use App\UserStatus;

class AuthController extends Controller
{
    public function loginForm()
    {
        $data = [
            'pageTitile' => 'Login'
        ];
        return view('back.pages.auth.login', $data);
    }

    public function forgotForm()
    {
        $data = [
            'pageTitle' => 'Forgot Password'
        ];
        return view('back.pages.auth.forgot', $data);
    }

    public function loginHandler(Request $request)
    {
        $fieldType = filter_var($request->login_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if ($fieldType == 'email') {
            $request->validate([
                'login_id' => 'required|email|exists:users,email',
                'password' => 'required|min:5'
            ], [
                'login_id.required' => 'Enter your email or username',
                'login_id.email' => 'Invalid Email address',
                'login_id.exists' => 'No account found for this email',
            ]);
        } else {
            $request->validate([
                'login_id' => 'required|exists:users,username',
                'password' => 'required|min:5'
            ], [
                'login_id.required' => 'Enter your username or email',
                'login_id.exists' => 'No account found for this username'
            ]);
        }

        $creds = [
            $fieldType => $request->login_id,
            'password' => $request->password,
        ];

        if (Auth::attempt($creds)) {

            // inactive account
            if (auth()->user()->status == UserStatus::Inactive) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('admin.login')
                    ->with('fail', 'Your account is inactive. Please contact support (suport@larablog.com).');
            }

            // pending account
            if (auth()->user()->status == UserStatus::Pending) {
                Auth::logout();
                $request->session()->invalidate();

                return redirect()->route('admin.login')
                    ->with('fail', 'Your account is pending approval.');
            }

            // success login
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('admin.login')
            ->withInput()
            ->with('fail', 'Incorrect login credentials.Please contact support (suport@larablog.com).');
    }
}
