<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet" />
    @vite('resources/js/app.js')
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }

        /* Notification Bell Styling */
    .notification-bell {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
        background: #fff;
        border-radius: 50%;
        padding: 12px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        cursor: pointer;
        transition: background 0.3s, box-shadow 0.3s;
    }

    .notification-bell:hover {
        background: #f0f0f0;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
    }

    .notification-bell i {
        font-size: 28px;
        color: #333;
    }
    </style>
</head>
<body class="bg-[#ECE9E9]">
    @stack('styles')

    <!-- Notification Bell -->
    @unless(request()->routeIs('login'))
    <div class="notification-bell">
        <i class="fas fa-bell"></i>
    </div>
    @endunless

    <!-- Main Content -->
    <div class="flex-1 p-6">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    @stack('scripts')
</body>
</html>
