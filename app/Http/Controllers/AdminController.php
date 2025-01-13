<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\GymStaff;
use App\Models\Gender; 
use App\Models\User;
use App\Models\Subscription;
use App\Models\Receipt;
use App\Models\Payment;
use App\Models\Equipment;
use App\Models\Member;
use App\Models\Attendance;
use Carbon\Carbon;
use Rawilk\Printing\Receipts\ReceiptPrinter;
use Rawilk\Printing\Printing;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Options;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
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
     // Update expired members' status
     $expiredMembers = Member::whereDate('date_expired', '<=', Carbon::now())->get();
     foreach ($expiredMembers as $member) {
         $member->status = 'inactive';
         $member->save();
     }
 
      // Fetch active members
    $activeMembers = Member::where('status', 'active')->get();

    // Fetch total equipment
    $totalEquipment = Equipment::count();

    // Fetch equipment in use
    $equipmentInUse = Equipment::where('status', 'inactive')->count();

    // Fetch equipment available
    $equipmentAvailable = Equipment::where('status', 'active')->count();

    // Calculate total revenue for the current month
    $currentMonth = Carbon::now()->format('Y-m'); // Define $currentMonth here
    $totalRevenue = Payment::whereYear('date_paid', Carbon::parse($currentMonth)->year)
                           ->whereMonth('date_paid', Carbon::parse($currentMonth)->month)
                           ->sum('amount');

    // Fetch student and regular members for the selected month
    $studentMembers = Member::where('promo', 'Student')
                             ->whereYear('date_joined', Carbon::parse($currentMonth)->year)
                             ->whereMonth('date_joined', Carbon::parse($currentMonth)->month)
                             ->count();
    $regularMembers = Member::where('promo', 'Regular')
                             ->whereYear('date_joined', Carbon::parse($currentMonth)->year)
                             ->whereMonth('date_joined', Carbon::parse($currentMonth)->month)
                             ->count();

    // Calculate percentages
    if (count($activeMembers) > 0) {
        $studentMembersPercentage = ($studentMembers / count($activeMembers)) * 100;
        $regularMembersPercentage = ($regularMembers / count($activeMembers)) * 100;
    } else {
        $studentMembersPercentage = 0;
        $regularMembersPercentage = 0;
    }

    // Fetch revenue by month for the entire year
    $revenueByMonth = [];
    for ($i = 1; $i <= 12; $i++) {
        $month = Carbon::parse($currentMonth)->year . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
        $revenue = Payment::whereMonth('date_paid', $i)
                          ->whereYear('date_paid', Carbon::parse($currentMonth)->year)
                          ->sum('amount');
        $revenueByMonth[$month] = $revenue;
    }

    // Fetch top subscriptions
    $topSubscriptions = Subscription::withCount('members')
                                     ->orderBy('members_count', 'desc')
                                     ->take(5)
                                     ->get();
    // Fetch monthly registrations for growth graph
    $monthlyRegistrations = Member::select(DB::raw('DATE_FORMAT(date_joined, "%Y-%m") as month'), DB::raw('count(*) as count'))
        ->groupBy('month')
        ->orderBy('month')
        ->get();

    // Prepare data for the growth graph
    $months = [];
    $registrationCounts = [];
    foreach ($monthlyRegistrations as $registration) {
        $months[] = $registration->month;
        $registrationCounts[] = $registration->count;
    }

    $ageTrend = [];
    $members = Member::whereYear('date_joined', Carbon::parse($currentMonth)->year)
                      ->whereMonth('date_joined', Carbon::parse($currentMonth)->month)
                      ->get();
    foreach ($members as $member) {
        $age = Carbon::parse($member->birthdate)->age;
        if (isset($ageTrend[$age])) {
            $ageTrend[$age]++;
        } else {
            $ageTrend[$age] = 1;
        }
    }

    return view('admin.dashboard', compact(
        'notifications',
        'activeMembers',
        'expiredMembers',
        'totalEquipment',
        'equipmentInUse',
        'equipmentAvailable',
        'totalRevenue',
        'studentMembers',
        'regularMembers',
        'studentMembersPercentage',
        'regularMembersPercentage',
        'revenueByMonth',
        'topSubscriptions',
        'ageTrend',
        'months', // Pass months to the view
        'registrationCounts' // Pass $currentMonth to the view
    ));
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
    $selectedMonth = $request->input('month', Carbon::now()->format('Y-m'));

    // Fetch member registration data for the selected month
    $memberRegistrations = Member::whereYear('date_joined', Carbon::parse($selectedMonth)->year)
        ->whereMonth('date_joined', Carbon::parse($selectedMonth)->month)
        ->count();

    // Fetch members collection for age trend analysis
    $members = Member::whereYear('date_joined', Carbon::parse($selectedMonth)->year)
        ->whereMonth('date_joined', Carbon::parse($selectedMonth)->month)
        ->get();

    $ageTrend = [];
    foreach ($members as $member) {
        $age = Carbon::parse($member->birthdate)->age;
        if (isset($ageTrend[$age])) {
            $ageTrend[$age]++;
        } else {
            $ageTrend[$age] = 1;
        }
    }

    // Fetch total revenue for the selected month
    $totalRevenue = Payment::whereYear('date_paid', Carbon::parse($selectedMonth)->year)
        ->whereMonth('date_paid', Carbon::parse($selectedMonth)->month)
        ->sum('amount');

    // Fetch student and regular members for the selected month
    $studentMembers = Member::where('promo', 'Student')
        ->whereYear('date_joined', Carbon::parse($selectedMonth)->year)
        ->whereMonth('date_joined', Carbon::parse($selectedMonth)->month)
        ->count();
    $regularMembers = Member::where('promo', 'Regular')
        ->whereYear('date_joined', Carbon::parse($selectedMonth)->year)
        ->whereMonth('date_joined', Carbon::parse($selectedMonth)->month)
        ->count();

    // Calculate percentages
    if ($memberRegistrations > 0) {
        $studentMembersPercentage = ($studentMembers / $memberRegistrations) * 100;
        $regularMembersPercentage = ($regularMembers / $memberRegistrations) * 100;
    } else {
        $studentMembersPercentage = 0;
        $regularMembersPercentage = 0;
    }

    // Fetch revenue by month for the entire year
    $revenueByMonth = [];
    for ($i = 1; $i <= 12; $i++) {
        $month = Carbon::parse($selectedMonth)->year . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
        $revenue = Payment::whereMonth('date_paid', $i)
            ->whereYear('date_paid', Carbon::parse($selectedMonth)->year)
            ->sum('amount');
        $revenueByMonth[$month] = $revenue;
    }

    // Fetch top subscriptions
    $topSubscriptions = Subscription::withCount('members')
        ->orderBy('members_count', 'desc')
        ->take(5)
        ->get();

    return view('admin.report', compact(
        'selectedMonth',
        'memberRegistrations',
        'ageTrend',
        'totalRevenue',
        'studentMembers',
        'regularMembers',
        'studentMembersPercentage',
        'regularMembersPercentage',
        'revenueByMonth',
        'topSubscriptions'
    ));
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


