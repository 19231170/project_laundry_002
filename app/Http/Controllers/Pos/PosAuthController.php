<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PosAuthController extends Controller
{
    /**
     * Show POS login page with PIN input.
     */
    public function showLoginForm()
    {
        // If already logged in to POS, redirect to POS main page
        if (session()->has('pos_user_id')) {
            return redirect()->route('pos.index');
        }

        // Get users that have POS PIN set
        $users = User::whereNotNull('pos_pin')->get(['id', 'name']);

        return view('pos.login', compact('users'));
    }

    /**
     * Verify PIN and create POS session.
     */
    public function verifyPin(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'pin' => 'required|string|size:6',
        ]);

        $user = User::find($request->user_id);

        if (! $user || ! $user->hasPosPin()) {
            return back()->with('error', 'User tidak memiliki akses POS.');
        }

        if (! $user->verifyPosPin($request->pin)) {
            return back()->with('error', 'PIN salah. Silakan coba lagi.');
        }

        // Create POS session
        session([
            'pos_user_id' => $user->id,
            'pos_user_name' => $user->name,
            'pos_last_activity' => time(),
        ]);

        return redirect()->route('pos.index')->with('success', 'Selamat datang, '.$user->name.'!');
    }

    /**
     * Logout from POS session.
     */
    public function logout()
    {
        session()->forget(['pos_user_id', 'pos_user_name', 'pos_last_activity']);

        return redirect()->route('pos.login')->with('success', 'Berhasil logout dari POS.');
    }
}
