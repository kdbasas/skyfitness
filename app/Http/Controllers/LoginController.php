<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GymStaff;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
     // Show the login form
     public function showLoginForm()
     {
         // If the user is already logged in, redirect to the appropriate dashboard
         if (Auth::check()) {
             $user = Auth::user();
             if ($user->role === 'admin') {
                 return redirect()->route('admin.dashboard');
             } elseif ($user->role === 'gym_staff') {
                 return redirect()->route('gym_staff.dashboard');
             }
         }
 
         return view('login.login');
     }
 
     // Handle login attempt
     public function login(Request $request)
{
    // Validate user input
    $request->validate([
        'email' => 'required|string|email',
        'password' => 'required|string',
    ]);

    // Attempt to find gym staff
    $gymStaff = GymStaff::where('email', $request->input('email'))->first();

    // Check if gym staff exists and verify password
    if ($gymStaff && Hash::check($request->input('password'), $gymStaff->password)) {
        Auth::guard('gym_staff')->login($gymStaff);  // Log in using gym staff guard
        \Log::info("Gym staff logged in: {$gymStaff->email}");
        
        // Log before redirection
        \Log::info("Redirecting to gym staff dashboard");
        
        return redirect()->route('gym_staff.dashboard');
    } else {
        \Log::warning("Login attempt failed for email: {$request->input('email')}");
    }

    // Attempt to log in the admin using the default guard (web)
    $credentials = $request->only('email', 'password');
    if (Auth::guard('web')->attempt($credentials)) {
        $user = Auth::guard('web')->user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
    }

    // If authentication fails, redirect back with an error
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
