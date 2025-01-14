@extends('layouts.app')

@section('content')
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        @include('include.sidebarstaff')
        <div class="flex-1 ml-64 p-6 bg-[#ECE9E9]">
            <!-- Header Section -->
            <div class="flex items-center justify-center mb-6 p-4 bg-transparent text-[#1A1363]"style="margin-top: -20px;">
                <h1 class="text-4xl font-bold">Attendance Records for {{ $member->first_name }} {{ $member->last_name }}</h1>
            </div>

            <!-- Back Button -->
            <div class="mb-4">
                <a href="{{ route('gym_staff.attendance') }}" class="bg-[#1A1363] text-white px-4 py-2 rounded-lg hover:bg-[#333] transition duration-300 ease-in-out">
                    Back to Attendance Records
                </a>
            </div>

            <!-- Attendance Table -->
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <table class="w-full bg-white border border-gray-300 rounded-lg shadow-md">
                    <thead>
                        <tr class="bg-[#1A1363] text-white">
                            <th class="px-4 py-2 text-left">Date</th>
                            <th class="px-4 py-2 text-left">Check-in Time</th>
                            <th class="px-4 py-2 text-left">Check-out Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendanceRecords as $record)
                            <tr>
                                <td class="px-4 py-2 border-b">{{ $record->date }}</td>
                                <td class="px-4 py-2 border-b">{{ $record->check_in_time }}</td>
                                <td class="px-4 py-2 border-b">{{ $record->check_out_time }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-2 text-center border-b">No attendance records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
