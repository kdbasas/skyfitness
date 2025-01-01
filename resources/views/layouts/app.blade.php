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

        /* Notification Bell Styling */
        .notification-dropdown {
            position: absolute;
            top: 60px; /* Adjust based on the bell position */
            right: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 350px; /* Adjust based on your preference */
            max-height: 500px; /* Limit height */
            overflow-y: auto; /* Scroll if too many notifications */
            z-index: 1000;
            transition: opacity 0.3s ease, transform 0.3s ease;
            transform: translateY(-20px);
            opacity: 0;
            pointer-events: none;
        }
        .notification-dropdown.show {
            pointer-events: auto;
            transform: translateY(0);
            opacity: 1;
        }
        .notification-item {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .notification-item:last-child {
            border-bottom: none;
        }

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
        .notification-count {
            position: absolute;
            top: -5px;
            right: -10px;
            background: red;
            color: #fff;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
        }

        /* Alert Styles */
        .alert {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 10px;
            color: #fff;
        }
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body class="bg-[#ECE9E9]">
    @stack('styles')

    <!-- Header and Notification Bell -->
    <header>
        @unless(request()->routeIs('login') || request()->routeIs('attendance'))
            <div class="notification-bell">
                <i class="fas fa-bell"></i>
                <span class="notification-count">{{ count($notifications ?? []) }}</span>
                <!-- Dropdown container for notifications -->
                <div class="notification-dropdown">
                    <div class="notification-header">
                        Notifications
                    </div>
                    @foreach($notifications as $notification)
                        <div class="notification-item alert alert-{{ $notification['type'] }}" data-id="{{ $notification['id'] ?? '' }}">
                            {{ $notification['message'] }}
                        </div>
                    @endforeach
                </div>
            </div>
        @endunless
    </header>

    <!-- Main Content -->
    <div class="flex-1 p-6">
        @yield('content')
    </div>

    <!-- External JS Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>

    <!-- Notification Bell JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
    const notificationBell = document.querySelector('.notification-bell');
    const notificationDropdown = document.querySelector('.notification-dropdown');
    const notificationCountElem = document.querySelector('.notification-count');
    const notificationItems = document.querySelectorAll('.notification-item');

    if (notificationBell) {
        notificationBell.addEventListener('click', function (event) {
  if (event.target === notificationBell || event.target.tagName === 'I') {
    event.stopPropagation(); // Prevent the click event from propagating to the document
    notificationDropdown.classList.toggle('show');
    if (notificationDropdown.classList.contains('show')) {
      const lastNotification = notificationItems[notificationItems.length - 1];
      const dropdownHeight = notificationDropdown.offsetHeight;
      const lastNotificationOffset = lastNotification.offsetTop;
      notificationDropdown.scrollTop = lastNotificationOffset - dropdownHeight + 20; // Add 20px to account for padding
      markAllNotificationsAsRead();
    }
  }
});

        // Close the dropdown when clicking outside
        document.addEventListener('click', function (event) {
            if (
                !notificationBell.contains(event.target) &&
                !notificationDropdown.contains(event.target)
            ) {
                notificationDropdown.classList.remove('show');
            }
        });
    }

    // Add event listener to pagination links
    document.querySelectorAll('.pagination a').forEach(function (link) {
        link.addEventListener('click', function (event) {
            event.preventDefault(); // Prevent the default link behavior

            // Close the notification dropdown if it's open
            if (notificationDropdown.classList.contains('show')) {
                notificationDropdown.classList.remove('show');
            }

            // Do not mark notifications as read when clicking on pagination links
            // markAllNotificationsAsRead();

            const pageUrl = link.getAttribute('href'); // Get the URL for the page to load

            // Load new page content using AJAX
            fetch(pageUrl)
                .then(response => response.text())
                .then(data => {
                    // Update the main content with the new page data
                    document.querySelector('.flex-1').innerHTML = data;

                    // Fetch the unread notification count for the new page
                    fetchUnreadNotificationCount();
                });
        });
    });

    function markAllNotificationsAsRead() {
        fetch('/notifications/mark-all-as-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                notificationItems.forEach(item => {
                    item.classList.remove('alert-danger');
                    item.classList.add('alert-info');
                    item.style.opacity = '0.5';
                });

                // Clear the notification count
                updateNotificationCount(data.count);
            }
        });
    }

    function updateNotificationCount(count) {
        if (count === 0) {
            notificationCountElem.style.display = 'none';
        } else {
            notificationCountElem.textContent = count;
            notificationCountElem.style.display = 'flex';
        }
    }

    function fetchUnreadNotificationCount() {
        fetch('/notifications/unread-count')
            .then(response => response.json())
            .then(data => {
                updateNotificationCount(data.count);
            });
    }

    // Fetch the unread notification count on page load
    fetchUnreadNotificationCount();
});
    </script>
    
    @stack('scripts')
</body>
</html>
