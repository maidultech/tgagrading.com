<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WellcomeMail;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class SocialLoginController extends Controller
{
    // Redirect to provider (Google/Facebook)
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    // Handle the provider callback
    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
            DB::beginTransaction();

            // Check if the user already exists
            $user = User::whereRaw('lower(email) = ?', strtolower($socialUser->getEmail()))->first();

            // Prepare user data
            $username = explode(' ', $socialUser->getName());

            $max_code = User::max('user_code');
            $user_code = max($max_code, 1000) + 1;
            $userData = [
                'name' => $username[0],
                'last_name' => $username[1] ?? '',
                'username' => strtolower(str_replace([' ', ' -'], '_', $username[0])) . '_' . rand(1000, 9999),
                'email' => $socialUser->getEmail(),
                'user_code' => $user_code,
                'password' => Hash::make(rand(100000, 999999)),
                'provider' => $provider,
                'email_verified_at' => now(),
                'provider_id' => $socialUser->getId(),
            ];

            if (!empty($user)) {
                // Check if user already linked to the provider
                if ($user->provider == $provider && $user->provider_id == $socialUser->getId()) {
                    // Existing user, proceed with login
                    Auth::login($user);
                } else {
                    $user->update([
                        'provider' => $provider,
                        'provider_id' => $socialUser->getId(),
                    ]);
                    Auth::login($user);
                }
            } else {
                // Register new user
                $newUser = User::create($userData);
                
                try{
                    Mail::to($newUser->email)->send(new WellcomeMail($newUser->toArray()));
                    
                }catch(Exception $e){
                    Log::alert('Mail not sent');
                }
                session()->put('verified_user', [
                    'id' => $newUser->id,
                    'email' => $newUser->email, // Optionally hash this for privacy
                    'username' => $newUser->username,
                    'verified_at' => $newUser->created_at,
                    'registration_date' => now()->toDateString(), // Current date
                ]);

                Auth::login($newUser);
            }

            DB::commit();
            if(session('redirect.checkout.itemType')){
                return redirect(session()->pull('redirect.checkout.itemType'));
            }
            return redirect()->route('user.dashboard');
        } catch (Exception $e) {
            DB::rollBack();
            Toastr::error('Something went wrong. Please try again.', 'Error');
        }

        return redirect()->route('login');
    }

    public function generateUniqueUsername($firstName)
    {
        // Sanitize the first name (remove special characters, spaces, etc.)
        $baseUsername = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($firstName));

        // Check if the username already exists in the database
        $username = $baseUsername;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            // Append a number to the username if it exists
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }
}
