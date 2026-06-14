<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPinController extends Controller
{
    /**
     * Show PIN management page (admin only).
     */
    public function index()
    {
        // Check if current user is admin
        if (! Auth::user()->isAdmin()) {
            abort(403, 'Hanya admin yang dapat mengakses halaman ini.');
        }

        $users = User::orderBy('name')->get();

        return view('admin.pin-management', compact('users'));
    }

    /**
     * Update user's POS PIN.
     */
    public function updatePin(Request $request, User $user)
    {
        // Check if current user is admin
        if (! Auth::user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'pin' => 'required|string|size:6|regex:/^[0-9]+$/',
        ]);

        $user->update([
            'pos_pin' => $request->pin,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'PIN berhasil diperbarui untuk '.$user->name,
        ]);
    }

    /**
     * Remove user's POS PIN.
     */
    public function removePin(User $user)
    {
        // Check if current user is admin
        if (! Auth::user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $user->update([
            'pos_pin' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'PIN berhasil dihapus untuk '.$user->name,
        ]);
    }

    /**
     * Toggle user admin status.
     */
    public function toggleAdmin(User $user)
    {
        // Check if current user is admin
        if (! Auth::user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Prevent removing own admin status
        if ($user->id === Auth::id() && $user->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menghapus status admin Anda sendiri.',
            ], 400);
        }

        $user->update([
            'is_admin' => ! $user->is_admin,
        ]);

        return response()->json([
            'success' => true,
            'message' => $user->is_admin
                ? $user->name.' sekarang adalah admin.'
                : 'Status admin '.$user->name.' telah dihapus.',
            'is_admin' => $user->is_admin,
        ]);
    }
}
