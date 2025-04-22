@extends('layouts.app')

@section('content')
<div class="flex min-h-screen">
    <!-- Sidebar -->
    @include('include.sidebarstaff')
     <!-- Select2 CSS -->
     <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

     <!-- jQuery (required by Select2) -->
     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
 
     <!-- Select2 JS -->
     <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
 

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
                    <select id="member_id" name="member_id" class="select2-dropdown mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
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

        <!-- Payment History Section -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-4 text-[#1A1363]">Payment History</h2>

            <div class="flex justify-between mb-4">
                <div class="flex space-x-4">
                    <form method="GET" action="{{ route('gym_staff.payment.form') }}" class="flex items-center">
                        <label for="date" class="mr-2">Select Date:</label>
                        <input type="date" id="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()" class="mr-2">
                        <button type="submit" class="px-4 py-2 bg-[#1A1363] text-white rounded-lg hover:bg-[#0f0c5c]">Sort</button>
                    </form>
                </div>

                <!-- Search Form -->
                <form action="{{ route('gym_staff.payment.form') }}" method="GET" class="flex items-center">
                    <input type="hidden" name="date" value="{{ $selectedDate }}"> <!-- Preserve the selected date -->
                    <input type="text" name="search" placeholder="Search Payments..." value="{{ request()->get('search') }}" class="form-input w-full px-4 py-2 rounded-l-lg border border-gray-300 focus:outline-none focus:ring-[#1A1363] focus:border-[#1A1363]">
                    <button type="submit" class="px-4 py-2 bg-[#1A1363] text-white rounded-r-lg hover:bg-[#160f70] focus:outline-none">
                        Search
                    </button>
                </form>
                <!-- Sorting Dropdown -->
                <form method="GET" action="{{ route('gym_staff.payment.form') }}" class="flex items-center ml-4">
                    <input type="hidden" name="date" value="{{ $selectedDate }}"> <!-- Preserve the selected date -->
                    <select name="sort" id="sort" class="form-select rounded-lg border border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363] p-2" onchange="this.form.submit()">
                        <option value="" disabled selected>Sort By</option>
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest Payment</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest Payment</option>
                        <option value="registration" {{ request('sort') == 'registration' ? 'selected' : '' }}>Registration</option>
                        <option value="renewal" {{ request('sort') == 'renewal' ? 'selected' : '' }}>Renewal</option>
                    </select>
                    <button type="button" onclick="clearSort()" class="ml-2 px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">Clear Sort</button>
                </form>
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
                            <th class="py-3 px-4 border">Status</th>
                            <th class="py-3 px-4 border">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr class="text-sm text-gray-800 hover:bg-gray-50">
                                <td class="py-3 px-4 border">{{ $payment->member->first_name }} {{ $payment->member->last_name }}</td>
                                <td class="py-3 px-4 border">{{ $payment->subscription->subscription_name }}</td>
                                <td class="py-3 px-4 border">{{ $payment->promo }}</td>
                                <td class="py-3 px-4 border">Php {{ number_format($payment->amount, 2) }}</td>
                                <td class="py-3 px-4 border">{{ \Carbon\Carbon::parse($payment->date_paid)->format('M d, Y') }}</td>
                                <td class="py-3 px-4 border">{{ $payment->status }}</td>
                                <td class="py-3 px-4 border">
                                    <div class="flex space-x-4">
                                        <button 
                                            onclick="openEditPopup({{ $payment->payment_id }}, '{{ $payment->member->first_name }} {{ $payment->member->last_name }}', '{{ $payment->subscription->subscription_id }}', '{{ $payment->promo }}', {{ $payment->amount }}, '{{ \Carbon\Carbon::parse($payment->date_paid)->format('Y-m-d') }}')" 
                                            class="px-4 py-2 bg-[#1A1363] text-white rounded-lg shadow-md hover:bg-[#0f0c5c]"
                                        >
                                            Edit
                                        </button>
                                        <button 
                                            onclick="openDeletePopup({{ $payment->payment_id }}, '{{ $payment->member->first_name }} {{ $payment->member->last_name }}')" 
                                            class="px-4 py-2 bg-red-500 text-white rounded-lg shadow-md hover:bg-red-700"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-3 px-4 border text-gray-500">No payment history available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
                    <!-- Edit Payment Pop-Up -->
            <div id="edit-popup" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-75 hidden">
                <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg w-full">
                    <h2 class="text-2xl font-bold mb-4 text-[#1A1363]">Edit Payment</h2>
                    <form id="edit-form" action="{{ route('gym_staff.payment.update', '') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="edit_payment_id" name="payment_id">
                        <div class="space-y-4">
                            <!-- Member Name (Read-Only) -->
                            <div class="flex flex-col">
                                <label for="edit_member_name" class="text-sm font-medium text-black">Member Name</label>
                                <input type="text" id="edit_member_name" name="member_name" class="mt-1 block w-full border border-gray-300 rounded-lg p-2 bg-gray-100" readonly>
                            </div>

                            <!-- Subscription Dropdown -->
                            <div class="flex flex-col">
                                <label for="edit_subscription" class="text-sm font-medium text-black">Subscription Plan</label>
                                <select id="edit_subscription" name="subscription_id" class="form-select mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
                                    @foreach($subscriptions as $subscription)
                                        <option value="{{ $subscription->subscription_id }}">{{ $subscription->subscription_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Promo Dropdown -->
                            <div class="flex flex-col">
                                <label for="edit_promo" class="text-sm font-medium text-black">Promo</label>
                                <select id="edit_promo" name="promo" class="form-select mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
                                    <option value="Student">Student (₱450)</option>
                                    <option value="Regular">Regular (₱500)</option>
                                </select>
                            </div>

                            <!-- Date Paid -->
                            <div class="flex flex-col">
                                <label for="edit_date_paid" class="text-sm font-medium text-black">Date Paid</label>
                                <input type="date" id="edit_date_paid" name="date_paid" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
                            </div>
                        </div>
                        <div class="flex space-x-4 mt-4">
                            <button type="submit" class="px-4 py-2 bg-[#1A1363] text-white rounded-lg shadow-md hover:bg-[#0f0c5c]">Update</button>
                            <button type="button" onclick="closeEditPopup()" class="px-4 py-2 bg-gray-400 text-white rounded-lg shadow-md hover:bg-gray-500">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
            <div id="delete-popup" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h2 class="text-xl font-bold mb-4">Delete Payment</h2>
                    <p>Are you sure you want to delete the payment for <span id="delete_payment_name" class="font-semibold"></span>?</p>
                    <form id="delete-form" action="" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="flex justify-end mt-4">
                            <button type="submit" class="bg-red-500 text-white rounded-lg px-4 py-2">Delete</button>
                            <button type="button" onclick="closeDeletePopup()" class="ml-2 bg-gray-400 text-white rounded-lg px-4 py-2">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
            <style>
                .select2-container--default .select2-selection--single {
                    height: 2.5rem;
                    padding: 0.5rem;
                    border-radius: 0.5rem;
                    border: 1px solid #d1d5db; /* Tailwind gray-300 */
                }
                .select2-container--default .select2-selection--single .select2-selection__rendered {
                    line-height: 1.5rem;
                }
            </style>

            <script>
                function clearSort() {
                    window.location.href = "{{ route('gym_staff.payment.form') }}";
                }

                function openEditPopup(id, memberName, subscriptionPlan, promo, amount, datePaid) {
                    document.getElementById('edit_payment_id').value = id;
                    document.getElementById('edit_member_name').value = memberName; // Set the member name
                    document.getElementById('edit_subscription').value = subscriptionPlan; // Set the subscription plan
                    document.getElementById('edit_promo').value = promo; // Set the promo
                    document.getElementById('edit_date_paid').value = datePaid; // Set the date paid
                    document.getElementById('edit-form').action = `{{ route('gym_staff.payment.update', ':id') }}`.replace(':id', id); // Set the form action URL
                    document.getElementById('edit-popup').classList.remove('hidden'); // Show the pop-up
                }

                function closeEditPopup() {
                    document.getElementById('edit-popup').classList.add('hidden');
                }

                function openDeletePopup(id, paymentName) {
                    document.getElementById('delete_payment_name').textContent = paymentName;
                    const deleteFormAction = `{{ route('gym_staff.payment.delete', ':id') }}`.replace(':id', id);
                    document.getElementById('delete-form').action = deleteFormAction;
                    document.getElementById('delete-popup').classList.remove('hidden');
                }

                function closeDeletePopup() {
                    document.getElementById('delete-popup ').classList.add('hidden');
                }
                $(document).ready(function() {
                    $('#member_id').select2({
                        placeholder: "Select a member",
                        allowClear: true,
                        width: '100%' // Ensures it matches Tailwind's full width
                    });
                });
            </script>
        </div>
    </div>
</div>
@endsection