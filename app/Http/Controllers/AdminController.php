<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\Equipment;
use App\Models\Member;
use App\Models\Attendance;
use Illuminate\Support\Facades\Storage;


class AdminController extends Controller
{
    // Show Admin Dashboard
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    // Show Admin Profile
    public function showProfile()
    {
        $admin = Auth::user();
        if ($admin->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Unauthorized access');
        }
        return view('admin.profile', compact('admin'));
    }

    // Update Admin Profile
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        $admin = Auth::user();
        if ($admin->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Unauthorized access');
        }

        $admin->name = $request->input('name');
        $admin->email = $request->input('email');

        if ($request->filled('password')) {
            $admin->password = bcrypt($request->input('password'));
        }

        if ($request->hasFile('profile_image')) {
            $profileImage = $request->file('profile_image')->store('profile_pictures/admin', 'public');
            $admin->profile_image = $profileImage;
        }

        $admin->save();
        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully.');
    }

    // Show Registration Form
    public function showRegistrationForm()
    {
        return view('admin.registration');
    }

    // Register New Admin
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'role' => 'admin', // Set role to admin
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'New admin registered successfully.');
    }

    // Show Subscription Management
    public function showSubscriptions()
{
    $subscriptions = Subscription::all();
        return view('admin.subscription', compact('subscriptions'));
    }
    

    // Add New Subscription
    public function addSubscription(Request $request)
{
    $request->validate([
        'subscription_name' => 'required|string|max:255',
        'validity' => 'required|integer|min:1',
        'amount' => 'required|numeric|min:0',
    ]);

    try {
        // Debugging: Check if the request data is received correctly
        \Log::info('Subscription Data:', $request->all());

        Subscription::create($request->all());
        return redirect()->route('admin.subscription')->with('success', 'Subscription added successfully.');
    } catch (\Exception $e) {
        \Log::error('Error adding subscription: ' . $e->getMessage());
        return redirect()->back()->with('error', 'There was an issue adding the subscription.');
    }
}

    // Show Payment Management
    public function showPayments()
    {
        $payments = Payment::all();
        return view('admin.payments', compact('payments'));
    }

    // Add New Payment
    public function addPayment(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'subscription_id' => 'required|exists:subscriptions,id',
            'price' => 'required|numeric|min:0',
            'date_of_join' => 'required|date',
        ]);

        Payment::create($request->all());

        return redirect()->route('admin.payments')->with('success', 'Payment added successfully.');
    }

    // Show Inventory Management
    public function showInventory()
    {
        $equipment = Equipment::all();
        return view('admin.inventory', compact('equipment'));
    }

    // Add New Equipment
    public function addEquipment(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'picture' => 'nullable|image|max:2048',
            'total_number' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);

        $picture = $request->hasFile('picture')
            ? $request->file('picture')->store('equipment_pictures', 'public')
            : null;

        Equipment::create([
            'name' => $request->input('name'),
            'picture' => $picture,
            'total_number' => $request->input('total_number'),
            'status' => $request->input('status'),
        ]);

        return redirect()->route('admin.inventory')->with('success', 'Equipment added successfully.');
    }

    // Show Member Management
    public function showMembers()
    {
        $members = Member::paginate(10);
        return view('admin.members', compact('members'));
    }

    // Show Report Analytics
    public function showReports()
    {
        return view('admin.reports');
    }

    // Show Payment History
    public function showPaymentHistory()
    {
        $payments = Payment::paginate(10);
        return view('admin.payment_history', compact('payments'));
    }

    // Show Attendance
    public function showAttendance()
    {
        $attendance = Attendance::all();
        return view('admin.attendance', compact('attendance'));
    }
    public function updatePicture(Request $request)
{
    $request->validate([
        'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $admin = Auth::user();
    $currentImage = $admin->profile_image;

    if ($request->hasFile('profile_image')) {
        // Delete the old image if it exists
        if ($currentImage && Storage::exists('public/img/' . $currentImage)) {
            Storage::delete('public/img/' . $currentImage);
        }

        // Store the new image with its original name
        $image = $request->file('profile_image');
        $imageName = $image->getClientOriginalName();
        $image->storeAs('public/img', $imageName);

        // Update the admin's profile image path
        $admin->profile_image = $imageName;
        $admin->save();
    }

    return redirect()->back()->with('success', 'Profile picture updated successfully.');
    }
}