public function markAllNotificationsAsRead()
{
    try {
        $user = auth()->user();

        // Mark all notifications as read
        $user->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notifications marked as read.',
            'count' => $user->unreadNotifications()->count() // Return the updated unread count
        ]);
    } catch (\Exception $e) {
        Log::error($e); // Log the exception
        return response()->json([
            'success' => false,
            'message' => 'Failed to mark notifications as read.',
            'error' => $e->getMessage()
        ], 500);
    }
}
public function getUnreadCount()
{
    $count = auth()->user()->unreadNotifications()->count();
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
            $profileImage = $request->file('profile_image')->store('img/admin', 'public');
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
        $request->profile_image->storeAs('public/img/admin', $imageName);
        
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

    public function showPaymentForm($memberId = null, Request $request)
{
    $members = Member::all(); // Fetch all members
    $subscriptions = Subscription::all(); // Fetch all subscriptions

    // Get the selected date from the request, default to today
    $selectedDate = $request->input('date', Carbon::today()->format('Y-m-d'));

    // Fetch payments based on the selected date
    $payments = Payment::with('member', 'subscription')
        ->whereDate('date_paid', $selectedDate) // Filter by the selected date
        ->orderBy('date_paid', 'asc') // Default sorting by date
        ->get(); // Execute the query

    // If a member ID is provided, fetch that specific member
    $member = $memberId ? Member::find($memberId) : null; // Use find() to avoid an exception if not found

    // Check if the member exists
    if ($memberId && !$member) {
        return redirect()->back()->with('error', 'Member not found.'); // Redirect with an error message
    }

    return view('admin.payment', compact('members', 'subscriptions', 'payments', 'member', 'selectedDate'));
}
public function addPayment(Request $request)
{
    \Log::info('Payment Data:', $request->all()); // For debugging

    $request->validate([
        'member_id' => 'required|exists:members,member_id',
        'subscription_id' => 'required|exists:subscriptions,subscription_id',  // Ensure this points to the correct column
        'date_paid' => 'required|date',
        'promo' => 'required|string', // Ensure promo is validated
    ]);
    
    $member = Member::find($request->member_id);
    $promo = $request->input('promo');
    
    // Fetch the subscription to get the validity
    $subscription = Subscription::find($request->subscription_id);
    
    // Calculate the amount based on the subscription validity and promo
    $amount = 0;

    if ($subscription) {
        // Calculate the total amount based on the promo and subscription validity
        if ($promo == 'Student') {
            $amount = 450 * $subscription->validity; // Assuming validity is in months
        } elseif ($promo == 'Regular') {
            $amount = 500 * $subscription->validity; // Assuming validity is in months
        }
    }

    // Debugging: Log the calculated amount
    \Log::info('Calculated Amount:', ['amount' => $amount]);

    // Create the payment record
    $payment = Payment::create([
        'member_id' => $request->member_id,
        'subscription_id' => $request->subscription_id,
        'amount' => $amount, // Use the calculated amount
        'date_paid' => $request->date_paid,
        'promo' => $promo, // Store the promo selected
    ]);

    // Update the member's renewal date and subscription details
    $validityPeriodInMonths = $subscription->validity;

    // If the member already has an expiration date, add the validity period to it
    if ($member->date_expired) {
        $member->date_expired = Carbon::parse($member->date_expired)->addMonths($validityPeriodInMonths)->format('Y-m-d');
    } else {
        // If no expiration date exists, set it based on the current date
        $member->date_expired = Carbon::now()->addMonths($validityPeriodInMonths)->format('Y-m-d');
    }

    // Update the member's subscription and promo
    $member->subscription_id = $request->subscription_id;
    $member->promo = $promo; // Update the promo field if needed

    // Update the total amount for the member
    $member->amount = ($member->amount ?? 0) + $amount; // Add the new payment amount to the existing amount
    $member->save(); // Save the updated member record

    return redirect()->route('admin.payment.form')->with('success', 'Payment recorded successfully!');
}
public function editPayment($id)
{
    $payment = Payment::findOrFail($id);
    $subscriptions = Subscription::all();
    $members = Member::all();

    return view('admin.payment.form', compact('payment', 'subscriptions', 'members'));
}
public function updatePayment(Request $request, $id)
{
    // Find the payment by ID or fail
    $payment = Payment::findOrFail($id);
    
    // Validate the incoming request data
    $validatedData = $request->validate([
        'subscription_id' => 'required|exists:subscriptions,subscription_id',
        'promo' => 'required|string',
        'date_paid' => 'required|date',
        'amount' => 'required|numeric',
    ]);

    // Update payment information
    $payment->subscription_id = $validatedData['subscription_id'];
    $payment->promo = $validatedData['promo'];
    $payment->date_paid = $validatedData['date_paid'];
    $payment->amount = $validatedData['amount'];

    // Save the updated payment
    $payment->save();

    // Update the member's subscription and promo
    $member = Member::findOrFail($payment->member_id);
    $member->subscription_id = $validatedData['subscription_id'];
    $member->promo = $validatedData['promo'];

    // Calculate the new expiration date based on the subscription validity
    $subscription = Subscription::find($validatedData['subscription_id']);
    if ($subscription) {
        if ($member->date_expired) {
            // Add the validity period of the new subscription to the existing expiration date
            $member->date_expired = Carbon::parse($member->date_expired)->addMonths($subscription->validity)->format('Y-m-d');
        } else {
            // If no expiration date exists, set it based on the current date
            $member->date_expired = Carbon::now()->addMonths($subscription->validity)->format('Y-m-d');
        }
    }

    // Save the updated member record
    $member->save();

    return redirect()->route('admin.payment.form', ['id' => $payment->payment_id])
        ->with('success', 'Payment updated successfully');
}
public function deletePayment($id)
{
    $payment = Payment::findOrFail($id); // Find the payment by ID
    $payment->delete(); // Delete the payment

    return redirect()->back()->with('success', 'Payment deleted successfully');
}

// Download Payment History
public function downloadPaymentHistory(Request $request)
{
    $selectedMonth = $request->input('month');

    $payments = Payment::whereYear('date_paid', Carbon::parse($selectedMonth)->year)
        ->whereMonth('date_paid', Carbon::parse($selectedMonth)->month)
        ->get();

    $pdf = PDF::loadView('admin.payment_history_pdf', compact('payments', 'selectedMonth'));

    return $pdf->download('payment_history_' . $selectedMonth . '.pdf');
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
    public function generatePaymentReport(Request $request)
    {
        $selectedDate = $request->input('date', Carbon::today()->format('Y-m-d'));
    
        // Fetch payments for the selected date
        $payments = Payment::with('member', 'subscription')
            ->whereDate('date_paid', $selectedDate)
            ->get();
    
        // Generate the PDF
        $pdf = PDF::loadView('admin.payment_report', compact('payments', 'selectedDate'));
    
        return $pdf->download("payment_report_{$selectedDate}.pdf");
    }
    // Add New Equipment
    // Add New Equipment
public function addEquipment(Request $request)
{
    $request->validate([
        'equipment_name' => 'required|string|max:255',
        'total_number' => 'required|integer',
        'status' => 'required|in:active,inactive,damaged,maintenance',
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
        'status' => 'required|in:active,inactive,damaged,maintenance',
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

public function generateEquipmentReport()
{
    $equipmentStats = [
        'active' => Equipment::where('status', 'active')->count(),
        'inactive' => Equipment::where('status', 'inactive')->count(),
        'damaged' => Equipment::where('status', 'damaged')->count(),
        'maintenance' => Equipment::where('status', 'maintenance')->count(),
        'total' => Equipment::count(),
    ];

    $equipments = Equipment::all();

    $pdf = Pdf::loadView('admin.equipment_report', [
        'equipmentStats' => $equipmentStats,
        'equipments' => $equipments,
    ]);

    return $pdf->download('equipment_report.pdf');
}
public function downloadReportEquipment(Request $request)
{
    $selectedDate = $request->input('selected_date'); // Format: YYYY-MM-DD

    if (!$selectedDate) {
        return redirect()->back()->withErrors(['selected_date' => 'Please select a date.']);
    }

    // Convert to a Carbon instance
    $date = Carbon::parse($selectedDate);

    // Fetch data for the selected date
    $equipments = Equipment::whereDate('created_at', $date)->get();

    // Pass data to generate the report (PDF or other format)
    // Your existing logic for generating the report

    return response()->download($generatedReportPath, 'Equipment_Report_' . $date->format('Y-m-d') . '.pdf');
}


    // Show Member Management
    public function showMembers(Request $request)
{
    $search = $request->input('search');

    // Query to fetch members with the search functionality
    $members = Member::with('subscription')
        ->when($search, function ($query, $search) {
            $query->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('contact_number', 'like', "%{$search}%") // Include contact_number search as well
                ->orWhereHas('subscription', function ($query) use ($search) {
                    $query->where('subscription_name', 'like', "%{$search}%");
                });
        })
        ->paginate(10);
        $subscriptions = Subscription::all();

    // Return the view with the members data
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
        'promo' => 'required|string',
        'id_attachment' => 'required|file|mimes:jpg,jpeg,png|max:2048',
    ]);
    try {
        $subscription = Subscription::find($request->subscription_id);
        $amount = 0;
        if ($request->promo == 'Student') {
            $amount = 450 * $subscription->validity;
        } elseif ($request->promo == 'Regular') {
            $amount = 500 * $subscription->validity;
        }

        if (!isset($validated['suffix_name'])) {
            $validated['suffix_name'] = '';
        }

        $subscription = Subscription::find($validated['subscription_id']);
        $validated['amount'] = $amount; // Use the calculated amount instead of the subscription amount

        if ($subscription) {
            $validityPeriodInMonths = $subscription->validity;
            $validated['date_expired'] = Carbon::parse($validated['date_joined'])->addMonths($validityPeriodInMonths)->format('Y-m-d');
        }

        $member = Member::create($validated);
            
        // Store the id attachment
        $idAttachment = $request->file('id_attachment');
        $idAttachmentFilename = time() . '.' . $idAttachment->getClientOriginalExtension();
        $idAttachment->storeAs('public/img/id_attachments', $idAttachmentFilename);
        $member->update(['id_attachment' => $idAttachmentFilename]);

        $qrCodeGenerator = new QrCodeGenerator();
        $qrCodeData = 'Member ID: ' . $member->member_id . ' - Name: ' . $member->first_name . ' ' . $member->last_name;
        $qrCodeFilename = 'member_' . $member->member_id . '.png';
        $qrCodeGenerator->generate($qrCodeData, $qrCodeFilename);

        $member->update(['qr_code' => $qrCodeFilename]);
          // Create the payment record
        Payment::create([
            'member_id' => $member->member_id,
            'subscription_id' => $validated['subscription_id'],
            'amount' => $amount,
            'date_paid' => now(),
            'promo' => $validated['promo'],
        ]);

        $pdf = Pdf::loadView('admin.receipt_pdf', compact('member'));

        $pdf->setPaper('A4', 'portrait');

        $pdf->save(storage_path('app/public/receipts/' . $member->first_name . '_' . $member->last_name . '_receipt.pdf'));

        $member->update(['receipt_path' => 'receipts/' . $member->first_name . '_' . $member->last_name . '_receipt.pdf']);
        // Return view with auto-print JS
        return view('admin.receipt', compact('member', 'pdfFilename'))
            ->with('success', 'Member registered successfully!');
    } catch (\Exception $e) {
        \Log::error('Error adding member: ' . $e->getMessage());
        return redirect()->back()->with('error', 'There was an issue adding the member.');
    }
}
public function calculateAmount(Request $request)
{
    $request->validate([
        'subscription_id' => 'required|exists:subscriptions,subscription_id',
        'promo' => 'required|string',
    ]);

    $subscription = Subscription::find($request->subscription_id);
    $amount = 0;

    if ($request->promo == 'Student') {
        $amount = 450 * $subscription->validity;
    } elseif ($request->promo == 'Regular') {
        $amount = 500 * $subscription->validity;
    }

    return response()->json(['amount' => $amount]);
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
    $qrCodePath = 'public/img/qrcode/' . $member->qr_code;
    if (Storage::exists($qrCodePath)) {
        Storage::delete($qrCodePath); // Delete the QR code image from storage
    }
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
        $selectedDate = $request->input('date', Carbon::today()->format('Y-m-d'));
    $sortByDate = $request->input('sort-by-date', 'desc');

    // Fetch attendance records filtered by the selected date
    $attendanceRecords = Attendance::with('member')
        ->whereDate('date', $selectedDate)
        ->orderBy('date', $sortByDate)
        ->get();
        $formattedRecords = $attendanceRecords->map(function ($attendance) {
            return [
                'member_name' => $attendance->member->first_name . ' ' . $attendance->member->last_name,
                'date' => $attendance->date,
                'check_in_time' => $attendance->check_in_time,
                'check_out_time' => $attendance->check_out_time,
                'member_id' => $attendance->member->member_id, // Add the 'member_id' key
            ];
        });
    
        // Return the view with formatted records
        return view('admin.attendance', ['attendanceRecords' => $formattedRecords, 'selectedDate' => $selectedDate,]);
    }
    public function viewAttendance($member_id)
{
    $member = Member::find($member_id);
    $attendanceRecords = Attendance::where('member_id', $member_id)->get();

    return view('admin.attendance_view', compact('member', 'attendanceRecords'));
}

public function handleAttendance(Request $request)
{
    $request->validate([
        'member_id' => 'required|exists:members,member_id',
        'check_in_out' => 'required|in:check-in,check-out',
    ]);

    $member = Member::find($request->member_id);
    $today = Carbon::today()->format('Y-m-d');

    // Check if the member's subscription is expired
    if ($member->date_expired && Carbon::parse($member->date_expired)->lt($today)) {
        return redirect()->back()->with('error', 'Your subscription has expired. Please renew your subscription before checking in.');
    }

    // Check if an attendance record exists for the member today
    $attendance = Attendance::where('member_id', $member->member_id)
        ->whereDate('date', $today)
        ->first();

    if ($attendance) {
        // Update the check-in or check-out time
        if ($request->check_in_out === 'check-in') {
            $attendance->check_in_time = Carbon::now()->format('H:i:s');
        } else {
            $attendance->check_out_time = Carbon::now()->format('H:i:s');
        }
    } else {
        // Create a new attendance record if it doesn't exist
        $attendance = new Attendance();
        $attendance->member_id = $member->member_id;
        $attendance->date = $today;

        if ($request->check_in_out === 'check-in') {
            $attendance->check_in_time = Carbon::now()->format('H:i:s');
        } else {
            $attendance->check_out_time = Carbon::now()->format('H:i:s');
        }
    }

    // Save the attendance record
    $attendance->save();

    // Generate the QR code only if the member's subscription is not expired
    if (!$member->date_expired || Carbon::parse($member->date_expired)->gte($today)) {
        $qrCodeGenerator = new QrCodeGenerator();
        $qrCodeData = 'Member ID: ' . $member->member_id . ' - Name: ' . $member->first_name . ' ' . $member->last_name;
        $qrCodeFilename = 'member_' . $member->member_id . '.png';
        $qrCodeGenerator->generate($qrCodeData, $qrCodeFilename);

        $member->update(['qr_code' => $qrCodeFilename]);
    }

    return redirect()->back()->with('success', 'Attendance recorded successfully!');
}

public function generatePdf(Request $request)
{
    // Retrieve the selected date from the request or use today's date as default
    $selectedDate = $request->input('date', Carbon::today()->format('Y-m-d'));

    // Fetch attendance records for the selected date
    $attendanceRecords = Attendance::with('member')
        ->whereDate('date', $selectedDate)
        ->orderBy('date', 'asc')
        ->get();

    // Format attendance data for the PDF view
    $formattedRecords = $attendanceRecords->map(function ($attendance) {
        return [
            'member_name' => $attendance->member->first_name . ' ' . $attendance->member->last_name,
            'date' => $attendance->date,
            'check_in_time' => $attendance->check_in_time,
            'check_out_time' => $attendance->check_out_time,
        ];
    });

    // Load the PDF view and pass the attendance data and selected date
    $pdf = Pdf::loadView('admin.attendance_report', [
        'attendanceRecords' => $formattedRecords,
        'selectedDate' => $selectedDate,
    ]);

    // Return the PDF for download
    return $pdf->download('attendance_report-' . $selectedDate . '.pdf');
}
public function renew(Request $request, $id)
{
    // Find the member by ID
    $member = Member::findOrFail($id);

    // Validate the request
    $request->validate([
        'subscription_id' => 'required|exists:subscriptions,subscription_id',
        'promo' => 'required|string', // Ensure promo is validated
    ]);

    // Fetch the new subscription to get the validity
    $subscription = Subscription::find($request->input('subscription_id'));
    
    if ($subscription) {
        // Log the current expiration date
        \Log::info('Current Expiration Date:', ['date_expired' => $member->date_expired]);

        // If the member already has an expiration date, add the validity period to it
        if ($member->date_expired) {
            // Add the validity period of the new subscription to the existing expiration date
            $member->date_expired = Carbon::parse($member->date_expired)->addMonths($subscription->validity)->format('Y-m-d');
        } else {
            // If no expiration date exists, set it based on the current date
            $member->date_expired = Carbon::now()->addMonths($subscription->validity)->format('Y-m-d');
        }

        // Log the new expiration date
        \Log::info('New Expiration Date:', ['new_date_expired' => $member->date_expired]);
    }

    // Update the member's subscription
    $member->subscription_id = $request->input('subscription_id');
    $member->promo = $request->input('promo'); // Update the promo field if needed
    $member->save(); // Save the updated member record

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

                                  $totalRevenue = Payment::whereYear('date_paid', Carbon::parse($selectedMonth)->year)
                                  ->whereMonth('date_paid', Carbon::parse($selectedMonth)->month)
                                  ->sum('amount');
           

    // Load the report view into PDF
    $pdf = PDF::loadView('admin.report_pdf', compact('memberRegistrations', 'totalRevenue', 'selectedMonth'));

    // Download the PDF file
    return $pdf->download('report_analytics_' . $selectedMonth . '.pdf');
    }
    public function printReceipt($member_id)
{
    $member = Member::find($member_id);

    if ($member) {
        $pdf = Pdf::loadView('admin.receipt_pdf', compact('member'));
        return $pdf->download("receipt_{$member->first_name}_{$member->last_name}.pdf");
    }

    return redirect()->back()->with('error', 'Member not found.');
}

public function showStaffManagement(Request $request)
{
    $search = $request->input('search');
    
    // Query to fetch gym staff with search functionality
    $gym_staffs = GymStaff::when($search, function ($query, $search) {
        $query->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
    })->paginate(10); // Paginate results  
    $genders = Gender::all(); // Make sure to import the Gender model 
    // Debugging: Check if gym_staff is a collection
    if ($gym_staffs->isEmpty()) {
        \Log::info('No gym staff found.');
    } else {
        \Log::info('Gym staff found:', $gym_staffs->toArray());
    }

    return view('admin.staff_management', compact('gym_staffs', 'genders'));
}
    
public function storeStaff(Request $request)
{
    $request->validate([
        'email' => 'required|email|unique:gym_staffs,email|unique:users,email',
        'password' => 'required|string|min:3',
        'first_name' => 'required|string|max:255',
        'middle_name' => 'nullable|string|max:255',
        'last_name' => 'required|string|max:255',
        'suffix_name' => 'nullable|string|max:55',
        'age' => 'required|integer|min:18',
        'contact_number' => 'required|string|max:20',
        'gender_id' => 'required|exists:genders,gender_id',
        'profile_image' => 'required|file|mimes:jpg,jpeg,png|max:2048',
    ]);

    try {
        $gym_staff = new GymStaff();
        $gym_staff->fill($request->all());
        
        $profileImage = $request->file('profile_image');
        $profileImageFilename = time() . '.' . $profileImage->getClientOriginalExtension();
        $profileImage->storeAs('public/img/gym_staff', $profileImageFilename);
        $gym_staff->profile_image = asset('storage/img/gym_staff/' . $profileImageFilename);


        $gym_staff->password = bcrypt($request->input('password'));
        $gym_staff->save();

        // Check if user with same email already exists
        $user = User::where('email', $request->input('email'))->first();
        if (!$user) {
            User::create([
                'name' => $request->input('first_name'),
                'email' => $request->input('email'),
                'password' => bcrypt($request->input('password')),
                'role' => 'gym_staff',
            ]);
        }

        return redirect()->route('admin.staff.management')->with('success', 'Staff member registered successfully!');
    } catch (\Exception $e) {
        return back()->withErrors('Failed to register staff. Please try again.');
    }
}

public function updateStaff(Request $request, $id)
{
    // Ensure $id is gymstaff_id
    $gym_staff = GymStaff::findOrFail($id); // Assuming $id is gymstaff_id

    $request->validate([
        'email' => [
        'required',
        'email',
        Rule::unique('gym_staffs')->ignore($gym_staff->gymstaff_id, 'gymstaff_id'),
    ],

        'first_name' => 'required|string|max:255',
        'middle_name' => 'nullable|string|max:255',
        'last_name' => 'required|string|max:255',
        'suffix_name' => 'nullable|string|max:55',
        'age' => 'required|integer|min:18',
        'contact_number' => 'required|string|max:20',
        'gender_id' => 'required|exists:genders,gender_id',
        'profile_image' => 'nullable|image|max:2048',
    ]);

    try {
        $gym_staff->fill($request->except(['profile_image', 'email']));

        // Update profile image if provided
        if ($request->hasFile('profile_image')) {
            // Delete old image
            if ($gym_staff->profile_image && Storage::exists('public/' . $gym_staff->profile_image)) {
                Storage::delete('public/' . $gym_staff->profile_image);
            }

            $imagePath = $request->file('profile_image')->store('img/gym_staff', 'public');
            $gym_staff->profile_image = $imagePath;
        }

        // Update email only if changed
        if ($gym_staff->email !== $request->input('email')) {
            // Check if new email already exists
            $user = User::where('email', $request->input('email'))->first();
            if ($user) {
                return back()->withErrors('Email already exists.');
            }

            $gym_staff->email = $request->input('email');
            $user = User::where('email', $gym_staff->getOriginal('email'))->first();
            if ($user) {
                $user->email = $request->input('email');
                $user->save();
            }
        }

        $gym_staff->save();

        return redirect()->route('admin.staff.management')->with('success', 'Staff member updated successfully!');
    } catch (\Exception $e) {
        return back()->withErrors('Failed to update staff. Please try again.');
    }
}


public function deleteStaff($id)
{
    try {
        $gym_staff = GymStaff::findOrFail($id);

        // Delete associated user record
        $user = User::where('email', $gym_staff->email)->first();
        if ($user) {
            $user->delete();
        } else {
            Log::error('User not found when trying to delete staff member ' . $id);
            return back()->withErrors('Failed to delete staff. Please try again.');
        }

        // Delete profile image if exists
        if ($gym_staff->profile_image && Storage::exists('public/' . $gym_staff->profile_image)) {
            try {
                Storage::delete('public/' . $gym_staff->profile_image);
            } catch (\Exception $e) {
                Log::error('Failed to delete profile image when trying to delete staff member ' . $id . ': ' . $e->getMessage());
                return back()->withErrors('Failed to delete staff. Please try again.');
            }
        }

        try {
            $gym_staff->delete();
        } catch (\Exception $e) {
            Log::error('Failed to delete staff member ' . $id . ': ' . $e->getMessage());
            return back()->withErrors('Failed to delete staff. Please try again.');
        }

        return redirect()->route('admin.staff.management')->with('success', 'Staff member deleted successfully!');
    } catch (\Exception $e) {
        Log::error('Failed to delete staff member ' . $id . ': ' . $e->getMessage());
        return back()->withErrors('Failed to delete staff. Please try again.');
    }
}
}