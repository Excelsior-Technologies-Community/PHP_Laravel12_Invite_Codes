<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\Invite;
use App\Models\User;
use App\Mail\InviteMail;
use Carbon\Carbon;

class InviteController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $search = $request->search;

        $query = Invite::where('invited_by', $user->id);

        if ($search) {
            $query->where('code', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        $invites = $query->orderBy('created_at', 'desc')->paginate(10);

        $totalCodes = Invite::where('invited_by', $user->id)->count();
        $activeCodes = Invite::where('invited_by', $user->id)
            ->whereColumn('uses', '<', 'max_uses')
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', Carbon::now());
            })
            ->count();
        $usedCodes = Invite::where('invited_by', $user->id)
            ->where('uses', '>', 0)
            ->count();
        $expiredCodes = Invite::where('invited_by', $user->id)
            ->where(function($q) {
                $q->whereColumn('uses', '>=', 'max_uses')
                  ->orWhere('expires_at', '<=', Carbon::now());
            })
            ->count();

        $referrals = User::where('referred_by', $user->id)->count();

        return view('invites.index', compact(
            'invites',
            'totalCodes',
            'activeCodes',
            'usedCodes',
            'expiredCodes',
            'referrals',
            'search'
        ));
    }

   public function create(Request $request)
{
    $request->validate([
        'max_uses' => 'nullable|integer|min:1|max:100',
        'expires_in' => 'nullable|integer|min:1|max:720',
        'email' => 'nullable|email',
        'send_email' => 'nullable|boolean'
    ]);

    $maxUses = (int) ($request->max_uses ?? 1);
    $expiresIn = $request->expires_in ? (int) $request->expires_in : null;
    $email = $request->email;

    $expiresAt = null;
    if ($expiresIn) {
        $expiresAt = now()->addHours($expiresIn);
    }

    $invite = Invite::create([
        'code' => strtoupper(Str::random(10)),
        'max_uses' => $maxUses,
        'uses' => 0,
        'email' => $email,
        'expires_at' => $expiresAt,
        'invited_by' => auth()->id()
    ]);

    if ($request->send_email && $email) {
        Mail::to($email)->send(new InviteMail($invite, auth()->user()->name));
        $message = 'Invite code created and email sent successfully!';
    } else {
        $message = 'Invite code created successfully!';
    }

    return redirect()->back()->with('success', $message);
}

    public function sendEmail(Request $request, $id)
    {
        $invite = Invite::where('id', $id)
            ->where('invited_by', auth()->id())
            ->firstOrFail();

        if (!$invite->email) {
            return redirect()->back()->with('error', 'No email address associated with this invite.');
        }

        Mail::to($invite->email)->send(new InviteMail($invite, auth()->user()->name));

        return redirect()->back()->with('success', 'Invite email resent successfully!');
    }

    public function destroy($id)
    {
        $invite = Invite::where('id', $id)
            ->where('invited_by', auth()->id())
            ->firstOrFail();

        $invite->delete();

        return redirect()->back()->with('success', 'Invite code deleted successfully.');
    }

    public function validateCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        $invite = Invite::where('code', strtoupper($request->code))->first();

        if (!$invite) {
            return response()->json(['valid' => false, 'message' => 'Invalid invite code.']);
        }

        if (!$invite->isValid()) {
            return response()->json(['valid' => false, 'message' => 'This invite code has expired or been fully used.']);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Valid invite code!',
            'uses' => $invite->uses,
            'max_uses' => $invite->max_uses,
            'remaining' => $invite->remaining_uses
        ]);
    }

    public function referralDashboard()
    {
        $user = auth()->user();

        $referrals = User::where('referred_by', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $referralStats = [
            'total' => $referrals->count(),
            'active' => $referrals->where('email_verified_at', '!=', null)->count(),
            'pending' => $referrals->where('email_verified_at', null)->count(),
            'points' => $user->referral_points ?? 0
        ];

        return view('invites.referrals', compact('referrals', 'referralStats'));
    }
}