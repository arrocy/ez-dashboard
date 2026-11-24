<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FilemanagerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::middleware(['web'])->group(function () {
    Route::get('lang/{locale}', function ($locale) {
        if (file_exists(base_path("lang/{$locale}.json"))) {
            Session::put('locale', $locale);
        }

        return back()->with('success', __('Language switched successfully to :locale', ['locale' => $locale]));
    })->name('switch.language');

    Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

    Route::middleware('guest')->group(function () {
        Route::get('register', fn () => view('auth.register'))->name('register');
        Route::post('register', [AuthController::class, 'register']);

        Route::get('login', fn () => view('auth.login'))->name('login');
        Route::post('login', [AuthController::class, 'login']);

        Route::get('login/{provider}', [SocialiteController::class, 'redirectTo'])->name('login.socialite');
        Route::get('login/{provider}/callback', [SocialiteController::class, 'handleCallback']);

        Route::get('forgot-password', fn () => view('auth.forgot-password'))->name('password.request');
        Route::post('forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');

        Route::get('reset-password/{token}', fn (string $token) => view('auth.reset-password', compact('token')))
            ->name('password.reset');
        Route::post('reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');
    });

    Route::middleware('auth')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('email/verify', fn () => view('auth.verify-email'))->name('verification.notice');
        Route::get('email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
            $request->fulfill();

            return redirect()->intended(route('dashboard').'?verified=1');
        })->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
        Route::post('email/verification-notification', function (Request $request) {
            $request->user()->sendEmailVerificationNotification();

            return back()->with('info', __('A fresh verification link has been sent to your email address.'));
        })->middleware('throttle:6,1')->name('verification.send');

        Route::get('password/confirm', fn () => view('auth.confirm-password'))->name('password.confirm');
        Route::post('password/confirm', [AuthController::class, 'confirmPassword']);

        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('/impersonate-leave', [UserController::class, 'impersonateLeave'])->name('impersonate.leave');
    });

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('filemanager', [FilemanagerController::class, 'index'])->name('filemanager.index');
        Route::post('filemanager/upload', [FilemanagerController::class, 'upload'])->name('filemanager.upload');
        Route::post('filemanager/folder', [FilemanagerController::class, 'createFolder'])->name('filemanager.folder');
        Route::get('filemanager/download', [FilemanagerController::class, 'download'])->name('filemanager.download');
        Route::delete('filemanager/item', [FilemanagerController::class, 'delete'])->name('filemanager.delete');

        Route::get('profile', [ProfileController::class, 'index'])->name('profile');
        Route::put('profile/{user}', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    Route::middleware(['auth', 'verified', 'roles:admin'])->group(function () {
        Route::get('settings', [SettingController::class, 'index'])->name('setting.index');
        Route::post('setting', [SettingController::class, 'store'])->name('setting.store');
        Route::get('setting/{setting}/edit', [SettingController::class, 'edit'])->name('setting.edit');
        Route::put('setting/{setting}', [SettingController::class, 'update'])->name('setting.update');
        Route::delete('setting/{setting}', [SettingController::class, 'destroy'])->name('setting.destroy');
        Route::delete('setting', [SettingController::class, 'bulkDelete'])->name('setting.bulk-destroy');
        Route::get('setting/export', [SettingController::class, 'export'])->name('setting.export');

        Route::get('users', [UserController::class, 'index'])->name('user.index');
        Route::post('user', [UserController::class, 'store'])->name('user.store');
        Route::get('user/{user}/edit', [UserController::class, 'edit'])->name('user.edit');
        Route::put('user/{user}', [UserController::class, 'update'])->name('user.update');
        Route::delete('user/{user}', [UserController::class, 'destroy'])->name('user.destroy');
        Route::delete('user', [UserController::class, 'bulkDelete'])->name('user.bulk-destroy');
        Route::get('user/export', [UserController::class, 'export'])->name('user.export');

        Route::get('/impersonate/{user}', [UserController::class, 'impersonate'])->name('impersonate');
    });
});
