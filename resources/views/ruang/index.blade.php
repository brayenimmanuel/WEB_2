<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Daftar Ruang</title>
</head>

<body>
    <h1>Daftar Ruang</h1>
    <ul>
        @foreach ($ruangs as $ruang)
        <li>
            {{ $ruang->kode }} - {{ $ruang->nama }} - {{ $ruang->status }}
        </li>
        @endforeach
</body>

</html>