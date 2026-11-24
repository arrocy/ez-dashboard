<?php

namespace App\Http\Controllers\Auth;

use Socialite;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SocialiteController extends Controller
{
    public function redirectTo($provider)
    {
        if (Auth::check()) return redirect()->route('dashboard');
        if ($provider === '.env') $provider = 'google';
        $drivers = collect(config('socialite.default'))->pluck('driver')->values()->toArray();
        if (!in_array($provider, $drivers)) return redirect()->route('login');

        return Socialite::driver($provider)->redirect();
    }

    public function handleCallback(Request $request, $provider)
    {
        try {
            $providerUser = Socialite::driver($provider)->user();

            // Safely fetch avatar and raw payload data
            $id = $providerUser->getId();
            $name = $providerUser->getName() ?? $providerUser->getNickname() ?? 'User';
            $avatar = $providerUser->getAvatar();
            $rawPayload = $providerUser->getRaw();
            $phone = $rawPayload['phone_number'] ?? $rawPayload['phone'] ?? null;
            $email = $providerUser->getEmail();

            // Find existing user by socialite ID
            $user = User::where('socialite->' . $provider . '_id', $id)->first();

            // Fallback: search by email if available
            if (!$user && !empty($email)) {
                $user = User::where('email', $email)->first();
            }

            // Create new user if not found
            if (!$user) {
                $allowRegister = setting('user_create_account_enabled') ?? true;
                abort_unless($allowRegister, 403, 'User registration is disabled.');

                // Instagram/X might not return an email
                $fallbackEmail = $email ?: ($id . "@{$provider}.user");

                $user = User::create([
                    'name'              => $name,
                    'email'             => $fallbackEmail,
                    'phone'             => $phone,
                    'avatar'            => $avatar,
                    'status'            => 'active',
                    'timezone'          => 'UTC',
                    'socialite'         => [$provider . '_id' => $id],
                    'email_verified_at' => now(),
                ]);
            } else {
                // Update avatar if missing or changed
                if ($avatar && empty($user->avatar)) {
                    $user->update(['avatar' => $avatar]);
                }
            }

            Auth::login($user);

            return redirect()
                ->route('dashboard')
                ->with(['alert-type' => 'success', 'message' => ucfirst($provider) . ' login successful!']);

        } catch (\Exception $e) {
            return redirect()
                ->route('login')
                ->with(['alert-type' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
