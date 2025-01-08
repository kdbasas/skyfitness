@extends('layouts.app')

@section('content')
<div class="flex min-h-screen">
    <!-- Sidebar -->
    @include('include.sidebar')

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
            <form action="{{ route('admin.payment.add') }}" method="POST" class="space-y-4">
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

         <!-- Payment History -->
         <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-4">Payment History</h2>
            <div class="flex justify-between mb-4">
                <div class="flex space-x-4">
                    <input type="search" id="search" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" placeholder="Search...">
                    <button class="px-4 py-2 bg-[#1A1363] text-white rounded-lg shadow-md hover:bg-[#0f0c5c]">Search</button>
                </div>
                <div class="flex space-x-4">
                    <button class="px-4 py-2 bg-[#1A1363] text-white rounded-lg shadow-md hover:bg-[#0f0c5c]">Sort by Date</button>
                    <button class="px-4 py-2 bg-[#1A1363] text-white rounded-lg shadow-md hover:bg-[#0f0c5c]">Sort by Amount</button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100 text-left text-sm uppercase text-gray-600">
                            <th class="py-3 px-4 border">Member Name</th>
                            <th class="py-3 px-4 border">Subscription Plan</th>
                            <th class="py-3 px-4 border">Promo</th>
                            <th class="py-3 px-4 border">Amount</th>
                            <th class="py-3 px-4 border">Date Paid</th>
                            <th class="py-3 px-4 border">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr class="text-sm text-gray-800 hover:bg-gray-50">
                                <td class="py-3 px-4 border">{{ $payment->member->first_name }} {{ $payment->member->last_name }}</td>
                                <td class="py-3 px-4 border">{{ $payment->subscription->subscription_name }}</td>
                                <td class="py-3 px-4 border">{{ $payment->promo == 'Student' ? 'Student' : 'Regular' }}</td>
                                <td class="py-3 px-4 border">Php {{ number_format($payment->amount, 2) }}</td>
                                <td class="py-3 px-4 border">{{ \Carbon\Carbon::parse($payment->date_paid)->format('M d, Y') }}</td>
                                <td class="py-3 px-4 border">
                                    <div class="flex space-x-4">
                                        <a href="{{ route('admin.payment.edit', $payment->payment_id) }}" class="px-4 py-2 bg-[#1A1363] text-white rounded-lg shadow-md hover:bg-[#0f0c5c]">Edit</a>
                                        <form action="{{ route('admin.payment.delete', $payment->payment_id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg shadow-md hover:bg-red-700">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-3 px-4 border text-gray-500">No payment history available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
@endsection

@section('scripts')
<script>
    document.getElementById('subscription_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const amount = selectedOption.getAttribute('data-amount');
        const promo = document.getElementById('promo').value;
        let finalAmount = amount;

        if (promo === 'Student') {
            finalAmount -= 50; // Discount for Student promo
        } else if (promo === 'Regular') {
            finalAmount -= 0; // No discount for Regular promo
        }

        document.getElementById('amount').value = finalAmount;
    });

    document.getElementById('promo').addEventListener('change', function() {
        const subscriptionSelect = document.getElementById('subscription_id');
        const selectedOption = subscriptionSelect.options[subscriptionSelect.selectedIndex];
        const amount = selectedOption.getAttribute('data-amount');
        const promo = this.value;
        let finalAmount = amount;

        if (promo === 'Student') {
            finalAmount -= 50; // Discount for Student promo
        } else if (promo === 'Regular') {
            finalAmount -= 0; // No discount for Regular promo
        }

        document.getElementById('amount').value = finalAmount;
    });

    // Search functionality
    document.getElementById('search').addEventListener('input', function() {
        const searchValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const memberName = row.cells[0].textContent.toLowerCase();
            const subscriptionName = row.cells[1].textContent.toLowerCase();
            const promo = row.cells[2].textContent.toLowerCase();
            const amount = row.cells[3].textContent.toLowerCase();
            const datePaid = row.cells[4].textContent.toLowerCase();

            if (memberName.includes(searchValue) || subscriptionName.includes(searchValue) || promo.includes(searchValue) || amount.includes(searchValue) || datePaid.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Sort by date functionality
    document.querySelector('.sort-by-date').addEventListener('click', function() {
        const rows = document.querySelectorAll('tbody tr');
        const sortedRows = Array.from(rows).sort((a, b) => {
            const dateA = new Date(a.cells[4].textContent);
            const dateB = new Date(b.cells[4].textContent);

            return dateA - dateB;
        });

        document.querySelector('tbody').innerHTML = '';
        sortedRows.forEach(row => document.querySelector('tbody').appendChild(row));
    });

    // Sort by amount functionality
    document.querySelector('.sort-by-amount').addEventListener('click', function() {
        const rows = document.querySelectorAll('tbody tr');
        const sortedRows = Array.from(rows).sort((a, b) => {
            const amountA = parseFloat(a.cells[3].textContent.replace('Php ', '').replace(',', ''));
            const amountB = parseFloat(b.cells[3].textContent.replace('Php ', '').replace(',', ''));

            return amountA - amountB;
        });

        document.querySelector('tbody').innerHTML = '';
        sortedRows.forEach(row => document.querySelector('tbody').appendChild(row));
    });
</script>
@endsection