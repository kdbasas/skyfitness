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

        <!-- Equipment Inventory Section -->
        <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
            <h1 class="text-4xl font-bold mb-4 text-yellow-500">Equipment Inventory</h1>

            <!-- Add Equipment Form -->
            <form action="{{ route('admin.equipment.add') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="flex flex-col">
                    <label for="equipment_name" class="text-sm font-medium text-black">Equipment Name</label>
                    <input type="text" id="equipment_name" name="equipment_name" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
                </div>

                <div class="flex flex-col">
                    <label for="total_number" class="text-sm font-medium text-black">Total Number</label>
                    <input type="number" id="total_number" name="total_number" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
                </div>

                <div class="flex flex-col">
                    <label for="status" class="text-sm font-medium text-black">Status</label>
                    <select id="status" name="status" class="form-select mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="flex flex-col">
                    <label for="picture" class="text-sm font-medium text-black">Upload Picture</label>
                    <input type="file" class="form-control-file mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" id="equipment_picture" name="equipment_picture">
                    @error('equipment_picture')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Form Buttons -->
                <div class="flex space-x-4 mt-4">
                    <button type="submit" class="px-4 py-2 bg-[#1A1363] text-white rounded-lg shadow-md hover:bg-[#0f0c5c]">Add Equipment</button>
                    <button type="reset" class="px-4 py-2 bg-gray-400 text-white rounded-lg shadow-md hover:bg-gray-500">Cancel</button>
                </div>
            </form>
        </div>

        <!-- Equipment List Section -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-4 text-[#1A1363]">Equipment List</h2>

            <table class="w-full bg-white border border-gray-300 rounded-lg shadow-md">
                <thead>
                    <tr class="bg-[#1A1363] text-white">
                        <th class="px-4 py-2 text-left">Equipment Image</th>
                        <th class="px-4 py-2 text-left">Equipment Name</th>
                        <th class="px-4 py-2 text-left">Total Number</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($equipments as $equipment)
                        <tr>
                            <td class="px-4 py-2">
                                <img src="{{ asset('storage/img/equipment/' . $equipment->equipment_picture) }}" alt="{{ $equipment->equipment_name }}" class="w-20 h-20 object-cover rounded">
                            </td>
                            <td class="px-4 py-2">{{ $equipment->equipment_name }}</td>
                            <td class="px-4 py-2">{{ $equipment->total_number }}</td>
                            <td class="px-4 py-2">{{ ucfirst($equipment->status) }}</td>
                            <td class="px-4 py-2 flex items-center space-x-2">
                                <button 
                                    onclick="openEditPopup({{ $equipment->equipment_id }}, '{{ $equipment->equipment_name }}', '{{ $equipment->total_number }}', '{{ $equipment->status }}')" 
                                    class="px-4 py-2 bg-green-500 text-white rounded-lg shadow-md hover:bg-yellow-600"
                                >
                                    Edit    
                                </button>
                                <button 
                                    onclick="openDeletePopup({{ $equipment->equipment_id }}, '{{ $equipment->equipment_name }}')" 
                                    class="px-4 py-2 ml-2 bg-red-500 text-white rounded-lg shadow-md hover:bg-red-600"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-2 text-center">No equipment found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Edit Equipment Pop-Up -->
        <div id="edit-popup" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-75 hidden">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg w-full">
                <h2 class="text-2xl font-bold mb-4 text-[#1A1363]">Edit Equipment</h2>
                <form id="edit-form" action="{{ route('admin.inventory.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_equipment_id" name="equipment_id">
                    <div class="space-y-4">
                        <!-- Form Fields -->
                        <div class="flex flex-col">
                            <label for="edit_equipment_name" class="text-sm font-medium text-black">Equipment Name</label>
                            <input type="text" id="edit_equipment_name" name="equipment_name" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
                        </div>
                        <div class="flex flex-col">
                            <label for="edit_total_number" class="text-sm font-medium text-black">Total Number</label>
                            <input type="number" id="edit_total_number" name="total_number" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
                        </div>
                        <div class="flex flex-col">
                            <label for="edit_status" class="text-sm font-medium text-black">Status</label>
                            <select id="edit_status" name="status" class="form-select mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex space-x-4 mt-4">
                        <button type="submit" class="px-4 py-2 bg-[#1A1363] text-white rounded-lg shadow-md hover:bg-[#0f0c5c]">Update Equipment</button>
                        <button type="button" class="px-4 py-2 bg-gray-400 text-white rounded-lg shadow-md hover:bg-gray-500" onclick="closeEditPopup()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Equipment Pop-Up -->
        <div id="delete-popup" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-75 hidden">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg w-full">
                <h2 class="text-2xl font-bold mb-4 text-red-600">Delete Equipment</h2>
                <p class="mb-4">Are you sure you want to delete this equipment?</p>
                <form id="delete-form" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" id="delete_equipment_id" name="equipment_id">
                    <div class="flex space-x-4">
                        <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg shadow-md hover:bg-red-600">Yes, Delete</button>
                        <button type="button" class="px-4 py-2 bg-gray-400 text-white rounded-lg shadow-md hover:bg-gray-500" onclick="closeDeletePopup()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Scripts for Popup Handling -->
<script>
    function openEditPopup(id, name, number, status) {
        document.getElementById('edit_equipment_id').value = id;
        document.getElementById('edit_equipment_name').value = name;
        document.getElementById('edit_total_number').value = number;
        document.getElementById('edit_status').value = status;
        document.getElementById('edit-popup').classList.remove('hidden');
    }

    function closeEditPopup() {
        document.getElementById('edit-popup').classList.add('hidden');
    }

    function openDeletePopup(id, name) {
        document.getElementById('delete_equipment_id').value = id;
        document.getElementById('delete-form').action = `/admin/inventory/delete/${id}`;
        document.getElementById('delete-popup').classList.remove('hidden');
    }

    function closeDeletePopup() {
        document.getElementById('delete-popup').classList.add('hidden');
    }
</script>
@endsection
