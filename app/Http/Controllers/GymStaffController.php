<?php

namespace App\Http\Controllers;

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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use App\Helpers\QrCodeGenerator;

class GymStaffController extends Controller
{
    public function dashboard()
{
    \Log::info('GymStaff dashboard accessed.');

    if (Auth::guard('gym_staff')->check()) {
        $gymStaff = Auth::guard('gym_staff')->user();
        \Log::info('Authenticated gym staff: ' . $gymStaff->email);

        $notifications = [];

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

         // Fetch members
    $activeMembers = Member::where('status', 'active')->get();
    $expiringMembers = Member::whereDate('date_expired', now()->addDays(5))->get();

    $expiringMembers->transform(function ($member) {
        $member->days_until_expiration = 5;
        return $member;
    });        
    // Fetch equipment
    $equipment = Equipment::all();

    // Fetch attendance records
    $attendance = Attendance::all();

    // Fetch notifications
    $notifications = [];

    return view('gym_staff.dashboard', compact('gymStaff', 'equipment', 'attendance', 'notifications', 'expiredMembers',
    'expiringMembers', 'activeMembers'));
    }
}

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
     return view('gym_staff.member_management', compact('members', 'subscriptions'));
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
 
         $pdf = Pdf::loadView('gym_staff.receipt_pdf', compact('member'));
 
         $pdf->setPaper('A4', 'portrait');
 
         $pdf->save(storage_path('app/public/receipts/' . $member->first_name . '_' . $member->last_name . '_receipt.pdf'));
 
         $member->update(['receipt_path' => 'receipts/' . $member->first_name . '_' . $member->last_name . '_receipt.pdf']);
         // Return view with auto-print JS
         return view('gym_staff.receipt', compact('member', 'pdfFilename'))
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
 
     return redirect()->route('gym_staff.member_management')->with('success', 'Member deleted successfully.');
 }
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
     return view('gym_staff.attendance', ['attendanceRecords' => $formattedRecords, 'selectedDate' => $selectedDate,]);
 }
 public function viewAttendance($member_id)
 {
     $member = Member::find($member_id);
     $attendanceRecords = Attendance::where('member_id', $member_id)->get();
 
     return view('gym_staff.attendance_view', compact('member', 'attendanceRecords'));
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
 
     return view('gym_staff.payment', compact('members', 'subscriptions', 'payments', 'member', 'selectedDate'));
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
 
     return redirect()->route('gym_staff.payment.form')->with('success', 'Payment recorded successfully!');
 }
 public function editPayment($id)
 {
     $payment = Payment::findOrFail($id);
     $subscriptions = Subscription::all();
     $members = Member::all();
 
     return view('gym_staff.payment.form', compact('payment', 'subscriptions', 'members'));
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
 
     return redirect()->route('gym_staff.payment.form', ['id' => $payment->payment_id])
         ->with('success', 'Payment updated successfully');
 }
 public function deletePayment($id)
 {
     $payment = Payment::findOrFail($id); // Find the payment by ID
     $payment->delete(); // Delete the payment
 
     return redirect()->back()->with('success', 'Payment deleted successfully');
 }
} 