<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login.login'); // Adjust path if necessary
    }

    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|string',
        'password' => 'required|string',
    ]);

    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        if ($user->role === 'admin') {
            Session::put('just_logged_in', true);
            return redirect()->route('admin.dashboard');
        } 
        //elseif ($user->role === 'staff') {
           // return redirect()->route('staff.dashboard');
        //}

        return redirect()->route('login')->withErrors(['role' => 'Unauthorized']);
    }

    return redirect()->route('login')->withErrors(['email' => 'Invalid credentials']);
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

    public function logout()
    {
        Auth::logout();
        Session::flush();
        return redirect()->route('login');
    }
}
