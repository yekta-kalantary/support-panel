<?php

namespace App\Http\Controllers\Auth;

use App\Enums\RecordStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email:rfc']]);

        $activeUserExists = User::query()
            ->where('email', (string) $request->string('email')->lower())
            ->where('status', RecordStatus::ACTIVE->value)
            ->exists();

        if ($activeUserExists) {
            Password::sendResetLink(['email' => (string) $request->string('email')->lower()]);
        }

        return back()->with('success', 'در صورت وجود حساب فعال، لینک بازیابی رمز عبور ارسال شد.');
    }
}
