@extends('layouts.app')

@section('content')
<div class="flex min-h-screen">
    <!-- Sidebar -->
    @include('include.sidebarstaff')

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
    <form action="{{ route('gym_staff.member.add') }}" method="POST" class="space-y-4" enctype="multipart/form-data">
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
            <button type="submit" class="bg-[#1A1363] text-white font-bold py-2 px-4 rounded-lg hover:bg-[#0f0c5c] transition duration-300 ease-in-out">Pay</button>
            <button type="reset" class="px-4 py-2 bg-gray-400 text-white rounded-lg shadow-md hover:bg-gray-500">Cancel</button>
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
    </div>
    
    <table class="w-full bg-white border border-gray-300 rounded-lg shadow-md text-sm">
        <thead>
            <tr class="bg-[#1A1363] text-white">
                <th class="px-6 py-3 text-left">First Name</th>
                <th class="px-6 py-3 text-left">Last Name</th>
                <th class="px-6 py-3 text-left">Subscription</th>
                <th class="px-6 py-3 text-left">Contact Number</th>
                <th class="px-6 py-3 text-left">Date Joined</th>
                <th class="px-6 py-3 text-left">Date Expired</th>
                <th class="px-6 py-3 text-center">QR Code</th>
                <th class="px-6 py-3 text-center">ID Attachment</th>
                <th class="px-6 py-3 text-left">Type</th>
                <th class="px-6 py-3 text-right">Total Price</th>
                <th class="px-6 py-3 text-center">Actions</th>
                <th class="px-6 py-3 text-center">Receipt</th>
            </tr>
        </thead>
        <tbody>
            @forelse($members as $member)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 border-b">{{ $member->first_name }}</td>
                    <td class="px-6 py-4 border-b">{{ $member->last_name }}</td>
                    <td class="px-6 py-4 border-b">{{ $member->subscription->subscription_name }}</td>
                    <td class="px-6 py-4 border-b">{{ $member->contact_number }}</td>
                    <td class="px-6 py-4 border-b">{{ \Carbon\Carbon::parse($member->date_joined)->format('M d, Y') }}</td>
                    <td class="px-6 py-4 border-b">{{ $member->date_expired ? \Carbon\Carbon::parse($member->date_expired)->format('M d, Y') : 'N/A' }}</td>
                    <td class="px-6 py-4 border-b text-center">
                        <img src="{{ asset('storage/img/qrcode/member_' . $member->member_id . '.png') }}" alt="QR Code" class="w-16 h-16 mx-auto mb-2">
                        <a href="{{ asset('storage/img/qrcode/member_' . $member->member_id . '.png') }}" download class="text-sm text-[#1A1363] hover:underline">Download</a>
                    </td>
                    <td class="px-6 py-4 border-b text-center">
                        <img src="{{ asset('storage/img/id_attachments/' . $member->id_attachment) }}" alt="ID Attachment" class="w-16 h-16 mx-auto">
                    </td>
                    <td class="px-6 py-4 border-b">{{ $member->promo == 'Student' ? 'Student' : 'Regular' }}</td>
                    <td class="px-6 py-4 border-b text-right">₱{{ number_format($member->amount, 2) }}</td>
                    <td class="px-6 py-4 border-b text-center">
                        <button 
                            onclick="openEditPopup({{ $member->member_id }}, '{{ $member->first_name }}', '{{ $member->last_name }}', '{{ $member->subscription_id }}', '{{ $member->contact_number }}', '{{ $member->date_joined->format('Y-m-d') }}', '{{ $member->date_expired ? $member->date_expired->format('Y-m-d') : '' }}')" 
                            class="px-3 py-2 bg-green-500 text-white rounded shadow-md hover:bg-yellow-600 text-xs"
                        >
                            Edit
                        </button>
                        <button 
                            onclick="openDeletePopup({{ $member->member_id }}, '{{ $member->first_name }} {{ $member->last_name }}')" 
                            class="px-3 py-2 bg-red-500 text-white rounded shadow-md hover:bg-red-600 text-xs ml-2"
                        >
                            Delete
                        </button>
                    </td>
                    <td class="px-6 py-4 border-b text-center">
                        <a href="{{ route('admin.print.receipt', $member->member_id) }}" class="text-[#1A1363] hover:underline text-sm">
                            <i class="fas fa-print"></i> Print
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="px-6 py-4 text-center border-b">No members found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-4">
        {{ $members->appends(['search' => request()->get('search')])->links('pagination::tailwind') }}
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
        document.getElementById('edit-form').action = `{{ route('gym_staff.member.update', ':id') }}`.replace(':id', id);

        document.getElementById('edit-popup').classList.remove('hidden');
    }

    function closeEditPopup() {
        document.getElementById('edit-popup').classList.add('hidden');
    }

    function openDeletePopup(id, fullName) {
        document.getElementById('delete_member_name').textContent = fullName;
        document.getElementById('delete-form').action = `{{ route('gym_staff.member.delete', ':id') }}`.replace(':id', id);
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