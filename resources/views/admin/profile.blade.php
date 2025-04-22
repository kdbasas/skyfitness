@extends('layouts.app')

@section('content')
<div class="flex min-h-screen">
    <!-- Sidebar -->
    @include('include.sidebar')
    
    <!-- Main Content -->
    <div class="flex-1 ml-64 px-4 py-6">
        <!-- Header Section -->
        <div class="flex items-center justify-center mb-6 p-4 bg-transparent text-[#1A1363]" style="margin-top: -20px;">
            <img src="{{ asset('img/logosky 2.png') }}" alt="Gym Logo" class="w-40 h-30 mr-4">
            <h1 class="text-4xl font-bold">ROXAS SKY FITNESS GYM</h1>
        </div>
        @if ($errors->any())
        <div class="mb-4">
            <ul class="text-red-500">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
        <!-- Page Title -->
        <h1 class="text-2xl font-bold mb-4 text-[#1A1363]">Admin Information</h1>

        <div class="flex space-x-6">
            <!-- Admin Profile Section -->
            <div class="w-1/3 bg-gray-800 p-4 rounded-lg shadow-lg">
                @php
                    $admin = Auth::user();
                    $profileImagePath = $admin && $admin->profile_image 
                        ? asset('storage/img/admin/' . $admin->profile_image) 
                        : asset('images/default-profile.png');
                @endphp
                <div class="flex items-center mb-4">
                    <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-600 flex items-center justify-center relative">
                        <img src="{{ $profileImagePath }}" alt="Admin Profile Picture" class="w-full h-full object-cover">
                        <!-- Edit Button -->
                        <button id="editProfilePicBtn" class="absolute bottom-0 right-3 bg-gray-900 text-white p-1 rounded-full shadow-md hover:bg-gray-700 opacity-90">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                </div>
                <!-- Edit Profile Picture Form -->
                <form id="editProfilePicForm" action="{{ route('admin.profile.update_picture') }}" method="POST" enctype="multipart/form-data" class="hidden">
                    @csrf
                    @method('PUT') <!-- Simulates the PUT request -->
                    <input type="file" id="profile_image" name="profile_image" accept="image/*" class="mb-4">
                    <button type="submit" class="px-4 py-2 bg-[#1A1363] text-white rounded-lg shadow-md hover:bg-[#1A1363]">Update Picture</button>
                </form>
                <div class="text-lg font-semibold text-white">{{ $admin ? $admin->name : 'Guest' }}</div>
                <div class="text-sm text-gray-400">{{ $admin ? $admin->email : '' }}</div>
            </div>

            <!-- Edit Profile Form -->
            <div class="w-2/3 bg-white p-4 rounded-lg shadow-lg">
                <h2 class="text-xl font-semibold mb-4 text-black font-roboto-bold">Edit Profile</h2>
                <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <!-- Name -->
                    <div class="flex flex-col">
                        <label for="name" class="text-sm font-medium text-black font-roboto-bold">Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $admin->name) }}" class="form-input mt-1 block w-full rounded-lg bg-gray-100 text-black border-gray-600 focus:border-[#1A1363] focus:ring-[#1A1363]">
                    </div>
                    
                    <!-- Email -->
                    <div class="flex flex-col">
                        <label for="email" class="text-sm font-medium text-black font-roboto-bold">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $admin->email) }}" class="form-input mt-1 block w-full rounded-lg bg-gray-100 text-black border-gray-600 focus:border-[#1A1363] focus:ring-[#1A1363]">
                    </div>

                    <!-- Password -->
                    <div class="flex flex-col">
                        <label for="password" class="text-sm font-medium text-black font-roboto-bold">New Password</label>
                        <input type="password" id="password" name="password" class="form-input mt-1 block w-full rounded-lg bg-gray-100 text-black border-gray-600 focus:border-[#1A1363] focus:ring-[#1A1363]">
                    </div>
                    
                    <!-- Confirm Password -->
                    <div class="flex flex-col">
                        <label for="password_confirmation" class="text-sm font-medium text-black font-roboto-bold">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-input mt-1 block w-full rounded-lg bg-gray-100 text-black border-gray-600 focus:border-[#1A1363] focus:ring-[#1A1363]">
                    </div>

                    <button type="submit" class="mt-4 px-4 py-2 bg-[#1A1363] text-white rounded-lg shadow-md hover:bg-[#1A1363] focus:outline-none focus:ring-2 focus:ring-[#1A1363]">Update Profile</button>
                </form>
            </div>
        </div>

       <!-- Register New Admin -->
<div class="mt-6 bg-white p-4 rounded-lg shadow-lg">
    <h2 class="text-xl font-semibold mb-4 text-black font-roboto-bold">Register New Admin</h2>
    <form action="{{ route('admin.register.new') }}" method="POST" class="space-y-4">
        @csrf

        <!-- Name -->
        <div class="flex flex-col">
            <label for="register_name" class="text-sm font-medium text-black font-roboto-bold">Name</label>
            <input type="text" id="register_name" name="register_name" class="form-input mt-1 block w-full rounded-lg bg-gray-100 text-black border-gray-600 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
        </div>
        
        <!-- Email -->
        <div class="flex flex-col">
            <label for="register_email" class="text-sm font-medium text-black font-roboto-bold">Email</label>
            <input type="email" id="register_email" name="register_email" class="form-input mt-1 block w-full rounded-lg bg-gray-100 text-black border-gray-600 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
        </div>

        <!-- Password -->
        <div class="flex flex-col">
            <label for="register_password" class="text-sm font-medium text-black font-roboto-bold">Password</label>
            <input type="password" id="register_password" name="register_password" class="form-input mt-1 block w-full rounded-lg bg-gray-100 text-black border-gray-600 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
        </div>

        <!-- Confirm Password -->
        <div class="flex flex-col">
            <label for="register_password_confirmation" class="text-sm font-medium text-black font-roboto-bold">Confirm Password</label>
            <input type="password" id="register_password_confirmation" name="register_password_confirmation" class="form-input mt-1 block w-full rounded-lg bg-gray-100 text-black border-gray-600 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
        </div>

        <button type="submit" class="mt-4 px-4 py-2 bg-[#1A1363] text-white rounded-lg shadow-md hover:bg-[#1A1363] focus:outline-none focus:ring-2 focus:ring-[#1A1363]">Register Admin</button>
    </form>
</div>
       <!-- Admin List Section -->
<div class="mt-6 bg-white p-4 rounded-lg shadow-lg">
    <h2 class="text-2xl font-semibold mb-4 text-[#1A1363]">Registered Admins</h2>
    <div class="overflow-x-auto">
        @if($admins->isNotEmpty())
            <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md">
                <thead class="bg-[#1A1363] text-white">
                    <tr>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-gray-200 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-gray-200 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-gray-200 uppercase tracking-wider">Created At</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-gray-200 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($admins as $adminUser )
                        <tr class="hover:bg-gray-100 transition duration-200">
                            <td class="px-6 py-4 border-b border-gray-300">{{ $adminUser ->name }}</td>
                            <td class="px-6 py-4 border-b border-gray-300">{{ $adminUser ->email }}</td>
                            <td class="px-6 py-4 border-b border-gray-300">{{ $adminUser ->created_at->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 border-b border-gray-300">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-gray-500 text-center py-4">No admins found.</p>
        @endif
    </div>
</div>
<style>
    table {
        border-collapse: collapse;
        width: 100%;
    }
    th, td {
        padding: 12px;
        text-align: left;
    }
    th {
        background-color: #1A1363;
        color: white;
    }
    tr:hover {
        background-color: #f5f5f5;
    }
</style>
<!-- JavaScript for toggling profile picture form -->
<script>
    document.getElementById('editProfilePicBtn').addEventListener('click', function() {
        var form = document.getElementById('editProfilePicForm');
        form.classList.toggle('hidden');
    });
</script>
@endsection