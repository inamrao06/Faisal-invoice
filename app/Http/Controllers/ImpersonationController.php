<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    public function start(Request $request, User $user): RedirectResponse
    {
        $admin = $request->user();

        abort_unless($admin->isSuperUser() && ! $request->session()->has('impersonation'), 403);
        abort_unless($user->is_active && ! $user->is($admin), 422);

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put('impersonation', [
            'original_user_id' => $admin->id,
            'target_user_id' => $user->id,
        ]);

        return redirect()->route('dashboard');
    }

    public function stop(Request $request): RedirectResponse
    {
        $impersonation = $request->session()->get('impersonation');

        abort_unless(
            is_array($impersonation)
                && ($impersonation['target_user_id'] ?? null) === $request->user()->id,
            403
        );

        $admin = User::find($impersonation['original_user_id'] ?? null);
        if (! $admin || ! $admin->is_active || ! $admin->isSuperUser()) {
            $request->session()->forget('impersonation');
            abort(403);
        }

        Auth::login($admin);
        $request->session()->regenerate();
        $request->session()->forget('impersonation');

        return redirect()->route('users.index');
    }
}
