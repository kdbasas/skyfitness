<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\Equipment;
use App\Models\Member;
use App\Models\Attendance;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use App\Helpers\QrCodeGenerator;


class AdminController extends Controller
{
    // Show Admin Dashboard
    public function dashboard()
    {
        $admin = Auth::user();
        $notifications = [];

        // Generate a welcome notification if this is the first login after session start
        if (Session::has('just_logged_in')) {
            $notifications[] = [
                'message' => "Welcome, {$admin->name}!",
                'type' => 'info',
            ];

            Session::forget('just_logged_in'); // Reset login notification
        }

        // Subscription Expiry Notification (5 days before expiry)
        $expiringMembers = Member::whereDate('date_expired', '<=', Carbon::now()->addDays(5))->get();
    foreach ($expiringMembers as $member) {
        $notifications[] = [
            'message' => "Subscription for {$member->first_name} {$member->last_name} is expiring in 5 days!",
            'type' => 'warning',
        ];
    }

        return view('admin.dashboard', compact('notifications'));
    }
    public function showReports(Request $request)
{
    $selectedMonth = $request->input('month', Carbon::now()->format('Y-m')); // Default to current month
    $memberRegistrations = Member::whereYear('date_joined', Carbon::parse($selectedMonth)->year)
                                  ->whereMonth('date_joined', Carbon::parse($selectedMonth)->month)
                                  ->count();

                                  $totalRevenue = Payment::whereYear('date_paid', Carbon::parse($selectedMonth)->year)
                                  ->whereMonth('date_paid', Carbon::parse($selectedMonth)->month)
                                  ->sum('amount');
           


    return view('admin.report', compact('selectedMonth', 'memberRegistrations', 'totalRevenue'));
}
public function reportAnalytics(Request $request)
{
    $selectedMonth = $request->get('month', now()->format('Y-m'));

    // Fetch members registered in the selected month
    $members = Member::whereMonth('date_joined', '=', date('m', strtotime($selectedMonth)))
                     ->whereYear('date_joined', '=', date('Y', strtotime($selectedMonth)))
                     ->with('subscription') // Include subscription details
                     ->get();

    $memberRegistrations = $members->count();

    // Calculate total revenue from payments
    $totalRevenue = Payment::whereYear('date_paid', Carbon::parse($selectedMonth)->year)
                           ->whereMonth('date_paid', Carbon::parse($selectedMonth)->month)
                           ->sum('amount');

    return view('admin.report', compact('selectedMonth', 'memberRegistrations', 'totalRevenue', 'members'));
}



public function printReport(Request $request)
{
    $selectedMonth = $request->input('month');

    // Fetch the data needed for the report
    $memberRegistrations = Member::whereYear('date_joined', Carbon::parse($selectedMonth)->year)
                                  ->whereMonth('date_joined', Carbon::parse($selectedMonth)->month)
                                  ->count();

    $totalRevenue = Payment::whereYear('date_paid', Carbon::parse($selectedMonth)->year)
                           ->whereMonth('date_paid', Carbon::parse($selectedMonth)->month)
                           ->sum('amount');

    // Load the report PDF view
    return view('admin.report_pdf', compact('memberRegistrations', 'totalRevenue', 'selectedMonth'));
}


    public function markAsRead($id)
{
    $notification = Auth::user()->notifications()->find($id);
    if ($notification) {
        $notification->update(['read' => true]);
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false], 404);
}

