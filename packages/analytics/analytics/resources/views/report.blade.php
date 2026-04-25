<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>PrismPath Report</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111827; margin: 32px; }
        h1 { margin-bottom: 4px; }
        table { border-collapse: collapse; width: 100%; margin-top: 24px; }
        th, td { border: 1px solid #e5e7eb; padding: 10px; text-align: left; }
        th { background: #f3f4f6; }
    </style>
</head>
<body>
    <h1>PrismPath Analytics Report</h1>
    <p>Generated {{ now()->toDayDateTimeString() }}</p>

    <table>
        <thead><tr><th>Metric</th><th>Value</th></tr></thead>
        <tbody>
        @foreach($payload['stats'] as $metric => $value)
            <tr><td>{{ Str::headline($metric) }}</td><td>{{ $value }}</td></tr>
        @endforeach
        </tbody>
    </table>

    <table>
        <thead><tr><th>Page</th><th>Views</th><th>Avg Duration</th><th>Exit Rate</th></tr></thead>
        <tbody>
        @foreach($payload['topPages'] as $page)
            <tr><td>{{ $page->path }}</td><td>{{ $page->views }}</td><td>{{ $page->duration }}s</td><td>{{ $page->exit_rate }}%</td></tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>

