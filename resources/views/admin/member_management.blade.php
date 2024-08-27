@extends('layouts.app')

@section('content')
<div class="flex min-h-screen">
    <!-- Sidebar -->
    @include('include.sidebar')

    <!-- Main Content -->
    <div class="flex-1 ml-64 p-6 bg-[#ECE9E9]">
        <!-- Header Section -->
        <div class="flex items-center justify-center mb-6 p-4 bg-[#1A1363] text-white">
            <img src="{{ asset('img/logosky 2.png') }}" alt="Gym Logo" class="w-40 h-30 mr-4">
            <h1 class="text-3xl font-bold">SKY FITNESS GYM</h1>
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

        <!-- Become a Member Section -->
        <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
            <h1 class="text-4xl font-bold mb-4 text-yellow-500">Become a Member</h1>
            <h2 class="text-2xl font-bold mb-4 text-[#1A1363]">Registration</h2>

            <!-- Registration Form -->
            <form action="{{ route('admin.member.add') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <!-- First Name -->
                    <div class="flex flex-col">
                        <label for="first_name" class="text-sm font-medium text-black">First Name</label>
                        <input type="text" id="first_name" name="first_name" autocomplete="given-name" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
                    </div>

                    <!-- Middle Name -->
                    <div class="flex flex-col">
                        <label for="middle_name" class="text-sm font-medium text-black">Middle Name</label>
                        <input type="text" id="middle_name" name="middle_name" autocomplete="additional-name" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]">
                    </div>

                    <!-- Last Name -->
                    <div class="flex flex-col">
                        <label for="last_name" class="text-sm font-medium text-black">Last Name</label>
                        <input type="text" id="last_name" name="last_name" autocomplete="family-name" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
                    </div>

                    <!-- Suffix -->
                    <div class="flex flex-col">
                        <label for="suffix_name" class="text-sm font-medium text-black">Suffix</label>
                        <input type="text" id="suffix_name" name="suffix_name" autocomplete="honorific-suffix" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]">
                    </div>

                    <!-- Date of Join -->
                    <div class="flex flex-col">
                        <label for="date_joined" class="text-sm font-medium text-black">Date of Join</label>
                        <input type="date" id="date_joined" name="date_joined" autocomplete="bday" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
                    </div>

                    <!-- Email Address -->
                    <div class="flex flex-col">
                        <label for="email" class="text-sm font-medium text-black">Email Address</label>
                        <input type="email" id="email" name="email" autocomplete="email" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
                    </div>

                    <!-- Contact Number -->
                    <div class="flex flex-col">
                        <label for="contact_number" class="text-sm font-medium text-black">Contact Number</label>
                        <input type="tel" id="contact_number" name="contact_number" autocomplete="tel" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
                    </div>

                    <!-- Subscription -->
                    <div class="flex flex-col">
                        <label for="subscription_id" class="text-sm font-medium text-black">Subscription</label>
                        <select id="subscription_id" name="subscription_id" class="form-select mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
                            <option value="">Select a subscription</option>
                            @foreach($subscriptions as $subscription)
                                <option value="{{ $subscription->subscription_id }}" data-price="{{ $subscription->amount }}">{{ $subscription->subscription_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Price -->
                    <div class="flex flex-col">
                        <label for="amount" class="text-sm font-medium text-black">Price</label>
                        <input type="text" id="amount" name="amount" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" readonly>
                    </div>
                </div>

                <!-- Form Buttons -->
                <div class="flex space-x-4 mt-4">
                    <button type="submit" class="px-4 py-2 bg-[#1A1363] text-white rounded-lg shadow-md hover:bg-[#0f0c5c]">Register</button>
                    <button type="reset" class="px-4 py-2 bg-gray-400 text-white rounded-lg shadow-md hover:bg-gray-500">Cancel</button>
                </div>
            </form>
        </div>

        <!-- Member List Section -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-4 text-[#1A1363]">Member's List</h2>

            <table class="w-full bg-white border border-gray-300 rounded-lg shadow-md">
                <thead>
                    <tr class="bg-[#1A1363] text-white">
                        <th class="px-4 py-2 text-left">First Name</th>
                        <th class="px-4 py-2 text-left">Last Name</th>
                        <th class="px-4 py-2 text-left">Subscription</th>
                        <th class="px-4 py-2 text-left">Contact Number</th>
                        <th class="px-4 py-2 text-left">Date Joined</th>
                        <th class="px-4 py-2 text-left">Date Expired</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                        <tr>
                            <td class="px-4 py-2">{{ $member->first_name }}</td>
                            <td class="px-4 py-2">{{ $member->last_name }}</td>
                            <td class="px-4 py-2">{{ $member->subscription->subscription_name }}</td>
                            <td class="px-4 py-2">{{ $member->contact_number }}</td>
                            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($member->date_joined)->format('M d, Y') }}</td>
                            <td class="px-4 py-2">{{ $member->date_expired ? \Carbon\Carbon::parse($member->date_expired)->format('M d, Y') : 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-2 text-center">No members found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const subscriptionSelect = document.getElementById('subscription_id');
    const amountInput = document.getElementById('amount');

    subscriptionSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const price = selectedOption.getAttribute('data-price');

        if (price) {
            amountInput.value = `₱${parseFloat(price).toFixed(2)}`;
        } else {
            amountInput.value = '';
        }
    });
});
</script>
@endsection
