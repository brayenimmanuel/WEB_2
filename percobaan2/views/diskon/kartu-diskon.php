<?php
// Koneksi database
$server = "localhost";
$user = "root";
$password = "";
$database = "db_koperasi";

$koneksi = mysqli_connect($server, $user, $password, $database);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Proses Hapus Data
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = $_GET['id'];
    
    // Cek apakah kartu diskon terhubung dengan anggota
    $cek_anggota = mysqli_query($koneksi, "SELECT * FROM anggota WHERE kartu_diskon_id = $id");
    if (mysqli_num_rows($cek_anggota) > 0) {
        echo "<script>
            alert('Kartu diskon ini tidak dapat dihapus karena masih digunakan oleh anggota!');
            window.location.href = 'kartu-diskon.php';
        </script>";
        exit;
    }
    
    $query = "DELETE FROM kartu_diskon WHERE id = $id";
    $result = mysqli_query($koneksi, $query);
    
    if ($result) {
        echo "<script>
            alert('Data kartu diskon berhasil dihapus!');
            window.location.href = 'kartu-diskon.php';
        </script>";
    } else {
        echo "<script>
            alert('Gagal menghapus data: " . mysqli_error($koneksi) . "');
            window.location.href = 'kartu-diskon.php';
        </script>";
    }
}

// Proses Tambah/Edit Data
if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $persen_diskon = mysqli_real_escape_string($koneksi, $_POST['persen_diskon']);
    
    // Jika ID diset, berarti update data
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = $_POST['id'];
        $query = "UPDATE kartu_diskon SET 
                  nama = '$nama', 
                  deskripsi = '$deskripsi', 
                  persen_diskon = $persen_diskon 
                  WHERE id = $id";
        $pesan_sukses = "Data kartu diskon berhasil diupdate!";
    } else {
        // Jika tidak ada ID, berarti insert data baru
        $query = "INSERT INTO kartu_diskon (nama, deskripsi, persen_diskon) 
                 VALUES ('$nama', '$deskripsi', $persen_diskon)";
        $pesan_sukses = "Data kartu diskon berhasil ditambahkan!";
    }
    
    $result = mysqli_query($koneksi, $query);
    
    if ($result) {
        echo "<script>
            alert('$pesan_sukses');
            window.location.href = 'kartu-diskon.php';
        </script>";
    } else {
        echo "<script>
            alert('Gagal menyimpan data: " . mysqli_error($koneksi) . "');
        </script>";
    }
}

// Ambil data untuk form edit
$id_edit = "";
$nama_edit = "";
$deskripsi_edit = "";
$persen_diskon_edit = "";

if (isset($_GET['aksi']) && $_GET['aksi'] == 'edit') {
    $id_edit = $_GET['id'];
    $query = "SELECT * FROM kartu_diskon WHERE id = $id_edit";
    $result = mysqli_query($koneksi, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        $nama_edit = $data['nama'];
        $deskripsi_edit = $data['deskripsi'];
        $persen_diskon_edit = $data['persen_diskon'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kartu Diskon Koperasi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <div class="container mt-4">
        <!-- <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 rounded">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Koperasi Pegawai</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="data-pegawai.php">Data Pegawai</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="data-anggota.php">Data Anggota</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="data-kartu-diskon.php">Kartu Diskon</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav> -->

        <h1 class="mb-4">Data Kartu Diskon Koperasi</h1>
        
        <!-- Form Tambah/Edit Data -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <?php echo (isset($_GET['aksi']) && $_GET['aksi'] == 'edit') ? 'Edit Data Kartu Diskon' : 'Tambah Data Kartu Diskon'; ?>
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    <?php if (isset($_GET['aksi']) && $_GET['aksi'] == 'edit'): ?>
                        <input type="hidden" name="id" value="<?php echo $id_edit; ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Kartu</label>
                        <input type="text" class="form-control" id="nama" name="nama" value="<?php echo $nama_edit; ?>" required maxlength="45">
                    </div>
                    
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?php echo $deskripsi_edit; ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="persen_diskon" class="form-label">Persentase Diskon (%)</label>
                        <input type="number" class="form-control" id="persen_diskon" name="persen_diskon" value="<?php echo $persen_diskon_edit; ?>" required min="0" max="100">
                    </div>
                    
                    <button type="submit" name="simpan" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <!-- <a href="kartu-diskon.php" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Batal
                    </a> -->
                    <a href="../dashboard.php" class="btn btn-success">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </form>
            </div>
        </div>
        
        <!-- Tabel Data -->
        <div class="card">
            <div class="card-header bg-success text-white">
                Daftar Kartu Diskon Koperasi
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>No.</th>
                                <th>Nama Kartu</th>
                                <th>Deskripsi</th>
                                <th>Persentase Diskon</th>
                                <th>Jumlah Anggota</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT k.*, 
                                     (SELECT COUNT(*) FROM anggota WHERE kartu_diskon_id = k.id) as jumlah_anggota
                                     FROM kartu_diskon k
                                     ORDER BY k.id DESC";
                            $result = mysqli_query($koneksi, $query);
                            $no = 1;
                            
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>";
                                    echo "<td>$no</td>";
                                    echo "<td>".$row['nama']."</td>";
                                    echo "<td>".$row['deskripsi']."</td>";
                                    echo "<td>".$row['persen_diskon']."%</td>";
                                    echo "<td>".$row['jumlah_anggota']."</td>";
                                    echo "<td>
                                            <a href='kartu-diskon.php?aksi=edit&id=".$row['id']."' class='btn btn-warning btn-sm'>
                                                <i class='bi bi-pencil'></i> Edit
                                            </a>
                                            <a href='kartu-diskon.php?aksi=hapus&id=".$row['id']."' class='btn btn-danger btn-sm' onclick='return confirm(\"Apakah Anda yakin ingin menghapus data ini?\")'>
                                                <i class='bi bi-trash'></i> Hapus
                                            </a>
                                          </td>";
                                    echo "</tr>";
                                    $no++;
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center'>Tidak ada data kartu diskon</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>