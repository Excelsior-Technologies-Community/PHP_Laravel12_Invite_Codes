<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InviteController extends Controller
{
    public function index()
    {
        $invites = DB::table('invites')->get();

        return view('invites.index', compact('invites'));
    }

    public function create()
    {
        DB::table('invites')->insert([
            'code' => strtoupper(uniqid()),
            'max_usages' => 1,
            'uses' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Invite code created successfully');
    }
}