<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Manajemen pengguna: hanya untuk admin (dijaga middleware 'role:admin' di route).
 */
class UserController extends Controller
{
    public function index(): View
    {
        $users = User::orderBy('name')->paginate(10);

        return view('users.index', compact('users'));
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', Rule::in(['admin', 'staff'])],
        ]);

        // Admin tidak boleh menurunkan role dirinya sendiri (hindari gudang tanpa admin).
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak dapat mengubah role akun Anda sendiri.');
        }

        $user->role = $data['role'];
        $user->save();

        return back()->with('success', "Role {$user->name} diubah menjadi {$user->role}.");
    }
}
