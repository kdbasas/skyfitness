@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-page-color font-roboto">
    <!-- Content Container -->
    <div class="w-full lg:w-1/2 flex items-center justify-center lg:pr-16">
        <div class="w-full max-w-md relative">
            <h2 class="text-2xl font-bold mb-6 text-center text-dark-blue">Login</h2>

            <!-- Display validation errors -->
            @if ($errors->any())
                <div class="mb-4 bg-red-200 text-red-700 p-3 rounded">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login.submit') }}" autocomplete="off">
                @csrf
                <div class="mb-4">
                    <label for="username" class="block text-dark-blue text-sm font-bold opacity-80">Username</label>
                    <input type="text" id="email" name="email" class="w-full h-12 border-2 border-dark-blue rounded-md shadow-sm text-dark-blue bg-light-gray placeholder-dark-blue @error('email') border-red-500 @enderror" placeholder="Email" required autocomplete="new-email">
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-dark-blue text-sm font-bold opacity-80">Password</label>
                    <input type="password" id="password" name="password" class="w-full h-12 border-2 border-dark-blue rounded-md shadow-sm text-dark-blue bg-light-gray placeholder-dark-blue @error('password') border-red-500 @enderror" placeholder="Password" required autocomplete="new-password">
                </div>
                <button type="submit" class="w-full h-16 rounded-full bg-dark-blue text-white text-lg font-bold">Login</button>
            </form>
        </div>
    </div>

    <!-- Logo Section -->
    <div class="hidden lg:flex lg:w-1/2 lg:justify-center lg:items-center lg:pl-16">
        <img src="{{ asset('img/logosky 2.png') }}" alt="Gym Logo" style="width: 811.27px; height: 800px;" class="object-contain">
    </div>
</div>
@endsection

@push('styles')
<style>
    html, body {
        height: 100%;
        margin: 0;
        background-color: #C9CCCD; /* Ensure the background color fills the entire viewport */
    }
    .bg-page-color {
        background-color: transparent; /* Override the background color here */
    }
    .text-dark-blue {
        color: #0C0B25;
    }
    .bg-light-gray {
        background-color: #f5f5f5;
    }
    .bg-dark-blue {
        background-color: #0C0B25;
    }
    .border-dark-blue {
        border-color: #0C0B25;
    }
    .h-12 {
        height: 49px; /* Adjust as needed */
    }
    .h-16 {
        height: 64px; /* Adjust as needed */
    }
    /* Additional styles for oblong button */
    .rounded-full {
        border-radius: 9999px; /* Makes the button fully rounded (oblong) */
    }
    .placeholder-dark-blue::placeholder {
        color: #0C0B25;
        opacity: 0; /* Hide placeholder text */
    }
</style>
@endpush

@push('scripts')
<script>
    // Temporarily remove this function for debugging
    // function clearFields() {
    //     document.getElementById('username').value = '';
    //     document.getElementById('password').value = '';
    // }

    // window.onload = function() {
    //     clearFields(); // Ensure fields are clear on page load
    // };
</script>
@endpush
