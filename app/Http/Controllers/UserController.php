<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $users = User::query()
            ->search($request->string('search')->toString())
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('user.index', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::defaults()],
        ]);

        User::create($validated);

        return redirect()->route('user.index')->with('success', __('User created successfully.'));
    }

    public function edit(User $user): View
    {
        return view('user.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'phone' => ['nullable', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'string', Rule::in(['active', 'suspended'])],
            'timezone' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'url:http,https'],
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

        return redirect()->route('user.index')->with('success', __('User updated successfully.'));
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_if($request->user()->is($user), 403, __('You cannot delete your own account.'));

        $user->delete();

        return redirect()->route('user.index')->with('success', __('User deleted successfully.'));
    }

    public function bulkDelete(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', Rule::exists('users', 'id')],
        ]);

        if (in_array($request->user()->getKey(), $validated['ids'])) {
            return back()->withErrors(['ids' => __('You cannot delete your own account from this list.')]);
        }

        DB::transaction(function () use ($validated) {
            User::whereIn('id', $validated['ids'])->delete();
        });

        return redirect()->route('user.index')->with('success', __('Selected users were deleted.'));
    }

    public function export(): StreamedResponse
    {
        return \Helper::csvDownload(
            'users.csv',
            ['ID', 'Name', 'Email', 'Created at'],
            User::query()->orderBy('id')->cursor()->map(fn (User $user) => [
                $user->getKey(),
                $user->name,
                $user->email,
                $user->created_at?->toDateTimeString(),
            ]),
        );
    }

    public function impersonate(User $user): RedirectResponse
    {
        Auth::user()->impersonate($user);

        return to_route('dashboard')->with('info', __('Successfully impersonating :user', ['user' => $user->name]));
    }

    public function impersonateLeave(): RedirectResponse
    {
        Auth::user()->leaveImpersonation();

        return to_route('user.index')->with('info', __('Successfully left impersonation'));
    }
}
