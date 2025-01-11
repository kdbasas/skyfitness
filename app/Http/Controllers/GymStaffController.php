<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GymStaffController extends Controller
{
    // Show Staff Dashboard
    public function dashboard()
    {
        if (Auth::guard('gym_staff')->check()) {
            $gymStaff = Auth::guard('gym_staff')->user();
            // Rest of your code here
        } else {
            // Handle the case where the user is not authenticated
            return redirect()->route('login');
        }
    }
    // Show Staff Profile (example)
    public function showProfile()
    {
        $staff = Auth::user(); // Get the currently authenticated staff
        return view('staff.profile', compact('staff')); // Ensure you have this view
    }

    // Update Staff Profile (example)
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
        ]);

        $staff = Auth::user();
        $staff->name = $request->input('name');
        $staff->email = $request->input('email');
        $staff->save();

        return redirect()->route('staff.profile')->with('success', 'Profile updated successfully.');
    }

    // Other staff-related methods...
}
