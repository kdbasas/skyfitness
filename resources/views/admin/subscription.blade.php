@extends('layouts.app')

@section('content')
<div class="flex min-h-screen">
    <!-- Sidebar -->
    @include('include.sidebar')

    <!-- Main Content -->
    <div class="flex-1 ml-64 p-6 bg-[#ECE9E9]">
        <!-- Header Section -->
        <div class="flex items-center justify-center mb-6 p-4 bg-transparent text-[#1A1363]"style="margin-top: -20px;">
            <img src="{{ asset('img/logosky 2.png') }}" alt="Gym Logo" class="w-40 h-30 mr-4">
            <h1 class="text-4xl font-bold">ROXAS SKY FITNESS GYM</h1>
        </div>

        <!-- Subscription Form -->
        <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
            <h2 class="text-2xl font-bold mb-4 text-[#1A1363]">Add Subscription</h2>
            <form action="{{ route('admin.subscription.add') }}" method="POST" class="space-y-4">
                @csrf
                <!-- Subscription Name -->
                <div class="flex flex-col">
                    <label for="subscription_name" class="text-sm font-medium text-black">Subscription Name</label>
                    <input type="text" id="subscription_name" name="subscription_name" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
                </div>
                
                <!-- Validity -->
                <div class="flex flex-col">
                    <label for="validity" class="text-sm font-medium text-black">Validity (months)</label>
                    <input type="number" id="validity" name="validity" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
                </div>
                
                <!-- Amount -->
                <div class="flex flex-col">
                    <label for="amount" class="text-sm font-medium text-black">Amount</label>
                    <input type="number" id="amount" name="amount" step="0.01" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
                </div>

                <div class="flex space-x-4 mt-4">
                    <button type="submit" class="px-4 py-2 bg-[#1A1363] text-white rounded-lg shadow-md hover:bg-[#1A1363]">Save</button>
                    <button type="reset" class="px-4 py-2 bg-gray-400 text-white rounded-lg shadow-md hover:bg-gray-600">Cancel</button>
                </div>
            </form>
        </div>

        <!-- Subscription List -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-4 text-[#1A1363]">Subscription List</h2>
            <div class="flex items-center mb-4">
                <input type="text" id="search" placeholder="Search Subscription Name" class="form-input w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]">
                <button id="searchButton" class="ml-4 px-4 py-2 bg-[#1A1363] text-white rounded-lg shadow-md hover:bg-[#1A1363]">Search</button>
            </div>

            <table class="w-full bg-white border border-gray-300 rounded-lg shadow-md">
                <thead>
                    <tr class="bg-[#1A1363] text-white">
                        <th class="px-4 py-2 text-left">Subscription Name</th>
                        <th class="px-4 py-2 text-left">Validity (months)</th>
                        <th class="px-4 py-2 text-left">Amount</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscriptions as $subscription)
                        <tr>
                            <td class="px-4 py-2">{{ $subscription->subscription_name }}</td>
                            <td class="px-4 py-2">{{ $subscription->validity }}</td>
                            <td class="px-4 py-2">₱{{ number_format($subscription->amount, 2) }}</td>
                            <td class="px-4 py-2">
                                <button 
                                    onclick="openEditPopup({{ $subscription->subscription_id }}, '{{ $subscription->subscription_name }}', {{ $subscription->validity }}, {{ $subscription->amount }})" 
                                    class="px-4 py-2 bg-green-500 text-white rounded-lg shadow-md hover:bg-yellow-600"
                                >
                                    Edit
                                </button>
                                <button 
                                        onclick="openDeletePopup({{ $subscription->subscription_id }}, '{{ $subscription->subscription_name }}')" 
                                        class="px-4 py-2 ml-2 bg-red-500 text-white rounded-lg shadow-md hover:bg-red-600"
                                    >
                                        Delete
                                    </button>
                                </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-2 text-center">No subscriptions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Subscription Popup -->
<div id="editSubscriptionPopup" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
        <h2 class="text-2xl font-bold mb-4 text-[#1A1363]">Edit Subscription</h2>
        <form id="editSubscriptionForm" action="{{ route('admin.subscription.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_subscription_id" name="subscription_id">

            <div class="flex flex-col">
                <label for="edit_subscription_name" class="text-sm font-medium text-black">Subscription Name</label>
                <input type="text" id="edit_subscription_name" name="subscription_name" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
            </div>
            
            <div class="flex flex-col">
                <label for="edit_validity" class="text-sm font-medium text-black">Validity (months)</label>
                <input type="number" id="edit_validity" name="validity" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
            </div>
            
            <div class="flex flex-col">
                <label for="edit_amount" class="text-sm font-medium text-black">Amount</label>
                <input type="number" id="edit_amount" name="amount" step="0.01" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
            </div>

            <div class="flex space-x-4 mt-4">
                <button type="submit" class="px-4 py-2 bg-[#1A1363] text-white rounded-lg shadow-md hover:bg-[#1A1363]">Update</button>
                <button type="button" onclick="closeEditPopup()" class="px-4 py-2 bg-gray-400 text-white rounded-lg shadow-md hover:bg-gray-600">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Subscription Popup -->
<div id="deleteSubscriptionPopup" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
        <h2 class="text-2xl font-bold mb-4 text-[#1A1363]">Delete Subscription</h2>
        <p>Are you sure you want to delete the subscription <strong id="delete_subscription_name"></strong>?</p>
        <form id="deleteSubscriptionForm" action="{{ route('admin.subscription.delete') }}" method="POST" class="space-y-4">
            @csrf
            @method('DELETE')
            <input type="hidden" id="delete_subscription_id" name="subscription_id">

            <div class="flex space-x-4 mt-4">
                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg shadow-md hover:bg-red-600">Delete</button>
                <button type="button" onclick="closeDeletePopup()" class="px-4 py-2 bg-gray-400 text-white rounded-lg shadow-md hover:bg-gray-600">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript for handling the popup -->
<script>
    function openEditPopup(id, name, validity, amount) {
        // Fill the form with the current subscription data
        document.getElementById('edit_subscription_id').value = id;
        document.getElementById('edit_subscription_name').value = name;
        document.getElementById('edit_validity').value = validity;
        document.getElementById('edit_amount').value = amount;
        
        // Display the popup
        document.getElementById('editSubscriptionPopup').classList.remove('hidden');
    }

    function closeEditPopup() {
        // Hide the popup
        document.getElementById('editSubscriptionPopup').classList.add('hidden');
    }

    function openDeletePopup(id, name) {
        console.log("Open Delete Popup triggered for:", id, name);

        // Set the subscription name in the confirmation message
        document.getElementById('delete_subscription_name').textContent = name;

        // Set the subscription ID in the hidden input field
        document.getElementById('delete_subscription_id').value = id;

        // Show the delete confirmation popup
        document.getElementById('deleteSubscriptionPopup').classList.remove('hidden');
    }

    function closeDeletePopup() {
        console.log("Close Delete Popup triggered");

        // Hide the delete confirmation popup
        document.getElementById('deleteSubscriptionPopup').classList.add('hidden');
    }
</script>
@endsection
