<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Verify;
use App\Models\User;
use Carbon\Carbon;

class LoginController extends Controller
{
    public function create()
    {
        if (auth()->user()) {
            return redirect()->route('main');
        }
        $hide_cat_bar = 1;
        return Inertia::render('Auth/LoginPhone', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
            'hide_cat_bar' =>1 ,
            'redirect_url' => request('redirect_url'),
        ]);
    }

     /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {  
        $request->authenticate();

        $user = auth()->user();

        $currentIp = $request->ip();
        $currentAgent = $request->userAgent();
        $currentFingerprint = $request->fingerprint;

        if (!$user->last_login_at || 
            Carbon::parse($user->last_login_at)->addDays(7) < now() ||
            (
                $user->last_fingerprint != $currentFingerprint &&
                $user->last_login_ip != $currentIp
            )
        ) {
            Auth::guard('web')->logout();

            $email = $user->email;

            $code = generateCode(6);
            
            $res = sendEmail($code, $email);
            
            if (!$res) {
                $data = array("status"=> 0);
                return redirect()->back()->with('message', 'もう一度メールアドレスを入力してください！')->with('title', '入力エラー')->with('type', 'dialog')->with('data', $data);
            }
            // $code = '123456';

            Verify::where('to', $email)->where('status', 0)->update(['status'=>1]);
            $data = array("to"=>$email, 'code'=>$code);
            Verify::create($data);

            $hide_cat_bar = 1;
            return Inertia::render('Auth/EmailVerify', [
                'email' => $email,
                'hide_cat_bar' => $hide_cat_bar,
            ]);
        }
        $request->session()->regenerate();

        $user->last_login_ip = $currentIp;
        $user->last_user_agent = $currentAgent;
        $user->last_login_at = now();
        $user->last_fingerprint = $currentFingerprint;
        $user->save();

        if ($request->redirect_url) {
            return redirect($request->redirect_url);
        }
        return redirect()->intended(RouteServiceProvider::HOME);
    }

    public function verify_email(Request $request) {
        $code = $request->code;
        $email = $request->email;
        $fingerprint = $request->fingerprint;
        $user = User::where('email', $email)->first();
        if (!$user) {
            return redirect()->route('login');
        }
        
        $count = Verify::where('to', $email)->where('code', $code)->where('status', 0)->count();

        if ($count) {
            Verify::where('to', $email)->where('code', $code)->where('status', 0)->update(array('status'=>2));
            
            Auth::login($user);

            $request->session()->regenerate();

            $user->last_login_ip = $request->ip();
            $user->last_user_agent = $request->userAgent();
            $user->last_login_at = now();
            $user->last_fingerprint = $fingerprint;
            $user->save();
            
            return redirect()->intended(RouteServiceProvider::HOME);
        } else {
            $failed_count = Verify::where('to', $user->email)
                ->where('status', 1)
                ->where('created_at', '>=', now()->subHours(1))
                ->count();

            if ($failed_count >= 9) {
                $user->status = 0;
                $user->save();
            }
            
            return redirect()->route('login');
        }
    }

    public function verify_email_get() {
        $email = 'user@example.com';
        $hide_cat_bar = 1;
        return Inertia::render('Auth/EmailVerify', [
            'email' => $email,
            'hide_cat_bar' => $hide_cat_bar,
        ]);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
