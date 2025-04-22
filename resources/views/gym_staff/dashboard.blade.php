@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-[#ECE9E9]">
    <!-- Sidebar -->
    @include('include.sidebarstaff')

     <!-- Main Content -->
     <div class="flex-1 ml-64 px-4 py-6">
        <!-- Header Section -->
        <div class="flex items-center justify-center mb-6 p-6 bg-transparent text-[#1A1363]" style="margin-top: -20px;">
            <img src="{{ asset('img/logosky 2.png') }}" alt="Gym Logo" class="w-40 h-30 mr-4">
            <h1 class="text-4xl font-bold">ROXAS SKY FITNESS GYM</h1>
        </div>
        <!-- Welcome Message -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-[#1A1363]">Welcome, {{ Auth::guard('gym_staff')->user()->first_name }}</h1>
            <p class="text-gray-600 mt-2">Here's what's happening at the gym today:</p>
        </div>

        <!-- Dashboard Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Active Members -->
            <div class="bg-white shadow-lg rounded-lg p-6 border-l-4 border-green-500">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Active Members</h2>
                <ul>
                    @forelse($activeMembers as $member)
                        <li class="flex justify-between py-2 border-b border-gray-200">
                            <span>{{ $member->first_name }} {{ $member->last_name }}</span>
                            <span class="text-green-500 font-medium">Active</span>
                        </li>
                    @empty
                        <p class="text-gray-500">No active members.</p>
                    @endforelse
                </ul>
            </div>

            <!-- Expired Members -->
            <div class="bg-white shadow-lg rounded-lg p-6 border-l-4 border-red-500">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Expired Members</h2>
                <ul>
                    @forelse($expiredMembers as $member)
                        <li class="flex justify-between py-2 border-b border-gray-200">
                            <span>{{ $member->first_name }} {{ $member->last_name }}</span>
                            <span class="text-red-500 font-medium">Expired</span>
                        </li>
                    @empty
                        <p class="text-gray-500">No expired members.</p>
                    @endforelse
                </ul>
            </div>

            <!-- Expiring Members -->
            <div class="bg-white shadow-lg rounded-lg p-6 border-l-4 border-orange-500">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Expiring Members (5 Days)</h2>
                <ul>
                    @forelse($expiringMembers as $member)
                        <li class="flex justify-between py-2 border-b border-gray-200">
                            <span>{{ $member->first_name }} {{ $member->last_name }}</span>
                            <span class="text-orange-500 font-medium">Expires in {{ $member->days_until_expiration }} days</span>
                        </li>
                    @empty
                        <p class="text-gray-500">No members expiring within 5 days.</p>
                    @endforelse
                </ul>
            </div>
        </div>
        <!-- Calendar Section -->
<div class="mt-8">
    <div class="bg-white shadow-lg rounded-lg p-6 border-l-4 border-purple-500">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
            <span class="mr-2">📅</span> Calendar
        </h2>
        <div class="calendar-container max-w-md mx-auto">
            <div class="flex justify-between items-center mb-4 bg-gradient-to-r from-purple-100 to-pink-100 p-3 rounded-lg">
                <h3 class="text-lg font-semibold flex items-center" id="monthAndYear"></h3>
                <div class="flex space-x-2">
                    <button onclick="previous()" class="w-8 h-8 flex items-center justify-center bg-white rounded-full shadow-md hover:bg-gray-50 transition-colors duration-200">
                        <span class="text-xl">⬅️</span>
                    </button>
                    <button onclick="next()" class="w-8 h-8 flex items-center justify-center bg-white rounded-full shadow-md hover:bg-gray-50 transition-colors duration-200">
                        <span class="text-xl">➡️</span>
                    </button>
                </div>
            </div>
            <table class="w-full border-collapse rounded-lg overflow-hidden">
                <thead>
                    <tr class="bg-gradient-to-r from-blue-100 to-purple-100">
                        <th class="p-3 text-center">Sun</th>
                        <th class="p-3 text-center">Mon</th>
                        <th class="p-3 text-center">Tue</th>
                        <th class="p-3 text-center">Wed</th>
                        <th class="p-3 text-center">Thu</th>
                        <th class="p-3 text-center">Fri</th>
                        <th class="p-3 text-center">Sat</th>
                    </tr>
                </thead>
                <tbody id="calendar-body" class="bg-white"></tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .calendar-day {
        position: relative;
        height: 3.5rem;
        transition: all 0.2s ease-in-out;
    }

    .calendar-day:hover:not(.empty-day) {
        background-color: #f3f4f6;
        transform: scale(1.1);
        z-index: 10;
        border-radius: 50%;
    }

    .today-cell {
        background: linear-gradient(45deg, #6366f1, #8b5cf6);
        color: white;
        border-radius: 50%;
        transform: scale(1.1);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .empty-day {
        background-color: #f9fafb;
    }
</style>
       <!-- Attendance Management -->
<div class="mt-8">
    <div class="bg-white shadow-lg rounded-lg p-6 border-l-4 border-blue-500">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Attendance Management For Today!</h2>
        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200 text-gray-700">
                    <th class="p-3 border border-gray-300">Member Name</th>
                    <th class="p-3 border border-gray-300">Date</th>
                    <th class="p-3 border border-gray-300">Check-in Time</th>
                    <th class="p-3 border border-gray-300">Check-out Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendance as $record)
                    <tr class="text-gray-800">
                        <td class="p-3 border border-gray-300">{{ $record->member->first_name }} {{ $record->member->last_name }}</td>
                        <td class="p-3 border border-gray-300">{{ $record->date }}</td>
                        <td class="p-3 border border-gray-300 text-green-500 font-medium">
                            {{ $record->check_in_time ?? 'Not Checked In' }}
                        </td>
                        <td class="p-3 border border-gray-300 text-red-500 font-medium">
                            {{ $record->check_out_time ?? 'Not Checked Out' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-3 border border-gray-300 text-center text-gray-500">No attendance records available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<script>
    let today = new Date();
    let currentMonth = today.getMonth();
    let currentYear = today.getFullYear();
    
    const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    const emojis = {
        month: ["❄️", "💝", "🌸", "🌺", "🌻", "☀️", "🌞", "🌊", "🍁", "🎃", "🍂", "🎄"]
    };

    function showCalendar(month, year) {
        let firstDay = new Date(year, month).getDay();
        let daysInMonth = 32 - new Date(year, month, 32).getDate();

        let tbl = document.getElementById("calendar-body");
        tbl.innerHTML = "";

        // Set month and year with emoji
        document.getElementById("monthAndYear").innerHTML = `
            <span class="mr-2">${emojis.month[month]}</span>
            <span>${months[month]} ${year}</span>
        `;

        let date = 1;
        for (let i = 0; i < 6; i++) {
            let row = document.createElement("tr");

            for (let j = 0; j < 7; j++) {
                let cell = document.createElement("td");
                cell.classList.add("calendar-day", "text-center", "border");

                if (i === 0 && j < firstDay) {
                    cell.classList.add("empty-day");
                    cell.innerHTML = "";
                    row.appendChild(cell);
                }
                else if (date > daysInMonth) {
                    break;
                }
                else {
                    let cellContent = document.createElement("div");
                    cellContent.classList.add("h-full", "flex", "items-center", "justify-center");
                    
                    if (date === today.getDate() && year === today.getFullYear() && month === today.getMonth()) {
                        cell.classList.add("today-cell");
                        cellContent.innerHTML = `${date} 📍`;
                    } else {
                        cellContent.textContent = date;
                    }

                    cell.appendChild(cellContent);
                    row.appendChild(cell);
                    date++;
                }
            }
            tbl.appendChild(row);
            if (date > daysInMonth) {
                break;
            }
        }
    }

    function next() {
        currentYear = (currentMonth === 11) ? currentYear + 1 : currentYear;
        currentMonth = (currentMonth + 1) % 12;
        showCalendar(currentMonth, currentYear);
    }

    function previous() {
        currentYear = (currentMonth === 0) ? currentYear - 1 : currentYear;
        currentMonth = (currentMonth === 0) ? 11 : currentMonth - 1;
        showCalendar(currentMonth, currentYear);
    }

    // Show calendar when page loads
    document.addEventListener('DOMContentLoaded', function() {
        showCalendar(currentMonth, currentYear);
    });
</script>
@endsection
