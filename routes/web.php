<?php

use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Integrations;
use App\Livewire\App\BuyCredits;
use App\Livewire\App\ComposeSms;
use App\Livewire\App\Me2U;
use App\Livewire\App\Phonebook;
use App\Livewire\App\SentMessages;
use App\Livewire\App\Settings;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('home');

Route::view('/developers', 'developers')->name('developers');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', Login::class)->name('login');
    Route::get('/signup', Register::class)->name('register');
    Route::get('/forgot-password', ForgotPassword::class)->name('password.request');
    Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/email/verify', fn () => view('auth.verify-email'))->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return redirect()->route('compose');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    })->middleware('throttle:6,1')->name('verification.send');

    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    })->name('logout');
});

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/compose', ComposeSms::class)->name('compose');
    Route::get('/sent', SentMessages::class)->name('sent');
    Route::get('/phonebook', Phonebook::class)->name('phonebook');
    Route::get('/buy', BuyCredits::class)->name('buy');
    Route::get('/me2u', Me2U::class)->name('me2u');
    Route::get('/settings', Settings::class)->name('settings');

    Route::middleware('admin')->group(function (): void {
        Route::get('/admin', Dashboard::class)->name('admin.dashboard');
        Route::get('/admin/integrations', Integrations::class)->name('admin.integrations');
    });
});
