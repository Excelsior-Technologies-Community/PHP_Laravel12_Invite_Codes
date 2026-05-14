<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InviteController extends Controller
{
    /**
     * Display invite codes
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $query = DB::table('invites');

        // Search
        if ($search) {
            $query->where('code', 'like', "%{$search}%");
        }

        // Pagination
        $invites = $query->oldest()->paginate(4);

        // Dashboard Stats
        $totalCodes = DB::table('invites')->count();

        $activeCodes = DB::table('invites')
            ->whereColumn('uses', '<', 'max_usages')
            ->count();

        $usedCodes = DB::table('invites')
            ->where('uses', '>', 0)
            ->count();

        return view('invites.index', compact(
            'invites',
            'totalCodes',
            'activeCodes',
            'usedCodes'
        ));
    }

    /**
     * Create invite code
     */
    public function create()
    {
        DB::table('invites')->insert([
            'code' => strtoupper(Str::random(8)),
            'max_usages' => 1,
            'uses' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Invite code created successfully.');
    }

    /**
     * Delete invite code
     */
    public function destroy($id)
    {
        DB::table('invites')
            ->where('id', $id)
            ->delete();

        return redirect()->back()
            ->with('success', 'Invite code deleted successfully.');
    }
}