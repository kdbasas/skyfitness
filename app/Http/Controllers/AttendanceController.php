<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class AttendanceController extends Controller
{
    public function showAttendance()
    {
        return view('member.attendance');
    }

    public function generateAttendance(Request $request)
    {
        $qrCodeData = $request->input('qr_code');
    
        // Debugging: Print QR code for verification
        \Log::info('Received QR Code: ' . $qrCodeData);
    
        // Parse the Member ID from the QR code string
        // Assuming the QR code is in the format: "Member ID: 21 - Name: Jude Angelo Basas"
        if (preg_match('/Member ID:\s*(\d+)/', $qrCodeData, $matches)) {
            $memberId = $matches[1]; // Extracted member ID from the QR code string
        } else {
            return response()->json(['message' => 'Invalid QR code format'], 400);
        }
    
        // Find the member using the extracted member ID
        $member = Member::where('member_id', $memberId)->first();
    
        if (!$member) {
            return response()->json(['message' => 'Invalid QR code'], 400);
        }
    
        // Proceed with attendance logic
        $attendance = Attendance::where('member_id', $member->member_id)
            ->whereDate('date', Carbon::today())
            ->first();
    
            if ($attendance) {
                if ($attendance->check_in_time) {
                    return response()->json(['message' => 'Already checked in', 'attendanceStatus' => 'checkedIn', 'memberId' => $member->member_id]);
                } else {
                    $attendance->check_in_time = Carbon::now()->format('H:i:s');
                    $attendance->save();
                    return response()->json(['message' => 'Check-in successful', 'attendanceStatus' => 'checkedIn', 'memberId' => $member->member_id]);
                }
            } else {
                $attendance = new Attendance();
                $attendance->member_id = $member->member_id;
                $attendance->date = Carbon::today()->format('Y-m-d');
                $attendance->check_in_time = Carbon::now()->format('H:i:s');
                $attendance->save();
            
                return response()->json(['message' => 'Attendance recorded successfully', 'attendanceStatus' => 'checkedIn', 'memberId' => $member->member_id]);
            }
        }
    public function checkIn(Request $request)
{
    $memberId = $request->input('member_id'); // Member ID should be sent in the request

    // Check for today's attendance record
    $attendance = Attendance::where('member_id', $memberId)
        ->whereDate('date', Carbon::today())
        ->first();

    // If no record exists, create one
    if (!$attendance) {
        $attendance = new Attendance();
        $attendance->member_id = $memberId;
        $attendance->date = Carbon::today()->format('Y-m-d');
        $attendance->check_in_time = Carbon::now()->format('H:i:s');
        $attendance->save();

        return response()->json(['message' => 'Attendance recorded successfully', 'attendanceStatus' => 'checkedIn']);
    }

    // If a record exists, check the check-in time
    if ($attendance->check_in_time) {
        return response()->json(['message' => 'Already checked in', 'attendanceStatus' => 'checkedIn']);
    }

    // If not checked in, update the check-in time
    $attendance->check_in_time = Carbon::now()->format('H:i:s');
    $attendance->save();

    return response()->json(['message' => 'Check-in successful', 'attendanceStatus' => 'checkedIn']);
}
public function checkOut(Request $request)
{
    \Log::info('Check-out request data: ', $request->all()); // Log all incoming data

    $memberId = $request->input('member_id'); // Member ID should be sent in the request

    // Validate member ID
    if (!$memberId || !is_numeric($memberId)) {
        return response()->json(['message' => 'Invalid member ID'], 400);
    }

    // Retrieve today's attendance record
    $attendance = Attendance::where('member_id', $memberId)
        ->whereDate('date', Carbon::today())
        ->first();

    // Check if the record exists
    if (!$attendance) {
        return response()->json(['message' => 'No attendance record found'], 404);
    }

    // Validate check-in status
    if (!$attendance->check_in_time) {
        return response()->json(['message' => 'Cannot check out without checking in'], 400);
    }

    // Update check-out time
    $attendance->check_out_time = Carbon::now()->format('H:i:s');
    $attendance->save();

    return response()->json(['message' => 'Check-out successful', 'attendanceStatus' => 'checkedOut']);
}
}