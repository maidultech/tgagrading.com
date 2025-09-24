<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;


    public function __construct()
    {
        $this->middleware('guest:user')->except('logout');
    }

    protected function guard()
    {
        return Auth::guard('user');
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    // protected function attemptLogin(Request $request)
    // {
    //     return $this->guard()->attempt(
    //         array_merge($this->credentials($request),['status' => 1]), $request->boolean('remember')
    //     );
    // }

    public function login(Request $request)
    {
        $setting = getSetting();

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            // 'g-recaptcha-response' => $setting->recaptcha_site_key ? 'required' : 'nullable',
        ], [
            // 'g-recaptcha-response.required' => 'The captcha-response field is required.',
        ]);

        $setting =  Setting::first();
        
        if ($setting->google_recaptcha == '1') {
            $recaptchaResponse = $request->input('g-recaptcha-response');
            $secretKey = $setting->recaptcha_site_secret;
    
            // Verify reCAPTCHA response with Google
            $response = Http::get('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $recaptchaResponse,
            ]);
            $responseBody = $response->json();
            Log::info('reCAPTCHA API Response:', $responseBody);
    
            // Validate reCAPTCHA success and score
            if (!isset($responseBody['success']) || !$responseBody['success']) {
                Toastr::error(trans('Google reCAPTCHA verification failed. Please try again.'), 'Error', ["positionClass" => "toast-top-right"]);
                return back();
            }
    
            if ($responseBody['score'] < 0.3) { // Adjust threshold as needed
                Toastr::error(trans('Suspicious activity detected. Please try again.'), 'Error', ["positionClass" => "toast-top-right"]);
                return back();
            }
        }
        
        if ($this->attemptLogin($request)) {
            return redirect()->intended(route('user.dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => [__('auth.failed')],
        ]);
    }

    protected function attemptLogin(Request $request)
    {
        $loginAttempt = $this->guard()->attempt(
            array_merge($this->credentials($request), ['status' => 1]),
            $request->boolean('remember')
        );

        if ($loginAttempt) {
            $user = $this->guard()->user();

            if ($user && $user->cart_json) {
                $cartItems = json_decode($user->cart_json, true);
                session(['checkout.items' => $cartItems]);
            }
        }

        return $loginAttempt;
    }


    // protected $redirectTo;

    // protected function redirectTo() {

    //     if(auth()->user()->role_id == 1 ) {

    //         Toastr::success('Welcome to Admin Panel :-)','Success');
    //         return route('admin.dashboard');

    //     }elseif(auth()->user()->role_id == 2 ) {

    //         Toastr::success('Welcome to your profile :-)','Success');
    //         return route('user.dashboard');

    //     }else {
    //         $this->redirectTo = route('login');
    //     }

    // }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('guest')->except('logout');
    // }


    public function logout(Request $request)
    {

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/');
    }


}

