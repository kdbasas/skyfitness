<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        h1 { text-align: center; color: #1A1363; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #1A1363; color: white; }
    </style>
</head>
<body>
    <h1>Attendance Report - {{ $selectedDate }}</h1>

    <table>
        <thead>
            <tr>
                <th>Member Name</th>
                <th>Date</th>
                <th>Check-in Time</th>
                <th>Check-out Time</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendanceRecords as $record)
                <tr>
                    <td>{{ $record['member_name'] }}</td>
                    <td>{{ $record['date'] }}</td>
                    <td>{{ $record['check_in_time'] ?? 'N/A' }}</td>
                    <td>{{ $record['check_out_time'] ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No attendance records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
