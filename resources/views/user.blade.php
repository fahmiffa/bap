<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        table,
        th,
        td {
            border: 1px solid #000;
            padding: 4px;
        }

        thead {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <h3>{{ $title }}</h3>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="50%">Dinas / Instansi</th>
                <th>Nama</th>
                <th>Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td>
                        <center>{{ $loop->iteration }}</center>
                    </td>
                    <td>{{ $item->value }}</td>
                    <td>{{ $item->name }}</td>
                    <td>
                        <img src="{{ public_path('storage/' . $item->ttd) }}" alt="Tanda Tangan" width="80%">
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
