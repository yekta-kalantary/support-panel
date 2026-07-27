<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request, ActivityLogger $logger): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email:rfc'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $throttleKey = Str::lower($validated['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => "تلاش‌های ورود بیش از حد مجاز است. {$seconds} ثانیه دیگر دوباره تلاش کنید.",
            ]);
        }

        $user = User::query()->where('email', Str::lower($validated['email']))->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => 'اطلاعات ورود صحیح نیست.',
            ]);
        }

        if (! $user->isActive()) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => 'حساب کاربری شما غیرفعال است.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        Auth::login($user, (bool) ($validated['remember'] ?? false));
        $request->session()->regenerate();
        $request->session()->put('auth_version', $user->auth_version);

        $user->forceFill(['last_login_at' => now()])->save();
        $logger->log('auth.login', $user, request: $request);

        return redirect()->intended(
            route($user->isAdmin() ? 'admin.dashboard' : 'portal.dashboard')
        );
    }

    public function destroy(Request $request, ActivityLogger $logger): RedirectResponse
    {
        if ($request->user()) {
            $logger->log('auth.logout', $request->user(), request: $request);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
