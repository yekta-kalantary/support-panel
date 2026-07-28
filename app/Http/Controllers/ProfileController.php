<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function updatePassword(
        UpdatePasswordRequest $request,
        ActivityLogger $logger,
    ): RedirectResponse {
        $user = $request->user();

        $user->forceFill([
            'password' => Hash::make($request->validated('password')),
            'auth_version' => $user->auth_version + 1,
        ])->save();

        $request->session()->put('auth_version', $user->auth_version);
        $logger->log('profile.password_updated', $user, request: $request);

        return back()->with('success', 'رمز عبور با موفقیت تغییر کرد.');
    }
}
