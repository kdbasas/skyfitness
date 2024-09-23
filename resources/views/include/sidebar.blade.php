<div class="fixed h-screen bg-gray-900 text-white w-64 flex flex-col top-0 left-0 shadow-lg rounded-tr-2xl rounded-br-2xl">
    <!-- Admin Profile Section -->
    <div class="p-4 bg-gradient-to-r from-gray-800 to-gray-700 flex items-center border-b border-gray-600 rounded-tr-2xl">
        @php
            $admin = Auth::user();
            $profileImagePath = $admin && $admin->profile_image 
                ? asset('storage/img/' . $admin->profile_image) 
                : asset('images/default-profile.png');
        @endphp
        <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-gray-500">
            <img src="{{ $profileImagePath }}" alt="Admin Profile Picture" class="w-full h-full object-cover">
        </div>
        <div class="ml-4">
            <div class="text-lg font-bold">{{ $admin ? $admin->name : 'Guest' }}</div>
            <div class="text-sm text-gray-400">{{ $admin ? $admin->email : '' }}</div>
        </div>
    </div>

    <!-- Sidebar Menu -->
    <ul class="mt-4 flex-grow flex flex-col space-y-1 px-4">
        <!-- Dashboard -->
        <li>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-button {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt text-white text-lg mr-3"></i>
                <span class="text-base font-medium">Dashboard</span>
            </a>
        </li>

        <!-- Admin Profile -->
        <li>
            <a href="{{ route('admin.profile') }}" class="sidebar-button {{ request()->routeIs('admin.profile') ? 'active' : '' }}">
                <i class="fas fa-user text-white text-lg mr-3"></i>
                <span class="text-base font-medium">Admin Profile</span>
            </a>
        </li>

        <!-- Member Management -->
        <li>
            <a href="{{ route('admin.member_management') }}" class="sidebar-button {{ request()->routeIs('admin.member_management') ? 'active' : '' }}">
                <i class="fas fa-users text-white text-lg mr-3"></i>
                <span class="text-base font-medium">Member Management</span>
            </a>
        </li>

        <!-- Subscription/Plan -->
        <li>
            <a href="{{ route('admin.subscription') }}" class="sidebar-button {{ request()->routeIs('admin.subscription') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt text-white text-lg mr-3"></i>
                <span class="text-base font-medium">Subscription</span>
            </a>
        </li>

        <!-- Payment -->
        <li>
            <a href="{{ route('admin.payment.form') }}" class="sidebar-button {{ request()->routeIs('admin.payment.form') ? 'active' : '' }}">
                <i class="fas fa-money-bill-wave text-white text-lg mr-3"></i>
                <span class="text-base font-medium">Payment</span>
            </a>
        </li>

        <!-- Staff Registration -->
        <li>
            <a href="#" class="sidebar-button {{ request()->routeIs('admin.registration') ? 'active' : '' }}">
                <i class="fas fa-user-plus text-white text-lg mr-3"></i>
                <span class="text-base font-medium">Staff</span>
            </a>
        </li>

        <!-- Inventory -->
        <li>
            <a href="{{ route('admin.equipment_inventory') }}" class="sidebar-button {{ request()->routeIs('admin.inventory') ? 'active' : '' }}">
                <i class="fas fa-dumbbell text-white text-lg mr-3"></i>
                <span class="text-base font-medium">Inventory</span>
            </a>
        </li>


        <!-- Report Analytics -->
        <li>
            <a href="{{ route('admin.reports') }}" class="sidebar-button {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                <i class="fas fa-chart-line text-white text-lg mr-3"></i>
                <span class="text-base font-medium">Report Analytics</span>
            </a>
        </li>

        <!-- Attendance -->
        <li>
            <a href="#" class="sidebar-button {{ request()->routeIs('admin.attendance') ? 'active' : '' }}">
                <i class="fas fa-calendar-check text-white text-lg mr-3"></i>
                <span class="text-base font-medium">Attendance</span>
            </a>
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
</style>
