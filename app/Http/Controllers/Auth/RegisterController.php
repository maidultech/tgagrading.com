<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Mail\WellcomeMail;
use App\Models\User;
use App\Models\Plan;
use App\Models\Setting;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function register(Request $request)
    {
        $setting = Setting::first();
    
        // reCAPTCHA validation FIRST
        if ($setting && $setting->google_recaptcha == '1') {
            $recaptchaResponse = $request->input('g-recaptcha-response');
            $secretKey = $setting->recaptcha_site_secret;
    
            $response = Http::get('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $recaptchaResponse,
            ]);
    
            $responseBody = $response->json();
    
            if (!isset($responseBody['success']) || !$responseBody['success']) {
                Toastr::error('Google reCAPTCHA verification failed. Please try again.');
                return back()->withInput();
            }
    
            if ($responseBody['score'] < 0.3) {
                Toastr::error('Suspicious activity detected. Please try again.');
                return back()->withInput();
            }
        }
    
        // Now validate form
        $this->validator($request->all())->validate();
    
        // Create user
        event(new Registered($user = $this->create($request->all())));
    
        // Log in
        $this->guard()->login($user);
    
        // Redirect
        return $this->registered($request, $user)
                    ?: redirect($this->redirectPath());
    }
    
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $setting = getSetting();

        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8','confirmed',Password::min(8)->mixedCase()->symbols()->numbers()],
            // 'g-recaptcha-response' => $setting->recaptcha_site_key ? ['required'] : ['nullable'],
        ],[
            // 'g-recaptcha-response.required' => 'The captcha-response field is required.',
            'password.mixed' => '1 uppercase and 1 lowercase is required',
            'password.symbols' => '1 special character is required',
            'password.numbers' => '1 number is required',
        ]);

    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {

        $username = strtolower(str_replace([' ', ' -'], '_', $data['first_name']));
        $check = User::where('username',$username)->first();
        $max_code = User::max('user_code');
        $user_code = max($max_code, 1000) + 1;
        $setting = getSetting();
        $email_verified_at = NULL;
        if($setting->email_verification == 2){
            $email_verified_at = now();
        }

        if($check){
            $username = $username.$user_code;
        }
      
      
        // try{
        //     Mail::to($data['email'])->send(new WellcomeMail($data));
        // }catch(Exception $e){
        //     Log::alert('Welcome mail not sent. Error: ' . $e->getMessage());
        // }

        $current_plan_id = 0;


        $user =  User::create([
            'name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'category_id' => $data['category_id'],
            'dob' => $data['dob'] ? Carbon::createFromFormat('Y-m-d', $data['dob']) : null,
            'username' => $username,
            'user_code' => $user_code,
            'password' => bcrypt($data['password']),
            'current_plan_id' => $current_plan_id,
            'email_verified_at' => $email_verified_at,
        ]);

       session()->put('verified_user', [
            'id' => $user->id,
            'email' => $user->email, // Optionally hash this for privacy
            'username' => $user->username,
            'verified_at' => $user->created_at,
            'registration_date' => now()->toDateString(), // Current date
        ]);

        return $user;

    }
}
