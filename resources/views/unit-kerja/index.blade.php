<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Daftar Unit Kerja</title>
</head>

<body>
    <h1>Daftar Unit Kerja</h1>
    <ul>
        @foreach ($units as $unit)
        <li>
            {{ $unit->kode }} - {{ $unit->nama }}
        </li>
        @endforeach
</body>

</html>