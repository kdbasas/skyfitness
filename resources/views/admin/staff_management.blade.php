@extends('layouts.app')

@section('content')
<div class="flex min-h-screen">
    <!-- Sidebar -->
    @include('include.sidebar')

    <!-- Main Content -->
    <div class="flex-1 ml-64 p-6 bg-[#ECE9E9]">
        <!-- Header Section -->
        <div class="flex items-center justify-center mb-6 p-4 bg-transparent text-[#1A1363]" style="margin-top: -20px;">
            <img src="{{ asset('img/logosky 2.png') }}" alt="Gym Logo" class="w-40 h-30 mr-4">
            <h1 class="text-4xl font-bold">ROXAS SKY FITNESS GYM</h1>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="bg-green-500 text-white p-4 rounded-lg mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-500 text-white p-4 rounded-lg mb-4">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Staff Registration Section -->
        <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
            <h2 class="text-2xl font-bold mb-4 text-[#1A1363] text-center">Register Staff</h2>

            <!-- Registration Form -->
            <form action="{{ route('admin.staff.add') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col">
                        <label for="email" class="text-sm font-medium text-black">Email</label>
                        <input type="email" id="email" name="email" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363] p-2" required autocomplete="off">
                    </div>
                    <div class="flex flex-col">
                        <label for="password" class="text-sm font-medium text-black">Password</label>
                        <input type="password" id="password" name="password" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363] p-2" required autocomplete="new-password">
                    </div>
                    <div class="flex flex-col">
                        <label for="first_name" class="text-sm font-medium text-black">First Name</label>
                        <input type="text" id="first_name" name="first_name" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363] p-2" required>
                    </div>
                    <div class="flex flex-col">
                        <label for="middle_name" class="text-sm font-medium text-black">Middle Name</label>
                        <input type="text" id="middle_name" name="middle_name" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363] p-2">
                    </div>
                    <div class="flex flex-col">
                        <label for="last_name" class="text-sm font-medium text-black">Last Name</label>
                        <input type="text" id="last_name" name="last_name" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363] p-2" required>
                    </div>
                    <div class="flex flex-col">
                        <label for="suffix_name" class="text-sm font-medium text-black">Suffix Name</label>
                        <input type="text" id="suffix_name" name="suffix_name" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363] p-2">
                    </div>
                    <div class="flex flex-col">
                        <label for="contact_number" class="text-sm font-medium text-black">Contact Number</label>
                        <input type="text" id="contact_number" name="contact_number" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363] p-2" required>
                    </div>
                    <div class="flex flex-col">
                        <label for="age" class="text-sm font-medium text-black">Age</label>
                        <input type="number" id="age" name="age" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363] p-2" required>
                    </div>
                    <div class="flex flex-col">
                        <label for="gender_id" class="text-sm font-medium text-black">Gender</label>
                        <select id="gender_id" name="gender_id" class="form-select mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363] p-2" required>
                            <option value="" disabled selected>Select Gender</option>
                            @foreach($genders as $gender)
                                <option value="{{ $gender->gender_id }}">{{ $gender->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col">
                        <label for="profile_image" class="text-sm font-medium text-black">Profile Image</label>
                        <input type="file" id="profile_image" name="profile_image" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363] p-2" accept="image/*">
                    </div>
                </div>
                
                <div class="flex justify-between mt-6">
                    <button type="submit" class="bg-[#1A1363] text-white font-bold py-2 px-4 rounded-lg hover:bg-[#0f0c5c] transition duration-300 ease-in-out">Register</button>
                    <button type="reset" class="px-4 py-2 bg-gray-400 text-white rounded-lg shadow-md hover:bg-gray-500">Cancel</button>
                </div>
            </form>
        </div>

        <!-- Staff List Section -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-6 text-[#1A1363]">Staff List</h2>
            <div class="flex justify-between mb-4">
                <form action="{{ route('admin.staff.management') }}" method="GET" class="w-full md:w-1/3 flex">
                    <input type="text" name="search" placeholder="Search Staff..." value="{{ request()->get('search') }}" class="form-input w-full px-4 py-2 rounded-l-lg border border-gray-300 focus:outline-none focus:ring-[#1A1363] focus:border-[#1A1363]">
                    <button type="submit" class="px-4 py-2 bg-[#1A1363] text-white rounded-r-lg hover:bg-[#160f70] focus:outline-none">
                        Search
                    </button>
                </form>
            </div>
            
            <table class="w-full bg-white border border-gray-300 rounded-lg shadow-md text-sm">
                <thead>
                    <tr class="bg-[#1A1363] text-white">
                        <th class="px-6 py-3 text-left">First Name</th>
                        <th class="px-6 py-3 text-left">Last Name</th>
                        <th class="px-6 py-3 text-left">Email</th>
                        <th class="px-6 py-3 text-left">Contact Number</th>
                        <th class="px-6 py-3 text-left">Role</th>
                        <th class="px-6 py-3 text-left">Staff Picture</th>
                        <th class="px-6 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gym_staffs as $staffMember)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 border-b">{{ $staffMember->first_name }}</td>
                            <td class="px-6 py-4 border-b">{{ $staffMember->last_name }}</td>
                            <td class="px-6 py-4 border-b">{{ $staffMember->email }}</td>
                            <td class="px-6 py-4 border-b">{{ $staffMember->contact_number }}</td>
                            <td class="px-6 py-4 border-b">{{ $staffMember->role }}</td>
                            <td class="px-6 py-4 border-b text-center">
                                @if($staffMember->profile_image)
                                    <img src="{{ asset('storage/' . $staffMember->profile_image) }}" alt="Profile Image" class="w-16 h-16 rounded-full">
                                @else
                                    <span>No Image</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 border-b text-center">
                                <a href="#" class="text-blue-500 hover:underline">Edit</a>
                                <form action="#" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center">No staff found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
