<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests\Admin\LoginRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ForgotPasswordRequest;
use App\Mail\ResetCodeMail;
use App\Models\Admin;
use App\Models\General;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        $logo = General::where('key', 'platform_logo')->value('value');


        if (Auth::guard('admin')->check()) {
            return redirect()->route('home.index', compact('logo'));
        }
        return view('pages.auth.login', compact('logo'));
    }
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');
        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()
                ->intended(route('home.index'))
                ->with('success', __('welcome_back'));
        }

        throw ValidationException::withMessages([
            'email' => __('These credentials do not match our records.'),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', __('logout_success'));
    }


    // Step 1: Show Email Form
    public function showForgotForm()
    {
        $logo = General::where('key', 'platform_logo')->value('value');
        return view('pages.auth.forgot', compact('logo'));
    }

    // Step 2: Handle Email Submission
    // public function submitForgot(Request $request)
    // {
    //     $request->validate(['email' => 'required|email']);
    //     $admin = Admin::where('email', $request->email)->first();

    //     if (!$admin) {
    //         return back()->with('error', __('Email not found.'));
    //     }

    //     $code = rand(100000, 999999);
    //     Session::put('reset_email', $admin->email);
    //     Session::put('reset_code', $code);
    //     Session::put('reset_code_expires_at', now()->addMinutes(5));
    //     // Session::put('reset_code_expires_at', now()->addSeconds(20));


    //     // Send email with code
    //     Mail::to($admin->email)->send(new ResetCodeMail($admin, $code));


    //     return redirect()->route('verify.code.form')->with('success', __('Verification code sent.'));
    // }
    public function submitForgot(Request $request)
    {
        // Stricter email validation helps avoid obvious bad domains
        $request->validate([
            'email' => 'required|email:rfc,dns',
        ]);

        $admin = Admin::where('email', $request->email)->first();
        if (!$admin) {
            throw ValidationException::withMessages([
                'email' => __('No account found with this email address.'),
            ]);
        }

        if (!$admin->is_active) {
            throw ValidationException::withMessages([
                'email' => __('We couldn’t send the verification email. Please make sure the email is active.'),
            ]);
        }
        $code = random_int(100000, 999999);

        try {
            Mail::to($admin->email)->send(new ResetCodeMail($admin, $code));

            // Only persist the code after a successful send
            Session::put('reset_email', $admin->email);
            Session::put('reset_code', $code);
            Session::put('reset_code_expires_at', now()->addMinutes(5));

            return redirect()
                ->route('verify.code.form')
                ->with('success', __('Verification code sent.'));
        } catch (TransportExceptionInterface $e) {
            report($e);

            // $message = __('We couldn’t send the verification email. Please make sure the email is active.');
            // return back()->withInput()->with('error', $message);
            throw ValidationException::withMessages([
                'email' => __('We couldn’t send the verification email. Please make sure the email is active.'),
            ]);
        }
    }
    // Step 3: Show Code Verification Page
    public function showVerifyForm()
    {
        $logo = General::where('key', 'platform_logo')->value('value');
        return view('pages.auth.verify-code', compact('logo'));
    }

    // Step 4: Submit Verification Code
    public function submitVerify(Request $request)
    {
        $request->validate(['code' => 'required']);

        $expiresAt = Session::get('reset_code_expires_at');

        if (!$expiresAt || now()->greaterThan($expiresAt)) {
            Session::forget(['reset_code', 'reset_code_expires_at', 'reset_email']);
            throw ValidationException::withMessages([
                'code' => __('Verification code expired. Please request a new one.'),
            ]);
        }

        if ($request->code != Session::get('reset_code')) {
            throw ValidationException::withMessages([
                'code' => __('Invalid verification code.'),
            ]);
        }

        return redirect()->route('reset.form');
    }


    // Step 5: Show Reset Password Page
    public function showResetForm()
    {
        $logo = General::where('key', 'platform_logo')->value('value');
        return view('pages.auth.reset-password', compact('logo'));
    }

    // Step 6: Handle Password Reset
    public function submitReset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $email = Session::get('reset_email');
        $admin = Admin::where('email', $email)->first();

        if (!$admin) {
            return redirect()->route('forgot.form')->with('error', __('Session expired. Try again.'));
        }

        $admin->update(['password' => Hash::make($request->password)]);
        Session::forget(['reset_email', 'reset_code']);

        return redirect()->route('login')->with('success', __('Password updated. You may now log in.'));
    }
}
