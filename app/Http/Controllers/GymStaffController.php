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

  // Show Member Management
  public function showMembers(Request $request)
  {
      $search = $request->input('search');
      $sort = $request->input('sort', '');
  
      // Query to fetch members with the search functionality
      $members = Member::with('subscription', 'gender')
          ->when($search, function ($query, $search) {
              $query->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%")
                  ->orWhereHas('subscription', function ($query) use ($search) {
                      $query->where('subscription_name', 'like', "%{$search}%");
                  });
          });
       // Define sorting options
      $sortOptions = [
          'latest' => ['column' => 'date_joined', 'direction' => 'desc'],
          'oldest' => ['column' => 'date_joined', 'direction' => 'asc'],
          'student' => ['column' => 'promo', 'value' => 'Student'],
          'regular' => ['column' => 'promo', 'value' => 'Regular'],
          'gender_male' => ['column' => 'gender', 'value' => 'Male'],
          'gender_female' => ['column' => 'gender', 'value' => 'Female'],
          'active' => ['condition' => 'active'],
          'inactive' => ['condition' => 'inactive'],
          'expired' => ['condition' => 'expired'],
      ];
  
       // Apply sorting based on the selected option
       if (array_key_exists($sort, $sortOptions)) {
          $option = $sortOptions[$sort];
          if (isset($option['condition'])) {
              if ($option['condition'] === 'active') {
                  $members->where('date_expired', '>', now());
              } elseif ($option['condition'] === 'inactive') {
                  $members->where('date_expired', '<', now()->subDays(30));
              } elseif ($option['condition'] === 'expired') {
                  $members->where('date_expired', '<=', now())->where('date_expired', '>', now()->subDays(30));
              }
          } elseif (isset($option['direction'])) {
              $members->orderBy($option['column'], $option['direction']);
          } elseif (isset($option['value'])) {
              if ($option['column'] === 'promo') {
                  // Sort by promo directly
                  $members->where('promo', $option['value']);
              } elseif ($option['column'] === 'gender') {
                  // Sort by gender
                  $members->whereHas('gender', function ($query) use ($option) {
                      $query->where('name', $option['value']);
                  });
              }
          }
      }
      // Default sorting by date_joined if no sort option is selected
      $members->orderBy('date_joined', 'desc');
  
      // Paginate the results
      $members = $members->paginate(10); // Adjust the number of items per page as needed
  
      // Retrieve all subscriptions for the dropdown
      $subscriptions = Subscription::all();
      $genders = Gender::all();
  
      return view('gym_staff.member_management', compact('members', 'subscriptions', 'genders'));
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
          'age' => 'required|integer|min:1',
          'subscription_id' => 'required|exists:subscriptions,subscription_id',
          'promo' => 'required|string',
          'id_attachment' => 'required|file|mimes:jpg,jpeg,png|max:2048',
          'gender_id' => 'required|exists:genders,gender_id',
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
            $monthlyAmount = $amount / $validityPeriodInMonths; // Divide total amount by months
            for ($i = 0; $i < $validityPeriodInMonths; $i++) {
              $paymentDate = Carbon::parse($validated['date_joined'])->addMonths($i)->format('Y-m-d');
              Payment::create([
                  'member_id' => $member->member_id,
                  'subscription_id' => $validated['subscription_id'],
                  'amount' => $monthlyAmount, // Assign the divided amount
                  'date_paid' => $paymentDate,
                  'promo' => $request->promo,
                  'status' => 'Registration',
              ]);
          }
  
          return redirect()->route('gym_staff.member_management')->with('success', 'Member registered successfully!');
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
     $pdf = Pdf::loadView('gym_staff.attendance_report', [
         'attendanceRecords' => $formattedRecords,
         'selectedDate' => $selectedDate,
     ]);
 
     // Return the PDF for download
     return $pdf->download('attendance_report-' . $selectedDate . '.pdf');
 }
 public function showPaymentForm(Request $request)
{
    $members = Member::all(); // Fetch all members
    $subscriptions = Subscription::all(); // Fetch all subscriptions

    // Get the selected date from the request, default to today
    $selectedDate = $request->input('date', Carbon::today()->format('Y-m-d'));

    // Start building the query for payments
    $payments = Payment::with('member', 'subscription');

    // Implement search functionality
    if ($request->has('search') && $request->input('search') != '') {
        $search = $request->input('search');
        $payments->whereHas('member', function ($query) use ($search) {
            $query->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
        });
    }

    // Filter by the selected date
    $payments->whereDate('date_paid', $selectedDate);

    // Implement sorting functionality
    if ($request->has('sort')) {
        $sort = $request->input('sort');
        if ($sort === 'latest') {
            $payments->orderBy('date_paid', 'desc');
        } elseif ($sort === 'oldest') {
            $payments->orderBy('date_paid', 'asc');
        } elseif ($sort === 'registration') {
            $payments->where('status', 'Registration');
        } elseif ($sort === 'renewal') {
            $payments->where('status', 'Renewal');
        }
    }

    // Execute the query to get the payments
    $payments = $payments->get(); // Now we call get() after applying filters

    return view('gym_staff.payment', compact('members', 'subscriptions', 'payments', 'selectedDate'));
}
public function addPayment(Request $request)
{
    \Log::info('Payment Data:', $request->all()); // Debugging

    $request->validate([
        'member_id' => 'required|exists:members,member_id',
        'subscription_id' => 'required|exists:subscriptions,subscription_id',
        'date_paid' => 'required|date',
        'promo' => 'required|string',
    ]);

    $member = Member::find($request->member_id);
    $subscription = Subscription::find($request->subscription_id);
    $promo = $request->input('promo');

    // Calculate amount based on promo
    $monthlyAmount = ($promo == 'Student') ? 450 : 500; // Monthly amount based on promo
    $validityPeriodInMonths = $subscription->validity ?? 0; 

    // Prevent division by zero
    if ($validityPeriodInMonths <= 0) {
        return back()->with('error', 'Invalid subscription validity period.');
    }

    // ✅ Move totalAmount calculation here
    $totalAmount = $monthlyAmount * $validityPeriodInMonths;

    // Create payment records for each month of the new subscription
    for ($i = 0; $i < $validityPeriodInMonths; $i++) {
        $paymentDate = Carbon::parse($request->date_paid)->addMonths($i)->format('Y-m-d');
        Payment::create([
            'member_id' => $request->member_id,
            'subscription_id' => $request->subscription_id,
            'amount' => $monthlyAmount, // Assign the divided amount
            'date_paid' => $paymentDate,
            'promo' => $promo,
            'status' => 'Renewal',
        ]);
    }

    // Update member's expiration date based on the renewal date
    $newExpiration = Carbon::parse($request->date_paid)->addMonths($subscription->validity)->format('Y-m-d');
    $member->date_expired = $newExpiration;
    $member->subscription_id = $request->subscription_id;
    $member->amount = ($member->amount ?? 0) + $totalAmount; // Update total amount
    $member->save();

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
    ]);

    // Calculate the amount based on the promo and subscription
    $subscription = Subscription::find($validatedData['subscription_id']);
    $amount = ($validatedData['promo'] == 'Student') ? 450 * $subscription->validity : 500 * $subscription->validity;

    // Update payment information
    $payment->subscription_id = $validatedData['subscription_id'];
    $payment->promo = $validatedData['promo'];
    $payment->date_paid = $validatedData['date_paid'];
    $payment->amount = $amount; // Set the calculated amount

    // Save the updated payment
    $payment->save();

    // Update the member's subscription and promo
    $member = Member::findOrFail($payment->member_id);
    $member->subscription_id = $validatedData['subscription_id'];
    $member->promo = $validatedData['promo'];

    // Calculate the new expiration date based on the subscription validity
    if ($subscription) {
        // Reset the expiration date based on the new subscription validity
        $member->date_expired = Carbon::now()->addMonths($subscription->validity)->format('Y-m-d');
    }

    // Save the updated member record
    $member->save();

    return redirect()->route('gym_staff.payment.form')->with('success', 'Payment updated successfully');
}
public function deletePayment($id)
{
    $payment = Payment::findOrFail($id); // Find the payment by ID
    $payment->delete(); // Delete the payment

    return redirect()->back()->with('success', 'Payment deleted successfully');
}

 public function printReceipt($memberId)
{
    $member = Member::find($memberId);

    if ($member) {
        return view('gym_staff.receipt_pdf', compact('member'));
    }

    return redirect()->back()->with('error', 'Member not found.');
    }
}