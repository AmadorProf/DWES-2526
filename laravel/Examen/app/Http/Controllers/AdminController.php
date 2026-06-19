<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\User;
use App\Models\Rating;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function index()
    {
        $stats = [
            'total_movies' => Movie::count(),
            'total_users' => User::count(),
            'total_ratings' => Rating::count(),
            'top_movies' => Movie::orderBy('average_rating', 'desc')->take(5)->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Display all users
     */
    public function users()
    {
        $users = User::withCount(['movies', 'ratings', 'reviews'])->paginate(20);
        return view('admin.users', compact('users'));
    }

    /**
     * Update user role
     */
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:user,admin',
        ]);

        $user->update(['role' => $request->role]);

        return redirect()->back()
            ->with('success', 'Rol actualizado correctamente');
    }

    /**
     * Delete user
     */
    public function deleteUser(User $user)
    {
        // No permitir eliminar al propio admin
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'No puedes eliminar tu propia cuenta');
        }

        $user->delete();

        return redirect()->back()
            ->with('success', 'Usuario eliminado correctamente');
    }
}
