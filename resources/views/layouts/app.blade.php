<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- CSS and Font Awesome -->
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet">
    
    <!-- JavaScript -->
    @vite('resources/js/app.js')
    
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
    </style>
</head>
<body class="bg-[#ECE9E9]">
    @stack('styles')
    <!-- Main Content -->
    <div class="flex-1 p-6">
        @yield('content')
    </div>

    <!-- External JS Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    @stack('scripts')
</body>
</html>