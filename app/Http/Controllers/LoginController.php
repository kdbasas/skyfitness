<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GymStaff;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function showLoginForm()
{
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'gym_staff') {
            return redirect()->route('gym_staff.dashboard');
        }
    }

    return view('login.login'); // Display the login view
}

public function login(Request $request)
{
    $request->validate([
        'email' => 'required|string|email',
        'password' => 'required|string',
    ]);

    // Attempt to log in as either admin or gym staff
    $user = User::where('email', $request->input('email'))->first();

    if ($user && Hash::check($request->input('password'), $user->password)) {
        // Check the role to determine the guard and redirect
        switch ($user->role) {
            case 'admin':
                Auth::guard('web')->login($user);
                return redirect()->route('admin.dashboard');
            case 'gym_staff':
                // Adjust this query if necessary, depending on how the gym staff is stored
                $gymStaff = GymStaff::where('email', $request->input('email'))->first();
                if ($gymStaff) {
                    Auth::guard('gym_staff')->login($gymStaff);
                    return redirect()->route('gym_staff.dashboard');
                }
                break;
            default:
                return redirect()->route('login')->withErrors(['email' => 'Invalid role']);
        }
    }

    // Redirect back with error message
    return redirect()->route('login')->withErrors(['email' => 'Invalid credentials']);
}

    // Handle logout
    public function logout(Request $request)
    {
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        } elseif (Auth::guard('gym_staff')->check()) {
            Auth::guard('gym_staff')->logout();
        }

        return redirect()->route('login');
    }


public function markNotificationAsRead(Request $request, $id)
{
    // Find and update the notification status
    $notification = Notification::find($id);
    if ($notification) {
        $notification->update(['read' => true]);
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false], 404);
}

}