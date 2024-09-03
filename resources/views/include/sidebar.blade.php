<div class="fixed h-screen bg-gray-900 text-white w-64 flex flex-col top-0 left-0 shadow-lg">
    <!-- Admin Profile Section -->
    <div class="p-4 bg-gray-800 flex items-center border-b border-gray-700">
        @php
            $admin = Auth::user();
            $profileImagePath = $admin && $admin->profile_image 
                ? asset('storage/img/' . $admin->profile_image) 
                : asset('images/default-profile.png');
        @endphp
        <div class="w-16 h-16 rounded-full overflow-hidden bg-gray-600 flex items-center justify-center">
            <img src="{{ $profileImagePath }}" alt="Admin Profile Picture" class="w-full h-full object-cover">
        </div>
        <div class="ml-4">
            <div class="text-lg font-semibold">{{ $admin ? $admin->name : 'Guest' }}</div>
            <div class="text-sm text-gray-400">{{ $admin ? $admin->email : '' }}</div>
        </div>
    </div>

    <!-- Sidebar Menu -->
    <ul class="mt-4 flex-grow flex flex-col space-y-1">
        <!-- Dashboard -->
        <li class="group relative py-3 px-4 rounded-lg transition-all duration-200 hover:bg-gray-700 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700' : '' }}">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center text-white text-sm transition-all duration-200 group-hover:text-gray-300">
                <i class="fas fa-tachometer-alt mr-4 text-xl transition-transform duration-200 group-hover:scale-110"></i>
                <span class="text-base font-medium">Dashboard</span>
            </a>
        </li>
        
        <!-- Admin Profile -->
        <li class="group relative py-3 px-4 rounded-lg transition-all duration-200 hover:bg-gray-700 {{ request()->routeIs('admin.profile') ? 'bg-gray-700' : '' }}">
            <a href="{{ route('admin.profile') }}" class="flex items-center text-white text-sm transition-all duration-200 group-hover:text-gray-300">
                <i class="fas fa-user mr-4 text-xl transition-transform duration-200 group-hover:scale-110"></i>
                <span class="text-base font-medium">Admin Profile</span>
            </a>
        </li>

        <!-- Member Management -->
        <li class="group relative py-3 px-4 rounded-lg transition-all duration-200 hover:bg-gray-700 {{ request()->routeIs('admin.members') ? 'bg-gray-700' : '' }}">
            <a href="{{ route('admin.member_management') }}" class="flex items-center text-white text-sm transition-all duration-200 group-hover:text-gray-300">
                <i class="fas fa-users mr-4 text-xl transition-transform duration-200 group-hover:scale-110"></i>
                <span class="text-base font-medium">Member Management</span>
            </a>
        </li>

        <!-- Subscription/Plan -->
        <li class="group relative py-3 px-4 rounded-lg transition-all duration-200 hover:bg-gray-700 {{ request()->routeIs('admin.subscriptions') ? 'bg-gray-700' : '' }}">
            <a href="{{ route('admin.subscription') }}" class="flex items-center text-white text-sm transition-all duration-200 group-hover:text-gray-300">
                <i class="fas fa-calendar-alt mr-4 text-xl transition-transform duration-200 group-hover:scale-110"></i>
                <span class="text-base font-medium">Subscription</span>
            </a>
        </li>

        <!-- Payment -->
        <li class="group relative py-3 px-4 rounded-lg transition-all duration-200 hover:bg-gray-700 {{ request()->routeIs('admin.payment.form') ? 'bg-gray-700' : '' }}">
            <a href="{{ route('admin.payment.form') }}" class="flex items-center text-white text-sm transition-all duration-200 group-hover:text-gray-300">
                <i class="fas fa-money-bill-wave mr-4 text-xl transition-transform duration-200 group-hover:scale-110"></i>
                <span class="text-base font-medium">Payment</span>
            </a>
        </li>

        <!-- Registration -->
        <li class="group relative py-3 px-4 rounded-lg transition-all duration-200 hover:bg-gray-700 {{ request()->routeIs('admin.registration') ? 'bg-gray-700' : '' }}">
            <a href="#" class="flex items-center text-white text-sm transition-all duration-200 group-hover:text-gray-300">
                <i class="fas fa-user-plus mr-4 text-xl transition-transform duration-200 group-hover:scale-110"></i>
                <span class="text-base font-medium">Staff</span>
            </a>
        </li>

        <!-- Inventory -->
        <li class="group relative py-3 px-4 rounded-lg transition-all duration-200 hover:bg-gray-700 {{ request()->routeIs('admin.inventory') ? 'bg-gray-700' : '' }}">
            <a href="#" class="flex items-center text-white text-sm transition-all duration-200 group-hover:text-gray-300">
                <i class="fas fa-dumbbell mr-4 text-xl transition-transform duration-200 group-hover:scale-110"></i>
                <span class="text-base font-medium">Inventory</span>
            </a>
        </li>

        <!-- Report Analytics -->
        <li class="group relative py-3 px-4 rounded-lg transition-all duration-200 hover:bg-gray-700 {{ request()->routeIs('admin.reports') ? 'bg-gray-700' : '' }}">
            <a href="#" class="flex items-center text-white text-sm transition-all duration-200 group-hover:text-gray-300">
                <i class="fas fa-chart-line mr-4 text-xl transition-transform duration-200 group-hover:scale-110"></i>
                <span class="text-base font-medium">Report Analytics</span>
            </a>
        </li>

        <!-- Attendance -->
        <li class="group relative py-3 px-4 rounded-lg transition-all duration-200 hover:bg-gray-700 {{ request()->routeIs('admin.attendance') ? 'bg-gray-700' : '' }}">
            <a href="#" class="flex items-center text-white text-sm transition-all duration-200 group-hover:text-gray-300">
                <i class="fas fa-calendar-check mr-4 text-xl transition-transform duration-200 group-hover:scale-110"></i>
                <span class="text-base font-medium">Attendance</span>
            </a>
        </li>
    </ul>
</div>
