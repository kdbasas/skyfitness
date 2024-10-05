@extends('layouts.app')

@section('content')
<div class="flex min-h-screen">
    <!-- Sidebar -->
    @include('include.sidebar')
    <div class="flex-1 ml-64 p-6 bg-[#ECE9E9]">
        <!-- Header Section -->
        <div class="flex items-center justify-center mb-6 p-4 bg-transparent text-[#1A1363]"style="margin-top: -20px;">
            <h1 class="text-4xl font-bold">Attendance Records</h1>
        </div>

        <!-- Attendance Table -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-4 text-[#1A1363]">Attendance Records</h2>

            <table class="w-full bg-white border border-gray-300 rounded-lg shadow-md">
                <thead>
                    <tr class="bg-[#1A1363] text-white">
                        <th class="px-4 py-2 text-left">Date</th>
                        <th class="px-4 py-2 text-left">Time</th>
                        <th class="px-4 py-2 text-left">Member Name</th>
                        <th class="px-4 py-2 text-left">Check-in/Check-out</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendanceRecords as $record)
                        <tr>
                            <td class="px-4 py-2 border-b">{{ $record->date }}</td>
                            <td class="px-4 py-2 border-b">{{ $record->time }}</td>
                            <td class="px-4 py-2 border-b">{{ $record->member_name }}</td>
                            <td class="px-4 py-2 border-b">{{ $record->check_in_out }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-2 text-center border-b">No attendance records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>