<?php

namespace App\Http\Controllers;

use App\Models\User;
use Arrocy\Saas\Models\Paygate;
use Arrocy\Saas\Models\Pricing;
use Arrocy\Saas\SaasServiceProvider;
use Arrocy\Saas\Services\PaymentCheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Throwable;

class ProfileController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        if (class_exists(SaasServiceProvider::class)) {
            $subscription = $user->subscription;
            $orders = $user->orders()->orderBy('created_at', 'desc')->paginate(10);
            $paygates = Paygate::where('enabled', true)->get();
            $pricings = Pricing::where('enabled', true)->where('name', '!=', 'free')->get();
            $pricingFeatures = [];
            foreach ($pricings as $pricing) {
                $pricingFeatures[$pricing->name] = setting('pricing_features_'.$pricing->name, []);
            }
        }

        return view('profile.index', [
            'user' => $user,
            'subscription' => $subscription ?? null,
            'orders' => $orders ?? null,
            'paygates' => $paygates ?? null,
            'pricings' => $pricings ?? null,
            'pricingFeatures' => $pricingFeatures ?? null,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->getKey())],
            'phone' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'url:http,https'],
            'timezone' => ['nullable', 'string', 'max:255'],
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['required', 'confirmed', Password::defaults()];
        }

        $validated = $request->validate($rules);

        $emailChanged = $user->email !== $validated['email'];
        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->fill($validated);
        $user->save();

        if ($emailChanged) {
            $user->sendEmailVerificationNotification();

            return redirect()->route('verification.notice')->with('info', __('A fresh verification link has been sent to your email address.'));
        }

        return redirect()->route('profile')->with('success', __('Profile updated successfully.'));
    }

    public function destroy(Request $request, PaymentCheckoutService $checkoutService): RedirectResponse
    {
        $user = $request->user();
        abort_unless(
            setting('user_delete_account_enabled') && ! $user->hasRole('admin'),
            403,
            __('Account deletion is not available for this account.'),
        );

        $request->validate([
            'password' => ['required', 'current_password'],
            'confirm_deletion' => ['accepted'],
        ]);

        if (class_exists(SaasServiceProvider::class)) {
            $subscription = $user->subscription;
            if ($subscription?->gateway_subscription_id && ! $subscription->cancel_at_period_end) {
                try {
                    $checkoutService->cancelSubscription($subscription->paygate, $subscription);
                } catch (Throwable $exception) {
                    Log::error('Could not cancel gateway subscription before account deletion.', [
                        'user_id' => $user->getKey(),
                        'paygate_id' => $subscription->paygate_id,
                        'subscription_id' => $subscription->getKey(),
                        'exception' => $exception::class,
                    ]);

                    return back()->with('error', __('Unable to cancel the subscription with the payment provider. Your account has not been deleted.'));
                }
            }
        }

        DB::table('password_reset_tokens')->where('email', $user->email)->delete();
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', __('Your account and associated data have been permanently deleted.'));
    }
}
