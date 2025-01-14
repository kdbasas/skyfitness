@extends('layouts.app')

@section('content')
<div class="flex min-h-screen">
    <!-- Sidebar -->
    @include('include.sidebarstaff')

    <!-- Main Content -->
    <div class="flex-1 ml-64 p-6 bg-[#ECE9E9]">
        <!-- Header Section -->
        <div class="flex items-center justify-center mb-6 p-4 bg-transparent text-[#1A1363]" style="margin-top: -20px;">
            <img src="{{ asset('img/logosky 2.png') }}" alt="Gym Logo" class="w-40 h-auto mr-4">
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
                <ul class="list-disc pl-6">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Payment Section -->
        <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
            <h1 class="text-4xl font-bold mb-4 text-yellow-500">Renew Your Subscription</h1>

            <!-- Payment Form -->
            <form action="{{ route('gym_staff.payment.add') }}" method="POST" class="space-y-4">
                @csrf
                <!-- Member Dropdown -->
                <div class="flex flex-col">
                    <label for="member_id" class="text-sm font-medium text-black">Member Name</label>
                    <select id="member_id" name="member_id" class="form-select mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
                        <option value="">Select a member</option>
                        @foreach($members as $member)
                            <option value="{{ $member->member_id }}">{{ $member->first_name }} {{ $member->last_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Subscription Dropdown -->
                <div class="flex flex-col">
                    <label for="subscription_id" class="text-sm font-medium text-black">Subscription Plan</label>
                    <select id="subscription_id" name="subscription_id" class="form-select mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
                        <option value="">Select a subscription plan</option>
                        @foreach($subscriptions as $subscription)
                            <option value="{{ $subscription->subscription_id }}" data-amount="{{ $subscription->amount }}">{{ $subscription->subscription_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Promo Dropdown -->
                <div class="flex flex-col">
                    <label for="promo" class="text-sm font-medium text-black">Promo</label>
                    <select id="promo" name="promo" class="form-select mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
                        <option value="" disabled selected>Select Promo</option>
                        <option value="Student">Student (₱450)</option>
                        <option value="Regular">Regular (₱500)</option>
                    </select>
                </div>

                <!-- Date of Payment -->
                <div class="flex flex-col">
                    <label for="date_paid" class="text-sm font-medium text-black">Date of Payment</label>
                    <input type="date" id="date_paid" name="date_paid" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
                </div>

                <!-- Form Buttons -->
                <div class="flex space-x-4 mt-4">
                    <button type="submit" class="px-4 py-2 bg-[#1A1363] text-white rounded-lg shadow-md hover:bg-[#0f0c5c]">Submit Payment</button>
                    <button type="reset" class ="px-4 py-2 bg-gray-400 text-white rounded-lg shadow-md hover:bg-gray-500">Cancel</button>
                </div>
            </form>
        </div>
@endsection