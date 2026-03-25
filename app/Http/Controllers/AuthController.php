<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\UserType;
use App\UserStatus;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Helpers\CMail;
use Illuminate\Support\Facades\DB;
use Pdo\Firebird;
use function PHPUnit\Framework\returnArgument;

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
                $request->session()->regenerateToken();
                return redirect()->route('admin.login')
                    ->with('fail', 'Your account is currently pending approval. Please, Check your email for further instruction or contact support at (support@larablog.test) assistance ');
            }
            // success login
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('admin.login')
                ->withInput()
                ->with('fail', 'Incorrect login credentials.Please contact support (suport@larablog.com).');
        }
    }

    public function sendPasswordResetlink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => 'The :attribute is required',
            'email.email' => 'Invalid email address',
            'email.exists' => 'We can not find a user with this email address'
        ]);

        //Get User Details
        $user = User::where('email', $request->email)->first();

        //Generate Token
        $token = base64_encode(Str::random(64));

        //Check if there is an existing token
        $oldToken = DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->first();

        if ($oldToken) {
            DB::table('password_reset_tokens')
                ->where('email', $user->email)
                ->update([
                    'token' => $token,
                    'created_at' => Carbon::now()
                ]);
        } else {
            DB::table('password_reset_tokens')->insert([
                'email' => $user->email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]);
        }

        //Create Calickable action link
        $actionLink = route('admin.reset_password_form', ['token' => $token]);

        $data = array(
            'actionlink' => $actionLink,
            'user' => $user
        );

        // Ensure the view file exists and correct the path
        $mail_body = view('email-templates.forgot-template', $data)->render();

        $mailConfig = array(
            'recipient_address' => $user->email,
            'recipient_name' => $user->name,
            'subject' => 'Reset Password',
            'body' => $mail_body
        );

        // Correct the route and method chaining
        if (CMail::send($mailConfig)) {
            return redirect()->route('admin.forgot')
                ->with('success', 'We have e-mailed your password reset link.');
        } else {
            return redirect()->route('admin.forgot')
                ->with('fail', 'Failed to send the password reset link. Please try again.');
        }
    }

    public function resetForm(Request $request, $token = null)
    {
        //Check if this token is exists
        $isTokenExists = DB::table('password_reset_tokens')
            ->where('token', $token)
            ->first();

        if (!$isTokenExists) {
            return redirect()->route('admin.forgot')->with('fail', 'Invalid token,Request another reset password link.');
        } else {
            $data = [
                'pageTitle' => 'Reset Password',
                'token' => $token
            ];
            return view('back.pages.auth.reset', $data);
        }
    }

    //End method
    public function resetPasswordHandler(Request $request)
    {
        $request->validate([
            'new_password' => 'required|min:5|required_with:new_password_confirmation|same:new_password_confirmation',
            'new_password_confirmation' => 'required'
        ]);

        $dbToken = DB::table('password_reset_tokens')
            ->where('token', $request->token)
            ->first();

        $user = User::where('email', $dbToken->email)->first();

        User::where('email', $user->email)->update([
            'password' => Hash::make($request->new_password)
        ]);

        //Send notification email to this email address that contains new password

        $data = array(
            'user' => $user,
            'new_password' => $request->new_password
        );
        $mail_body = view('email-templates.password-change-template', $data)->render();

        $mailConfig = array(
            'recipient_address' => $user->email,
            'recipient_name' => $user->name,
            'subject' => 'Password Changed',
            'body' => $mail_body
        );

        if (CMail::send($mailConfig)) {
            DB::table('password_reset_tokens')->where([
                'email' => $dbToken->email,
                'token' => $dbToken->token
            ])->delete();

            return redirect()->route('admin.login')->with('succes', 'Done!,Your password has been Changed successfully.Use ypur new password for login into system.');
        } else {
            return redirect()->route('admin.reset_password_form', ['token' => $dbToken->token])->with('fail', 'Something went wrong. Try Again later');
        }

    }

}
