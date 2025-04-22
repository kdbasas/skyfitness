@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gray-50">
    <!-- Sidebar -->
    @include('include.sidebar')

    <!-- Main Content -->
    <div class="flex-1 ml-64 p-8">
        <!-- Header Section -->
        <div class="flex items-center justify-center mb-6 p-4 bg-transparent text-[#1A1363]">
            <img src="{{ asset('img/logosky 2.png') }}" alt="Gym Logo" class="w-40 h-auto mr-4">
            <h1 class="text-4xl font-bold text-[#1A1363]">ROXAS SKY FITNESS GYM</h1>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="bg-green-500 text-white p-4 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-500 text-white p-4 rounded-lg mb-6">
                <ul class="list-disc pl-6">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Feedback Section -->
        <div class="bg-white p-8 rounded-lg shadow-lg mb-8">
            <h1 class="text-3xl font-semibold text-[#1A1363] mb-8">Feedback</h1>

            <!-- Search and Sort -->
            <div class="flex flex-wrap justify-between items-center mb-6">
                <!-- Search Bar -->
                <form method="GET" action="{{ route('admin.feedback') }}" class="flex items-center space-x-2 mb-4 sm:mb-0">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Feedback ID"
                        class="border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#1A1363]">
                    
                    <button type="submit"
                        class="bg-[#1A1363] text-white px-4 py-2 rounded hover:bg-indigo-900 transition duration-300">
                        Search
                    </button>
                </form>

                <!-- Sort Dropdown -->
                <form method="GET" action="{{ route('admin.feedback') }}" class="flex items-center space-x-2">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <label for="sort" class="text-sm text-gray-700">Sort by Date:</label>
                    <select name="sort" onchange="this.form.submit()"
                        class="border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1A1363]">
                        <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Newest First</option>
                        <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Oldest First</option>
                    </select>
                </form>
            </div>

            <!-- Feedback Table -->
            <div class="overflow-x-auto bg-white rounded-lg shadow-sm">
                <table class="w-full text-xl text-left text-gray-700 border-collapse border border-gray-300">
                    <thead class="bg-[#1A1363] text-white text-sm uppercase">
                        <tr>
                            <th class="py-4 px-6 border">Feedback ID</th>
                            <th class="py-4 px-6 border">Message</th>
                            <th class="py-4 px-6 border">Date Submitted</th>
                            <th class="py-4 px-6 border text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($feedbacks as $index => $feedback)
                            <tr class="text-sm text-gray-800 hover:bg-gray-50">
                                <td class="py-4 px-6 border bg-gray-100">{{ $feedback->id }}</td>
                                <td class="py-4 px-6 border bg-gray-50">{{ Str::limit($feedback->message, 50) }}</td>
                                <td class="py-4 px-6 border bg-gray-100">{{ $feedback->created_at->format('F d, Y h:i A') }}</td>
                                <td class="py-4 px-6 border text-center bg-gray-50">
                                    <form action="{{ route('admin.feedback.delete', $feedback->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this feedback?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-8 py-4 bg-red-500 text-white rounded-lg hover:bg-red-600 transition duration-300">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 px-6 text-center text-gray-500">
                                    No feedback found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6 flex justify-center">
                {{ $feedbacks->links() }}
            </div>
        </div>
    </div>
</div>
@endsection