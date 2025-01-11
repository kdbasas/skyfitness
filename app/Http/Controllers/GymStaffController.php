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
        return view('gym_staff.dashboard', compact('gymStaff'));
    } else {
        \Log::warning('Gym staff not authenticated.');
        return redirect()->route('login')->with('error', 'You must be logged in to access the dashboard.');
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
} 
