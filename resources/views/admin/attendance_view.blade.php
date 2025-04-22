@extends('layouts.app')

@section('content')
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="sidebar print:hidden">
            @include('include.sidebar')
        </div>

        <!-- Main Content -->
        <div class="flex-1 ml-64 p-6 bg-[#ECE9E9] print:ml-0 print:p-12 print:bg-white">
            <!-- Screen Header -->
            <div class="flex items-center justify-center mb-6 p-4 bg-transparent text-[#1A1363] print:hidden" style="margin-top: -20px;">
                <h1 class="text-4xl font-bold">Attendance Records for {{ $member->first_name }} {{ $member->last_name }}</h1>
            </div>

            <!-- Print Header -->
            <div class="hidden print:block text-center mb-8">
                <h1 class="text-3xl font-bold">ROXAS SKY FITNESS GYM</h1>
                <h2 class="text-xl mt-2 font-semibold">Attendance Report</h2>
                <p class="mt-2">Member: <strong>{{ $member->first_name }} {{ $member->last_name }}</strong></p>
                <p>Date Printed: {{ now()->format('F d, Y') }}</p>
                <hr class="my-4 border-t border-black">
            </div>

            <!-- Action Buttons -->
            <div class="mb-6 flex justify-between items-center print:hidden">
                <a href="{{ route('admin.attendance') }}" class="bg-[#1A1363] text-white px-4 py-2 rounded-lg hover:bg-[#333] transition duration-300 ease-in-out">
                    Back to Attendance Records
                </a>
                <button onclick="window.print()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-300 ease-in-out">
                    Print Report
                </button>
            </div>

            <!-- Attendance Table -->
            <div class="bg-white p-6 rounded-lg shadow-lg print:shadow-none print:p-0">
                <table class="w-full border border-gray-300 print:border-black">
                    <thead>
                        <tr class="bg-[#1A1363] text-white print:bg-black print:text-white text-left">
                            <th class="px-4 py-2 border print:border-black">Date</th>
                            <th class="px-4 py-2 border print:border-black">Check-in Time</th>
                            <th class="px-4 py-2 border print:border-black">Check-out Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendanceRecords as $record)
                            <tr class="text-left">
                                <td class="px-4 py-2 border-b border-gray-300 print:border-black">{{ $record->date }}</td>
                                <td class="px-4 py-2 border-b border-gray-300 print:border-black">{{ $record->check_in_time }}</td>
                                <td class="px-4 py-2 border-b border-gray-300 print:border-black">{{ $record->check_out_time }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-2 text-center border-b border-gray-300 print:border-black">No attendance records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

                    <!-- Signature Section (only shows on print) -->
        <div class="hidden print:block mt-16 text-left print:mt-20">
            <p class="mb-12">&nbsp;</p>
            <p>______________________________</p>
            <p class="mt-2">Authorized Signature</p>
        </div>

    <!-- Print Styles -->
    <style>
        @media print {
            body {
                background: white !important;
                color: black !important;
                font-size: 14px;
                line-height: 1.5;
            }

            .sidebar,
            .print\:hidden {
                display: none !important;
            }

            .print\:block {
                display: block !important;
            }

            .print\:ml-0 {
                margin-left: 0 !important;
            }

            .print\:p-12 {
                padding: 3rem !important;
            }

            .print\:p-0 {
                padding: 0 !important;
            }

            .print\:mt-20 {
                margin-top: 5rem !important;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            th, td {
                border: 1px solid #000 !important;
                padding: 8px;
                text-align: left;
            }

            th {
                background-color: #000 !important;
                color: #fff !important;
            }

            hr {
                border-top: 2px solid #000;
                margin: 10px 0;
            }
        }
    </style>
@endsection