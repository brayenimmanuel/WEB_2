<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Daftar Pegawai</title>
</head>

<body>
    <h1>Daftar Pegawai</h1>
    <ul>
        @foreach ($pegawais as $pegawai)
        <li>
            {{ $pegawai->nip }} - {{ $pegawai->nama }} - {{ $pegawai->unit_kerja_id }}
        </li>
        @endforeach
</body>

</html>