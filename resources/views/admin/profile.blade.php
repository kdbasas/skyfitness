@extends('layouts.app')

@section('content')
<div class="flex min-h-screen">
    <!-- Sidebar -->
    @include('include.sidebar')
    
    <!-- Main Content -->
    <div class="flex-1 ml-64 px-4 py-6">
        <!-- Header Section -->
        <div class="flex items-center justify-center mb-6 p-4 bg-transparent text-[#1A1363]"style="margin-top: -20px;">
            <img src="{{ asset('img/logosky 2.png') }}" alt="Gym Logo" class="w-40 h-30 mr-4">
            <h1 class="text-4xl font-bold">ROXAS SKY FITNESS GYM</h1>
        </div>

        <!-- Page Title -->
        <h1 class="text-2xl font-bold mb-4 text-[#1A1363]">Admin Information</h1>

        <div class="flex space-x-6">
            <!-- Admin Profile Section -->
            <div class="w-1/3 bg-gray-800 p-4 rounded-lg shadow-lg">
                @php
                    $admin = Auth::user();
                    $profileImagePath = $admin && $admin->profile_image 
                        ? asset('storage/img/' . $admin->profile_image) 
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
            <form action="#" method="POST" class="space-y-4">
                @csrf

                <!-- Name -->
                <div class="flex flex-col">
                    <label for="new_admin_name" class="text-sm font-medium text-black font-roboto-bold">Name</label>
                    <input type="text" id="new_admin_name" name="name" class="form-input mt-1 block w-full rounded-lg bg-gray-100 text-black border-gray-600 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
                </div>
                
                <!-- Email -->
                <div class="flex flex-col">
                    <label for="new_admin_email" class="text-sm font-medium text-black font-roboto-bold">Email</label>
                    <input type="email" id="new_admin_email" name="email" class="form-input mt-1 block w-full rounded-lg bg-gray-100 text-black border-gray-600 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
                </div>

                <!-- Password -->
                <div class="flex flex-col">
                    <label for="new_admin_password" class="text-sm font-medium text-black font-roboto-bold">Password</label>
                    <input type="password" id="new_admin_password" name="password" class="form-input mt-1 block w-full rounded-lg bg-gray-100 text-black border-gray-600 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
                </div>

                <!-- Confirm Password -->
                <div class="flex flex-col">
                    <label for="new_admin_password_confirmation" class="text-sm font-medium text-black font-roboto-bold">Confirm Password</label>
                    <input type="password" id="new_admin_password_confirmation" name="password_confirmation" class="form-input mt-1 block w-full rounded-lg bg-gray-100 text-black border-gray-600 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
                </div>

                <button type="submit" class="mt-4 px-4 py-2 bg-[#1A1363] text-white rounded-lg shadow-md hover:bg-[#1A1363] focus:outline-none focus:ring-2 focus:ring-[#1A1363]">Register Admin</button>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for toggling profile picture form -->
<script>
    document.getElementById('editProfilePicBtn').addEventListener('click', function() {
        var form = document.getElementById('editProfilePicForm');
        form.classList.toggle('hidden');
    });
</script>
@endsection
