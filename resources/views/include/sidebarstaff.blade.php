<div class="fixed h-screen bg-gray-900 text-white w-64 flex flex-col top-0 left-0 shadow-lg rounded-tr-2xl rounded-br-2xl md:w-1/4 lg:w-1/5">
    <!-- Gym Staff Profile Section -->
    <div class="p-4 bg-gradient-to-r from-gray-800 to-gray-700 flex items-center border-b border-gray-600 rounded-tr-2xl">
        @php
        $gymStaff = Auth::guard('gym_staff')->user();
    @endphp
        <div class="ml-4">
            @if ($gymStaff)
                <div class="text-lg font-bold">{{ $gymStaff->first_name . ' ' . $gymStaff->last_name }}</div>
                <div class="text-sm text-gray-400">{{ $gymStaff->email }}</div>
            @else
                <div class="text-lg font-bold">Guest</div>
            @endif
        </div>
    </div>
    <ul class="flex flex-col mt-4">
        <!-- Dashboard -->
        <li>
            <a href="{{ route('gym_staff.dashboard') }}" class="sidebar-button {{ request()->routeIs('gym_staff.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt text-white text-lg mr-3"></i>
                <span class="text-base font-medium">Dashboard</span>
            </a>
        </li>
       
        <!-- Member Management -->
        <li>
            <a href="{{ route('gym_staff.member_management') }}" class="sidebar-button {{ request()->routeIs('gym_staff.member_management') ? 'active' : '' }}">
                <i class="fas fa-users text-white text-lg mr-3"></i>
                <span class="text-base font-medium">Member Management</span>
            </a>
        </li>
        <!-- Payment -->
        <li>
            <a href="{{ route('gym_staff.payment.form') }}" class="sidebar-button {{ request()->routeIs('admin.payment.form') ? 'active' : '' }}">
                <i class="fas fa-money-bill-wave text-white text-lg mr-3"></i>
                <span class="text-base font-medium">Payment</span>
            </a>
        </li>
         <!-- Attendance -->
         <li>
            <a href="{{ route('gym_staff.attendance') }}" class="sidebar-button {{ request()->routeIs('gym_staff.attendance') ? 'active' : '' }}">
                        <i class="fas fa-calendar-check text-white text-lg mr-3"></i>
                        <span class="text-base font-medium">Attendance</span>
                    </a>
                </li>
        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-button w-full text-left">
                    <i class="fas fa-sign-out-alt text-white text-lg mr-3"></i>
                    <span class="text-base font-medium">Logout</span>
                </button>
            </form>
        </li>
    </ul>
</div>

<!-- Embedded CSS -->
<style>
    .sidebar-button {
        display: flex;
        align-items: center;
        padding: 12px;
        border-radius: 8px;
        transition: all 0.3s ease;
        background-color: #1f2937; /* bg-gray-800 */
        color: white;
        text-decoration: none;
    }
    
    .sidebar-button:hover {
        background: linear-gradient(to right, #374151, #4b5563); /* hover:bg-gradient-to-r hover:from-gray-700 hover:to-gray-600 */
    }
    
    .sidebar-button.active {
        background: linear-gradient(to right, #374151, #4b5563); /* bg-gradient-to-r from-gray-700 to-gray-600 */
        border-left: 4px solid #10b981; /* border-green-400 */
    }
    
    .sidebar-button i {
        margin-right: 12px;
    }

    @media (max-width: 768px) {
        .fixed {
            width: 100%; /* Full width on smaller screens */
            height: auto; /* Auto height */
            position: relative; /* Change position to relative */
        }
        .sidebar-button {
            justify-content: center; /* Center items on smaller screens */
        }
    }
</style>