public function getUnreadCount()
{
    $count = Auth::user()->notifications()->where('read', false)->count();
    return response()->json(['count' => $count]);
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
            $profileImage = $request->file('profile_image')->store('img', 'public');
            $admin->profile_image = $profileImage;
        }

        $admin->save();
        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully.');
    }
    public function updatePicture(Request $request)
{
    $request->validate([
        'profile_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    $admin = Auth::user();

    if ($request->hasFile('profile_image')) {
        $imageName = time() . '.' . $request->profile_image->extension();
        $request->profile_image->storeAs('public/img', $imageName);
        
        $admin->profile_image = $imageName;
        $admin->save();
    }

    return redirect()->route('admin.profile')->with('success', 'Profile picture updated successfully.');
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

    public function showPaymentForm()
{
    $members = Member::all(); // Fetch all members
    $subscriptions = Subscription::all(); // Fetch all subscriptions
    $payments = Payment::with('member', 'subscription')->get(); // Fetch all payments with their associated members and subscriptions

    return view('admin.payment', compact('members', 'subscriptions', 'payments'));
}

public function addPayment(Request $request)
{
    \Log::info('Payment Data:', $request->all()); // For debugging

    $request->validate([
        'member_id' => 'required|exists:members,member_id',
        'subscription_id' => 'required|exists:subscriptions,subscription_id',  // Ensure this points to the correct column
        'amount' => 'required|numeric',
        'date_paid' => 'required|date',
    ]);

    Payment::create([
        'member_id' => $request->member_id,
        'subscription_id' => $request->subscription_id,
        'amount' => $request->amount,
        'date_paid' => $request->date_paid,
    ]);

    return redirect()->route('admin.payment.form')->with('success', 'Payment recorded successfully!');
}


public function updatePayment(Request $request)
{
    $request->validate([
        'payment_id' => 'required|exists:payments,payment_id',
        'member_id' => 'required|exists:members,member_id',
        'subscription_id' => 'required|exists:subscriptions,subscription_id',
        'amount' => 'required|numeric|min:0',
        'date_paid' => 'required|date',
    ]);

    $payment = Payment::find($request->payment_id);
    $payment->update([
        'member_id' => $request->member_id,
        'subscription_id' => $request->subscription_id,
        'amount' => $request->amount,
        'date_paid' => $request->date_paid,
    ]);

    return redirect()->route('admin.payment.form')->with('success', 'Payment updated successfully!');
}

public function deletePayment(Request $request)
{
    $request->validate([
        'payment_id' => 'required|exists:payments,payment_id',
    ]);

    $payment = Payment::find($request->payment_id);

    if ($payment) {
        $payment->delete();
        return redirect()->route('admin.payment.form')->with('success', 'Payment deleted successfully.');
    }

    return redirect()->route('admin.payment.form')->with('error', 'Payment not found.');
}

    // Show Inventory Management
    public function showInventory($id = null)
    {
        if ($id) {
            $equipments = Equipment::findOrFail($id);
            return view('admin.equipment_inventory_detail', compact('equipments')); // Modify to your detail view if needed
        }

        $equipments = Equipment::all();
        return view('admin.equipment_inventory', compact('equipments'));
    }

    // Add New Equipment
    // Add New Equipment
public function addEquipment(Request $request)
{
    $request->validate([
        'equipment_name' => 'required|string|max:255',
        'total_number' => 'required|integer',
        'status' => 'required|in:active,inactive',
        'equipment_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Adjust max size as needed
    ]);

    $equipment = new Equipment();
    $equipment->equipment_name = $request->equipment_name;
    $equipment->total_number = $request->total_number;
    $equipment->status = $request->status;

    // Handle picture upload
    if ($request->hasFile('equipment_picture')) {
        $fileWithExtension = $request->file('equipment_picture');
        $filename = pathinfo($fileWithExtension->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $fileWithExtension->getClientOriginalExtension();
        $filenameToStore = $filename . '_' . time() . '.' . $extension;
        $fileWithExtension->storeAs('public/img/equipment', $filenameToStore); 
        $equipment->equipment_picture = $filenameToStore; // Save path in your database
    }

    $equipment->save();

    return redirect()->back()->with('success', 'Equipment added successfully!');
}



    // Update Equipment
    public function updateEquipment(Request $request)
{
    $request->validate([
        'equipment_id' => 'required|exists:equipments,equipment_id',
        'equipment_name' => 'required|string|max:255',
        'total_number' => 'required|integer|min:1',
        'status' => 'required|in:active,inactive',
        'equipment_picture' => 'nullable|image|mimes:jpeg,png,bmp,biff|max:4096',
    ]);

    $equipment = Equipment::find($request->equipment_id);

    // Update picture if provided
    if ($request->hasFile('equipment_picture')) {
        // Store the new picture
        $newPicture = $request->file('equipment_picture')->store('img/equipment', 'public');
        
        // Delete the old picture if it exists
        if ($equipment->equipment_picture) {
            Storage::delete('public/' . $equipment->equipment_picture); // Delete the old picture
        }
        $equipment->equipment_picture = $newPicture;
    }

    $equipment->update([
        'equipment_name' => $request->input('equipment_name'),
        'total_number' => $request->input('total_number'),
        'status' => $request->input('status'),
    ]);

    return redirect()->route('admin.equipment_inventory')->with('success', 'Equipment updated successfully.');
}


    // Delete Equipment
    // Delete Equipment
public function deleteEquipment(Request $request, $id)
{
    $equipment = Equipment::find($id);

    if ($equipment) {
        // Delete the equipment picture from storage if it exists
        if ($equipment->equipment_picture && Storage::exists('public/img/equipment/' . $equipment->equipment_picture)) {
            Storage::delete('public/img/equipment/' . $equipment->equipment_picture); // Delete picture file
        }
        
        $equipment->delete(); // Now delete the equipment
        return redirect()->back()->with('success', 'Equipment deleted successfully.');
    }

    return redirect()->route('admin.equipment_inventory')->with('error', 'Equipment not found.');
}


    // Show Member Management
    public function showMembers()
    {
        $members = Member::paginate(10);
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


    if (!isset($validated['suffix_name'])) {
        $validated['suffix_name'] = '';
    }


    $subscription = Subscription::find($validated['subscription_id']);
    $validated['amount'] = $subscription ? $subscription->amount : 0; 
    if ($subscription) {
        
        $validityPeriodInMonths = $subscription->validity; 
        $validated['date_expired'] = Carbon::parse($validated['date_joined'])->addMonths($validityPeriodInMonths)->format('Y-m-d');
    }


    $member = Member::create($validated);

 
    $qrCodeGenerator = new QrCodeGenerator();
    $qrCodeData = 'Member ID: ' . $member->member_id . ' - Name: ' . $member->first_name . ' ' . $member->last_name;
    $qrCodeFilename = 'member_' . $member->member_id . '.png';
    $qrCodeGenerator->generate($qrCodeData, $qrCodeFilename);
 
  
    $member->update(['qr_code' => $qrCodeFilename]);
 
     return redirect()->back()->with('success', 'Member registered successfully!');
 }
public function updateMember(Request $request)
{
    $request->validate([
        'id' => 'required|exists:members,member_id',
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'subscription_id' => 'required|exists:subscriptions,subscription_id',
        'contact_number' => 'required|string|max:15',
        'date_joined' => 'required|date',
    ]);

    $member = Member::find($request->id);

    $subscription = Subscription::find($request->subscription_id);
    $dateExpired = Carbon::parse($request->date_joined)->addMonths($subscription->validity);

    $member->update([
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'subscription_id' => $request->subscription_id,
        'contact_number' => $request->contact_number,
        'date_joined' => $request->date_joined,
        'date_expired' => $dateExpired,
    ]);

    return redirect()->back()->with('success', 'Member updated successfully!');
}

public function deleteMember($id)
{
    $member = Member::findOrFail($id);
    $member->delete();

    return redirect()->route('admin.member_management')->with('success', 'Member deleted successfully.');
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
    public function showAttendance(Request $request)
    {
        $sortByDate = $request->input('sort-by-date', 'desc');

    $attendanceRecords = Attendance::with('member')
        ->orderByRaw('MONTH(date)')
        ->orderByRaw('DAY(date)')
        ->orderByRaw('YEAR(date)')
        ->get();
        $formattedRecords = $attendanceRecords->map(function ($attendance) {
            return [
                'member_name' => $attendance->member->first_name . ' ' . $attendance->member->last_name,
                'date' => $attendance->date,
                'check_in_time' => $attendance->check_in_time,
                'check_out_time' => $attendance->check_out_time,
            ];
        });
    
        // Return the view with formatted records
        return view('admin.attendance', ['attendanceRecords' => $formattedRecords]);
    }
    public function handleAttendance(Request $request)
{
    $request->validate([
        'member_id' => 'required|exists:members,member_id',
        'check_in_out' => 'required|in:check-in,check-out',
    ]);

    $member = Member::find($request->member_id);
    $attendance = Attendance::where('member_id', $member->member_id)->whereDate('date', Carbon::today())->first();

    if ($attendance) {
        if ($request->check_in_out == 'check-in') {
            $attendance->check_in_time = Carbon::now()->format('H:i:s');
        } elseif ($request->check_in_out == 'check-out') {
            $attendance->check_out_time = Carbon::now()->format('H:i:s');
        }
        $attendance->save();
    } else {
        $attendance = new Attendance();
        $attendance->member_id = $member->member_id;
        $attendance->date = Carbon::today()->format('Y-m-d');
        if ($request->check_in_out == 'check-in') {
            $attendance->check_in_time = Carbon::now()->format('H:i:s');
        } elseif ($request->check_in_out == 'check-out') {
            $attendance->check_out_time = Carbon::now()->format('H:i:s');
        }
        $attendance->save();
    }

    return redirect()->back()->with('success', 'Attendance recorded successfully!');
}
    public function renew(Request $request, $id)
{
    $member = Member::findOrFail($id);
    $member->subscription_id = $request->input('subscription_id');
    
    // Optionally validate the new date_expired before saving
    if ($request->has('date_expired')) {
        $member->date_expired = $request->input('date_expired');
    }

    $member->save();

    return redirect()->back()->with('success', 'Member renewed successfully.');
}

public function showValidity($id)
{
    $subscription = Subscription::findOrFail($id);
    return response()->json(['validity' => $subscription->validity]);
}
public function downloadReport(Request $request)
{
    $selectedMonth = $request->input('month');

    // Fetch report data as above
    $memberRegistrations = Member::whereYear('date_joined', Carbon::parse($selectedMonth)->year)
                                  ->whereMonth('date_joined', Carbon::parse($selectedMonth)->month)
                                  ->count();

    $totalRevenue = Payment::whereYear('payment_date', Carbon::parse($selectedMonth)->year)
                           ->whereMonth('payment_date', Carbon::parse($selectedMonth)->month)
                           ->sum('amount');

    // Load the report view into PDF
    $pdf = PDF::loadView('admin.report_pdf', compact('memberRegistrations', 'totalRevenue', 'selectedMonth'));

    // Download the PDF file
    return $pdf->download('report_analytics_' . $selectedMonth . '.pdf');
    }
}