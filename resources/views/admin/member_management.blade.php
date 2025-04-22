@extends('layouts.app')

@section('content')
@php
    use Carbon\Carbon;
@endphp
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
    <h1 class="text-4xl font-bold mb-4 text-yellow-500 text-center">Become a Member</h1>
    <h2 class="text-2xl font-bold mb-4 text-[#1A1363] text-center">Registration</h2>

    <!-- Registration Form -->
    <form id="registrationForm" action="{{ route('admin.member.add') }}" method="POST" class="space-y-4" enctype="multipart/form-data">
        @csrf
        <!-- Form Fields -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                <label for="gender_id" class="text-sm font-medium text-black">Gender</label>
                <select id="gender_id" name="gender_id" class="form-select mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363] p-2" required>
                    <option value="" disabled selected>Select Gender</option>
                    @foreach($genders as $gender)
                        <option value="{{ $gender->gender_id }}">{{ $gender->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col">
                <label for="email" class="text-sm font-medium text-black">Email Address</label>
                <input type="email" id="email" name="email" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363] p-2" required>
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
                <label for="promo" class="text-sm font-medium text-black">Promo</label>
                <select id="promo" name="promo" class="form-select mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363] p-2" required>
                    <option value="" disabled selected>Select Promo</option>
                    <option value="Student">Student (₱450)</option>
                    <option value="Regular">Regular (₱500)</option>
                </select>
            </div>
            <div class="flex flex-col">
                <label for="id_attachment" class="text-sm font-medium text-black">ID Attachment</label>
                <input type="file" id="id_attachment" name="id_attachment" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363] p-2" required>
                <small class="text-gray-500 mt-1">Upload a clear image of your ID (jpg, jpeg, png).</small>
            </div>
            <div class="flex flex-col">
                <label for="subscription_id" class="text-sm font-medium text-black">Subscription</label>
                <select id="subscription_id" name="subscription_id" class="form-select mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363] p-2" required>
                    @foreach($subscriptions as $subscription)
                        <option value="{{ $subscription->subscription_id }}">{{ $subscription->subscription_name }} ({{ $subscription->validity }} months)</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col">
                <label for="date_joined" class="text-sm font-medium text-black">Date Joined</label>
                <input type="date" id="date_joined" name="date_joined" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363] p-2" required>
            </div>
        </div>
        
        <div class="flex justify-between mt-6">
            <button type="submit" class="bg-[#1A1363] text-white font-bold py-2 px-4 rounded-lg hover:bg-[#0f0c5c] transition duration-300 ease-in-out">Register</button>
            <button type="button" class="px-4 py-2 bg-gray-400 text-white rounded-lg shadow-md hover:bg-gray-500" onclick="clearForm()">Clear</button>
        </div>
    </form>
</div>
<!-- Member List Section -->
<div class="bg-white p-6 rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold mb-6 text-[#1A1363]">Member's List</h2>
    <div class="flex justify-between mb-4">
        <form action="{{ route('admin.member_management') }}" method="GET" class="w-full md:w-1/3 flex">
            <input type="text" name="search" placeholder="Search Members..." value="{{ request()->get('search') }}" class="form-input w-full px-4 py-2 rounded-l-lg border border-gray-300 focus:outline-none focus:ring-[#1A1363] focus:border-[#1A1363]">
            <button type="submit" class="px-4 py-2 bg-[#1A1363] text-white rounded-r-lg hover:bg-[#160f70] focus:outline-none">
                Search
            </button>
        </form>
           <!-- Print Report Section -->
