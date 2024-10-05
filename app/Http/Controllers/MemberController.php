<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function showAttendance()
    {
        return view('member.attendance');
    }
    
    public function generateAttendance(Request $request)
    {
        // Get the member's ID from the QR code
        $memberId = $request->input('qr_code');
    
        // Get the member's attendance record for the current date
        $attendance = Attendance::where('member_id', $memberId)->whereDate('date', Carbon::today())->first();
    
        if ($attendance) {
            // Update the attendance record
            if ($request->input('check_in_out') == 'Check-in') {
                $attendance->check_in_time = Carbon::now()->format('H:i:s');
            } elseif ($request->input('check_in_out') == 'Check-out') {
                $attendance->check_out_time = Carbon::now()->format('H:i:s');
            }
            $attendance->save();
        } else {
            // Create a new attendance record
            $attendance = new Attendance();
            $attendance->member_id = $memberId;
            $attendance->date = Carbon::today()->format('Y-m-d');
            if ($request->input('check_in_out') == 'Check-in') {
                $attendance->check_in_time = Carbon::now()->format('H:i:s');
            } elseif ($request->input('check_in_out') == 'Check-out') {
                $attendance->check_out_time = Carbon::now()->format('H:i:s');
            }
            $attendance->save();
        }
    
        return response()->json(['message' => 'Attendance recorded successfully']);
    }
}