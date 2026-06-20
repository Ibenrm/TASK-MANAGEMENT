<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\File;

class AuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')
                ->setHttpClient(new \GuzzleHttp\Client(['verify' => false]))
                ->user();
            
            // Generate filename based on date and unique google id
            $date = date('YmdHis');
            $filename = "{$date}{$googleUser->id}.jpg";
            $directory = public_path('assets/customer');
            $filepath = $directory . '/' . $filename;
            
            // Ensure directory exists
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            // Download and save avatar
            if ($googleUser->avatar) {
                $avatarContent = Http::withoutVerifying()->get($googleUser->avatar)->body();
                File::put($filepath, $avatarContent);
            }

            // DB Transaction to ensure both user and profile are created safely
            $user = DB::transaction(function () use ($googleUser, $filename) {
                // Find or create user
                $user = User::firstOrCreate(
                    ['email' => $googleUser->email],
                    ['full_name' => $googleUser->name]
                );

                // Update or create user profile
                DB::table('user_profiles')->updateOrInsert(
                    ['user_id' => $user->id],
                    ['avatar_url' => "assets/customer/{$filename}"]
                );

                return $user;
            });

            // Login user
            Auth::login($user);

            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error($e);
            return redirect('/')->withErrors(['error' => 'Authentication failed. Please try again.']);
        }
    }

    public function logout(Request $request)
    {
        \Illuminate\Support\Facades\Cache::forget('user_context_' . Auth::id());
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
