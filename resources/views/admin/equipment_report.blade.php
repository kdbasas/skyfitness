<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Inventory Report</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            padding: 0; 
        }
        .header { 
            text-align: center; 
            margin-bottom: 20px; 
        }
        .header h1 { 
            color: #1A1363; 
            margin-bottom: 5px; 
        }
        .header h2 { 
            color: #444; 
            margin-bottom: 10px; 
        }
        .report-date { 
            font-size: 14px; 
            color: #666; 
        }
        .stats { 
            margin-bottom: 20px; 
            padding: 10px; 
            background-color: #f9f9f9; 
            border: 1px solid #ddd; 
            border-radius: 5px; 
        }
        .stats h3 { 
            margin-bottom: 10px; 
            color: #1A1363; 
        }
        .stats div { 
            padding: 5px 0; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px; 
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 10px; 
            text-align: left; 
        }
        th { 
            background-color: #1A1363; 
            color: white; 
        }
        tbody tr:nth-child(even) { 
            background-color: #f9f9f9; 
        }
        .footer { 
            margin-top: 20px; 
            font-size: 12px; 
            text-align: center; 
            color: #666; 
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Roxas Sky Fitness Gym</h1>
        <h2>Equipment Inventory Report</h2>
        <p class="report-date">Generated on: {{ now()->format('F d, Y') }}</p>
    </div>

    <div class="stats">
        <h3>Equipment Statistics</h3>
        <div><strong>Active Equipment:</strong> {{ $equipmentStats['active'] }}</div>
        <div><strong>Inactive Equipment:</strong> {{ $equipmentStats['inactive'] }}</div>
        <div><strong>Damaged Equipment:</strong> {{ $equipmentStats['damaged'] }}</div>
        <div><strong>Equipment Under Maintenance:</strong> {{ $equipmentStats['maintenance'] }}</div>
        <div><strong>Total Equipment:</strong> {{ $equipmentStats['total'] }}</div>
    </div>

    <table>
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
                    <td>
                        @if ($equipment->status == 'active')
                            <span style="color: green;">Active</span>
                        @elseif ($equipment->status == 'inactive')
                            <span style="color: gray;">Inactive</span>
                        @elseif ($equipment->status == 'damaged')
                            <span style="color: red;">Damaged</span>
                        @else
                            <span style="color: orange;">Under Maintenance</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>© {{ date('Y') }} Roxas Sky Fitness Gym. All Rights Reserved.</p>
    </div>

</body>
</html>
