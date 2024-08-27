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
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
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

    // Update Subscription
    public function updateSubscription(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,subscription_id',
            'subscription_name' => 'required|string|max:255',
            'validity' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
        ]);

        $subscription = Subscription::find($request->subscription_id);

        $subscription->update([
            'subscription_name' => $request->subscription_name,
            'validity' => $request->validity,
            'amount' => $request->amount,
        ]);

        return redirect()->route('admin.subscription')->with('success', 'Subscription updated successfully.');
    }

    // Delete Subscription
    public function deleteSubscription(Request $request)
    {
        $subscription = Subscription::find($request->subscription_id);

        if ($subscription) {
            $subscription->delete();
            return redirect()->route('admin.subscription')->with('success', 'Subscription deleted successfully.');
        }

        return redirect()->route('admin.subscription')->with('error', 'Subscription not found.');
    }

    // Show Payments
    public function showPayments()
    {
        $payments = Payment::all();
        return view('admin.payments', compact('payments'));
    }

    // Add New Payment
    public function addPayment(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,member_id',
            'subscription_id' => 'required|exists:subscriptions,subscription_id',
            'amount' => 'required|numeric|min:0',
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
        $members = Member::all();
        $subscriptions = Subscription::all();
        return view('admin.member_management', compact('members', 'subscriptions'));
    }

    // Add New Member
    public function addMember(Request $request)
{
    // Validate the request
    $validated = $request->validate([
        'first_name' => 'required|string|max:255',
        'middle_name' => 'nullable|string|max:255',
        'last_name' => 'required|string|max:255',
        'suffix_name' => 'nullable|string|max:255',
        'date_joined' => 'required|date',
        'email' => 'required|email|unique:members,email',
        'contact_number' => 'required|string|max:20',
        'subscription_id' => 'required|exists:subscriptions,subscription_id',
    ]);

    // Ensure 'suffix_name' has a value
    if (!isset($validated['suffix_name'])) {
        $validated['suffix_name'] = '';
    }

    // Fetch the subscription amount based on the selected subscription_id
    $subscription = Subscription::find($validated['subscription_id']);
    $validated['amount'] = $subscription ? $subscription->amount : 0; // Default to 0 if subscription not found
    if ($subscription) {
        // Calculate the date_expired based on the validity period of the subscription
        $validityPeriodInMonths = $subscription->validity; // validity in months
        $validated['date_expired'] = Carbon::parse($validated['date_joined'])->addMonths($validityPeriodInMonths)->format('Y-m-d');
    }

    // Store the validated data
    Member::create($validated);

    return redirect()->back()->with('success', 'Member registered successfully!');
}


    // Update Member
    public function updateMember(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,member_id',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix_name' => 'nullable|string|max:55',
            'email' => 'required|string|email|max:255',
            'contact_number' => 'required|string|max:255',
            'subscription_id' => 'required|exists:subscriptions,subscription_id',
            'amount' => 'required|numeric|min:0',
            'date_joined' => 'required|date',
            'date_expired' => 'nullable|date',
        ]);

        $member = Member::find($request->member_id);
        $member->update($request->all());

        return redirect()->route('admin.member_management')->with('success', 'Member updated successfully.');
    }

    // Delete Member
    public function deleteMember(Request $request)
    {
        $member = Member::find($request->member_id);

        if ($member) {
            $member->delete();
            return redirect()->route('admin.member_management')->with('success', 'Member deleted successfully.');
        }

        return redirect()->route('admin.member_management')->with('error', 'Member not found.');
    }
    public function getSubscriptionDetails($id)
    {
        $subscription = Subscription::find($id);
    
        if ($subscription) {
            return response()->json([
                'amount' => $subscription->amount,
            ]);
        }
    
        return response()->json([
            'amount' => null,
        ], 404);
    }
    
    // Handle Attendance
    public function handleAttendance(Request $request)
    {
        // Your attendance handling logic here
    }
}