<div class="flex justify-between mb-4">
    <form action="{{ route('admin.member_report') }}" method="GET" class="flex items-center">
        <select name="report_type" class="form-select rounded-lg border border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363] p-2">
            <option value="" disabled selected>Select Report Type</option>
            <option value="new_members">New Members</option>
            <option value="renewals">Renewals</option>
            <option value="active">Active Members</option>
            <option value="inactive">Inactive Members</option>
            <option value="expired">Expired Members</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-[#1A1363] text-white rounded-lg hover:bg-[#0f0c5c] ml-2">
            Print Report
        </button>
    </form>
</div>
        <!-- Sorting Dropdown -->
        <form action="{{ route('admin.member_management') }}" method="GET" class="flex items-center ml-4">
            <select name="sort" class="form-select rounded-lg border border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363] p-2">
                <option value="" disabled selected>Sort By</option>
                <option value="latest">Latest Member</option>
                <option value="oldest">Oldest Member</option>
                <option value="student">Student Promo</option>
                <option value="regular">Regular Promo</option>
                <option value="gender_male">Gender: Male</option>
                <option value="gender_female">Gender: Female</option>
                <option value="active">Active Members</option>
                <option value="inactive">Inactive Members</option>
                <option value="expired">Expired Members</option>
            </select>
            <button type="submit" class="ml-2 px-4 py-2 bg-[#1A1363] text-white rounded-lg hover:bg-[#160f70] focus:outline-none">Sort</button>
        </form>
    </div>
    
    <table class="w-full bg-white border border-gray-300 rounded-lg shadow-md text-sm">
        <thead>
            <tr class="bg-[#1A1363] text-white">
                <th class="px-6 py-3 text-left whitespace-nowrap w-1/6">First Name</th>
                <th class="px-6 py-3 text-left whitespace-nowrap w-1/6">Last Name</th>
                <th class="px-6 py-3 text-center whitespace-nowrap w-1/6">QR Code</th>
                <th class="px-6 py-3 text-center whitespace-nowrap w-1/6">ID Attachment</th>
                <th class="px-6 py-3 text-left whitespace-nowrap w-1/6">Date Expired</th>
                <th class="px-6 py-3 text-left whitespace-nowrap w-1/6">Status</th>
                <th class="px-6 py-3 text-center whitespace-nowrap w-1/6">Actions</th>
                <th class="px-6 py-3 text-center whitespace-nowrap w-1/6">Receipt</th>
            </tr>
        </thead>
        <tbody>
            @forelse($members as $member)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 border-b text-left">{{ $member->first_name }}</td>
                    <td class="px-6 py-4 border-b text-left">{{ $member->last_name }}</td>
                    <td class="px-6 py-4 border-b text-center">
                        <img src="{{ asset('storage/img/qrcode/member_' . $member->member_id . '.png') }}" alt="QR Code" class="w-16 h-16 mx-auto mb-2">
                        <a href="{{ asset('storage/img/qrcode/member_' . $member->member_id . '.png') }}" download class="text-sm text-[#1A1363] hover:underline">Download</a>
                    </td>
                    <td class="px-6 py-4 border-b text-center">
                        <img src="{{ asset('storage/img/id_attachments/' . $member->id_attachment) }}" alt="ID Attachment" class="w-16 h-16 mx-auto">
                        <br>
                        <a href="{{ asset('storage/img/id_attachments/' . $member->id_attachment) }}" download class="text-blue-500 hover:underline text-sm">
                            Download ID
                        </a>
                    </td>
                    <td class="px-6 py-4 border-b text-left">{{ $member->date_expired ? $member->date_expired->format('Y-m-d') : 'N/A' }}</td>
                    <td class="px-6 py-4 border-b text-left">
                        @if($member->date_expired && Carbon::parse($member->date_expired)->isFuture())
                            <span class="text-green-500 font-bold">Active</span>
                        @elseif($member->date_expired && Carbon::parse($member->date_expired)->isToday())
                            <span class="text-red-500 font-bold">Expired</span>
                        @elseif($member->date_expired && Carbon::parse($member->date_expired)->isPast())
                            @if(Carbon::now()->diffInDays($member->date_expired) > 30)
                                <span class="text-orange-500 font-bold">Inactive</span>
                            @else
                                <span class="text-red-500 font-bold">Expired</span>
                            @endif
                        @else
                            <span class="text-gray-500 font-bold">Unknown</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 border-b text-center">
                        <button 
                            onclick="openViewPopup({{ $member->member_id }}, '{{ $member->first_name }}', '{{ $member->middle_name }}', '{{ $member->last_name }}', '{{ $member->email }}', '{{ $member->contact_number }}', {{ $member->age }}, '{{ $member->date_joined->format('Y-m-d') }}', '{{ $member->promo }}', '{{ $member->subscription_id }}', '{{ $member->id_attachment }}', '{{ $member->suffix_name }}', '{{ $member->gender_id }}')" 
                            class="px-2 py-1 bg-blue-500 text-white rounded shadow-md hover:bg-blue-600 text-xs"
                        >
                            View
                        </button>
                        <button 
                            onclick="openEditPopup({{ $member->member_id }}, '{{ $member->first_name }}', '{{ $member->last_name }}', '{{ $member->subscription_id }}', '{{ $member->contact_number }}', '{{ $member->date_joined->format('Y-m-d') }}', '{{ $member->date_expired ? $member->date_expired->format('Y-m-d') : '' }}')" 
                            class="px-2 py-1 bg-green-500 text-white rounded shadow-md hover:bg-yellow-600 text-xs"
                        >
                            Edit
                        </button>
                        <button 
                            onclick="openDeletePopup({{ $member->member_id }}, '{{ $member->first_name }} {{ $member->last_name }}')" 
                            class="px-2 py-1 bg-red-500 text-white rounded shadow-md hover:bg-red-600 text-xs ml-2"
                        >
                            Delete
                        </button>
                    </td>
                    <td class="px-6 py-4 border-b text-center">
                        <button onclick="printReceipt({{ $member->member_id }})" class="bg-blue-500 text-white px-3 py-1 rounded-lg hover:bg-blue-600 text-xs">
                            Print Receipt
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center border-b">No members found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-4">
        {{ $members->appends(['search' => request()->get('search'), 'sort' => request()->get('sort')])->links('pagination::tailwind') }}
    </div>
</div>    
       <!-- Edit Member Pop-Up -->
<div id="edit-popup" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-75 hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg w-full">
        <h2 class="text-2xl font-bold mb-4 text-[#1A1363]">Edit Member</h2>
        <form id="edit-form" action="#" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_member_id" name="id">
            <div class="space-y-4">
                <!-- Form Fields -->
                <div class="flex flex-col">
                    <label for="edit_first_name" class="text-sm font-medium text-black">First Name</label>
                    <input type="text" id="edit_first_name" name="first_name" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
                </div>

                <div class="flex flex-col">
                    <label for="edit_last_name" class="text-sm font-medium text-black">Last Name</label>
                    <input type="text" id="edit_last_name" name="last_name" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
                </div>

                <div class="flex flex-col">
                    <label for="edit_subscription" class="text-sm font-medium text-black">Subscription</label>
                    <select id="edit_subscription" name="subscription_id" class="form-select mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
                        @foreach($subscriptions as $subscription)
                            <option value="{{ $subscription->subscription_id }}">{{ $subscription->subscription_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col">
                    <label for="edit_contact_number" class="text-sm font-medium text-black">Contact Number</label>
                    <input type="text" id="edit_contact_number" name="contact_number" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
                </div>

                <div class="flex flex-col">
                    <label for="edit_date_joined" class="text-sm font-medium text-black">Date Joined</label>
                    <input type="date" id="edit_date_joined" name="date_joined" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
                </div>
            </div>
            <div class="flex space-x-4 mt-4">
                <button type="submit" class="px-4 py-2 bg-[#1A1363] text-white rounded-lg shadow-md hover:bg-[#0f0c5c]">Update</button>
                <button type="button" onclick="closeEditPopup()" class="px-4 py-2 bg-gray-400 text-white rounded-lg shadow-md hover:bg-gray-500">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Member Pop-Up -->
<div id="delete-popup" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-75 hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
        <h2 class="text-2xl font-bold mb-4 text-[#1A1363]">Delete Member</h2>
        <p class="text-lg mb-4">Are you sure you want to delete <span id="delete_member_name"></span>?</p>
        <form id="delete-form" action="#" method="POST">
            @csrf
            @method('DELETE')
            <div class="flex space-x-4 mt-4">
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg shadow-md hover:bg-red-700">Delete</button>
                <button type="button" onclick="closeDeletePopup()" class="px-4 py-2 bg-gray-400 text-white rounded-lg shadow-md hover:bg-gray-500">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
        function printReceipt(memberId) {
    // Open the receipt in a new window
    var printWindow = window.open(`/print-receipt/${memberId}`, '_blank');

    // Wait for the new window to load
    printWindow.onload = function() {
        printWindow.print(); // Trigger the print dialog
    };
}
function openViewPopup(id, firstName, middleName, lastName, email, contactNumber, age, dateJoined, promo, subscriptionId, idAttachment, suffixName, genderId) {
    // Populate the form fields with the member's details
    document.getElementById('first_name').value = firstName;
    document.getElementById('middle_name').value = middleName;
    document.getElementById('last_name').value = lastName;
    document.getElementById('email').value = email;
    document.getElementById('contact_number').value = contactNumber;
    document.getElementById('age').value = age;
    document.getElementById('promo').value = promo;
    document.getElementById('subscription_id').value = subscriptionId;
    document.getElementById('date_joined').value = dateJoined;
    document.getElementById('suffix_name').value = suffixName; // Populate suffix name
    document.getElementById('gender_id').value = genderId; // Populate gender

    // Make fields read-only
    document.getElementById('first_name').readOnly = true;
    document.getElementById('middle_name').readOnly = true;
    document.getElementById('last_name').readOnly = true;
    document.getElementById('email').readOnly = true;
    document.getElementById('contact_number').readOnly = true;
    document.getElementById('age').readOnly = true;
    document.getElementById('promo').readOnly = true; // Disable the dropdown
    document.getElementById('subscription_id').readOnly = true; // Disable the dropdown
    document.getElementById('id_attachment').readOnly = true; 
    document.getElementById('date_joined').readOnly = true;

    // Show the registration form
    document.getElementById('registrationForm').classList.remove('hidden'); // Show the registration form
}
function clearForm() {
    document.getElementById('first_name').value = '';
    document.getElementById('middle_name').value = '';
    document.getElementById('last_name').value = '';
    document.getElementById('gender_id').value = '';
    document.getElementById('suffix_name').value = '';
    document.getElementById('email').value = '';
    document.getElementById('contact_number').value = '';
    document.getElementById('age').value = '';
    document.getElementById('promo').value = '';
    document.getElementById('id_attachment').value = '';
    document.getElementById('subscription_id').value = '';
    document.getElementById('date_joined').value = '';

    // Remove read-only attribute from form fields
    document.getElementById('first_name').readOnly = false;
    document.getElementById('middle_name').readOnly = false;
    document.getElementById('last_name').readOnly = false;
    document.getElementById('gender_id').readOnly = false;
    document.getElementById('suffix_name').readOnly = false;
    document.getElementById('email').readOnly = false;
    document.getElementById('contact_number').readOnly = false;
    document.getElementById('age').readOnly = false;
    document.getElementById('promo').disabled = false;
    document.getElementById('id_attachment').disabled = false;
    document.getElementById('subscription_id').disabled = false;
    document.getElementById('date_joined').readOnly = false;
}

    function openEditPopup(id, firstName, lastName, subscriptionId, contactNumber, dateJoined) {
        document.getElementById('edit_member_id').value = id;
        document.getElementById('edit_first_name').value = firstName;
        document.getElementById('edit_last_name').value = lastName;
        document.getElementById('edit_subscription').value = subscriptionId;
        document.getElementById('edit_contact_number').value = contactNumber;
        document.getElementById('edit_date_joined').value = dateJoined;

        let formattedDateJoined = new Date(dateJoined).toISOString().split('T')[0];
        document.getElementById('edit_date_joined').value = formattedDateJoined;

        // Set the form action dynamically based on member ID
        document.getElementById('edit-form').action = `{{ route('admin.member.update', ':id') }}`.replace(':id', id);

        document.getElementById('edit-popup').classList.remove('hidden');
    }

    function closeEditPopup() {
        document.getElementById('edit-popup').classList.add('hidden');
    }

    function openDeletePopup(id, fullName) {
        document.getElementById('delete_member_name').textContent = fullName;
        document.getElementById('delete-form').action = `{{ route('admin.member.delete', ':id') }}`.replace(':id', id);
        document.getElementById('delete-popup').classList.remove('hidden');
    }

    function closeDeletePopup() {
        document.getElementById('delete-popup').classList.add('hidden');
    }
    document.getElementById('subscription_id').addEventListener('change', function() {
    var subscriptionId = this.value;
    var promo = document.getElementById('promo').value;

    $.ajax({
        type: 'GET',
        url: '/calculate-amount',
        data: {subscription_id: subscriptionId, promo: promo},
        success: function(response) {
            document.getElementById('amount').value = '₱' + response.amount;
        },
        error: function(xhr, status, error) {
            console.log(xhr.responseText);
        }
    });
});

document.getElementById('promo').addEventListener('change', function() {
    var promo = this.value;
    var subscriptionId = document.getElementById('subscription_id').value;

    $.ajax({
        type: 'GET',
        url: '/calculate-amount',
        data: {subscription_id: subscriptionId, promo: promo},
        success: function(response) {
            document.getElementById('amount').value = '₱' + response.amount;
        },
        error: function(xhr, status, error) {
            console.log(xhr.responseText);
        }
    });
});
</script>
</div>
</div>
@endsection