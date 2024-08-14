<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GymStaffController extends Controller
{
    // Show Staff Dashboard
    public function dashboard()
    {
        return view('staff.dashboard'); // Ensure you have this view
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
