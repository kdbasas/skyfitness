<!-- Payment History Section -->
<div class="bg-white p-6 rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold mb-4 text-[#1A1363]">Payment History</h2>

    <div class="flex justify-between mb-4">
        <div class="flex space-x-4">
            <form method="GET" action="{{ route('gym_staff.payment.form') }}">
                <label for="date">Select Date:</label>
                <input type="date" id="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()">
            </form>
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
                    <td colspan="6" class="text-center py-3 px-4 border text-gray-500">No payment history available.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
</div>

<!-- Edit Payment Pop-Up -->
<div id="edit-popup" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-75 hidden">
<div class="bg-white p-6 rounded-lg shadow-lg max-w-lg w-full">
    <h2 class="text-2xl font-bold mb-4 text-[#1A1363]">Edit Payment</h2>
    <form id="edit-form" action="#" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" id="edit_payment_id" name="payment_id">
        <div class="space-y-4">
            <!-- Form Fields -->
            <div class="flex flex-col">
                <label for="edit_member_name" class="text-sm font-medium text-black">Member Name</label>
                <input type="text" id="edit_member_name" name="member_name" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
            </div>

            <div class="flex flex-col">
                <label for="edit_subscription_id" class="text-sm font-medium text-black">Subscription ID</label>
                <select id="edit_subscription_id" name="subscription_id" class="form-select mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
                    <option value="">Select a subscription plan</option>
                    @foreach($subscriptions as $subscription)
                        <option value="{{ $subscription->subscription_id }}" data-amount="{{ $subscription->amount }}">{{ $subscription->subscription_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col">
                <label for="edit_promo" class="text-sm font-medium text-black">Promo</label>
                <select id="edit_promo" name="promo" class="form-select mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
                    <option value="">Select a promo</option>
                    <option value="Student">Student</option>
                    <option value="Regular">Regular</option>
                </select>
            </div>

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


<!-- Delete Payment Pop-Up -->
<div id="delete-popup" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-75 hidden">
<div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
    <h2 class="text-2xl font-bold mb-4 text-[#1A1363]">Delete Payment</h2>
    <p class="text-lg mb-4">Are you sure you want to delete <span id="delete_payment_name"></span>?</p>
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
function openEditPopup(id, memberName, subscriptionPlan, promo, amount, datePaid) {
// Set the form fields with current payment values
document.getElementById('edit_payment_id').value = id;
document.getElementById('edit_member_name').value = memberName;
document.getElementById('edit_subscription_id').value = subscriptionPlan;
document.getElementById('edit_promo').value = promo;
document.getElementById('edit_date_paid').value = datePaid;

// Calculate the final amount based on promo
const subscriptionSelect = document.getElementById('edit_subscription_id');
const selectedOption = subscriptionSelect.options[subscriptionSelect.selectedIndex];
const subscriptionAmount = parseFloat(selectedOption.getAttribute('data-amount')); // Ensure it's a number
let finalAmount = subscriptionAmount; // Ensure it's a number

// Adjust amount based on promo
if (promo === 'Student') {
    finalAmount -= 50; // Discount for Student promo
}

// Ensure finalAmount is a valid number and not NaN
finalAmount = isNaN(finalAmount) ? 0 : finalAmount;

// Set the hidden amount field with the final amount
let amountInput = document.querySelector('#edit-form input[name="amount"]');
if (!amountInput) {
    amountInput = document.createElement('input');
    amountInput.type = 'hidden';
    amountInput.name = 'amount';
    document.getElementById('edit-form').appendChild(amountInput);
}
amountInput.value = finalAmount.toFixed(2); // Ensure it's a number with 2 decimal places

// Dynamically set the form action with the correct URL for the update
const actionUrl = "/gym_staff/payment/update/" + id; // Correct URL format for update
document.getElementById('edit-form').action = actionUrl;

// Show the edit popup
document.getElementById('edit-popup').classList.remove('hidden');
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

document.getElementById('delete-form').addEventListener('submit', function (e) {
e.preventDefault(); // Prevent default form submission
this.submit(); // Submit the form manually
});
function closeDeletePopup() {
document.getElementById('delete-popup').classList.add('hidden');
}
</script>
@endsection