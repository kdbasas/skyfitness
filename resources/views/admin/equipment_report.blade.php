<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Inventory Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f2f2f2; }
        .stats { margin-bottom: 20px; }
        .stats div { margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Roxas Sky Fitness Gym</h1>
        <h2>Equipment Inventory Report</h2>
        <p>{{ now()->format('F d, Y') }}</p>
    </div>

    <div class="stats">
        <h3>Equipment Statistics</h3>
        <div>Active Equipment: {{ $equipmentStats['active'] }}</div>
        <div>Inactive Equipment: {{ $equipmentStats['inactive'] }}</div>
        <div>Damaged Equipment: {{ $equipmentStats['damaged'] }}</div>
        <div>Equipment Under Maintenance: {{ $equipmentStats['maintenance'] }}</div>
        <div>Total Equipment: {{ $equipmentStats['total'] }}</div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Equipment Name</th>
                <th>Total Number</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($equipments as $index => $equipment)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $equipment->equipment_name }}</td>
                    <td>{{ $equipment->total_number }}</td>
                    <td>{{ ucfirst($equipment->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
