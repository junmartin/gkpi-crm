<!DOCTYPE html>
<html>
<head>
    <title>Asset Report</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 3px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <h1>Asset Report</h1>

    <p>Generated on: {{ $timestamp }}</p>

    <table>
        <thead>
            <tr>
                <th>Asset</th>
                <th>Owner</th>
                <th>PIC</th>
                <th>Status</th>
                <th>Location</th>
                <th>Spec</th>
                <th>Created by</th>
                <th>Created date</th>
                <th>Update by</th>
                <th>Update date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assets as $asset)
                <tr>
                    <td>{{ $asset->asset_type->name }} - {{ $asset->name }}</td>
                    <td>{{ $asset->ownership }}</td>
                    <td>{{ $asset->pic }}</td>
                    <td>{{ $asset_status[$asset->status] }}</td>
                    <td>{{ $asset->location }}</td>
                    <td>{!! nl2br(e($asset->spec)) !!}</td>
                    <td>{{ $asset->created_by->name ?? '' }}</td>
                    <td>{{ $asset->created_at }}</td>
                    <td>{{ $asset->updated_by->name ?? '' }}</td>
                    <td>{{ $asset->updated_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
