<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Show registration form
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle registration
     */
    public function store(Request $request): RedirectResponse
    {
        //  Validate input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'invite_code' => ['required'],
        ]);

        //  Find invite code
        $invite = DB::table('invites')
            ->where('code', $request->invite_code)
            ->first();

        //  Invalid code
        if (!$invite) {
            throw ValidationException::withMessages([
                'invite_code' => 'Invalid invite code.',
            ]);
        }

        //  Expired check
        if ($invite->expires_at && now()->gt($invite->expires_at)) {
            throw ValidationException::withMessages([
                'invite_code' => 'Invite code expired.',
            ]);
        }

        //  Usage limit check
        if ($invite->max_usages && $invite->uses >= $invite->max_usages) {
            throw ValidationException::withMessages([
                'invite_code' => 'Invite code already used.',
            ]);
        }

        //  Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        //  Increment invite usage
        DB::table('invites')
            ->where('code', $request->invite_code)
            ->increment('uses');

        //  Trigger event
        event(new Registered($user));

        //  Login user
        Auth::login($user);

        return redirect()->route('dashboard');
    }
}