<?php
// Koneksi ke database
$host = "localhost";
$username = "root";
$password = "";
$database = "db_koperasi";

$koneksi = mysqli_connect($host, $username, $password, $database);

// Cek koneksi
if (mysqli_connect_errno()) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Inisialisasi variabel
$id = "";
$status_aktif = "";
$pegawai_id = "";
$kartu_diskon_id = "";
$alert = "";

// Proses tambah data
if (isset($_POST['submit'])) {
    $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;
    $pegawai_id = $_POST['pegawai_id'];
    $kartu_diskon_id = !empty($_POST['kartu_diskon_id']) ? $_POST['kartu_diskon_id'] : null;
    
    // Validasi input
    if (empty($pegawai_id)) {
        $alert = '<div class="alert alert-danger">Pegawai harus dipilih!</div>';
    } else {
        // Cek apakah pegawai sudah terdaftar sebagai anggota
        $cek_query = "SELECT * FROM anggota WHERE pegawai_id = '$pegawai_id'";
        $cek_result = mysqli_query($koneksi, $cek_query);
        
        if (mysqli_num_rows($cek_result) > 0) {
            $alert = '<div class="alert alert-warning">Pegawai ini sudah terdaftar sebagai anggota!</div>';
        } else {
            // Insert data
            $query = "INSERT INTO anggota (status_aktif, pegawai_id, kartu_diskon_id) 
                      VALUES ('$status_aktif', '$pegawai_id', " . ($kartu_diskon_id ? "'$kartu_diskon_id'" : "NULL") . ")";
            
            if (mysqli_query($koneksi, $query)) {
                $alert = '<div class="alert alert-success">Data anggota berhasil ditambahkan!</div>';
                // Reset form
                $status_aktif = "";
                $pegawai_id = "";
                $kartu_diskon_id = "";
            } else {
                $alert = '<div class="alert alert-danger">Error: ' . mysqli_error($koneksi) . '</div>';
            }
        }
    }
}

// Proses update data
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;
    $pegawai_id = $_POST['pegawai_id'];
    $kartu_diskon_id = !empty($_POST['kartu_diskon_id']) ? $_POST['kartu_diskon_id'] : null;
    
    // Validasi input
    if (empty($pegawai_id)) {
        $alert = '<div class="alert alert-danger">Pegawai harus dipilih!</div>';
    } else {
        // Update data
        $query = "UPDATE anggota SET 
                  status_aktif = '$status_aktif', 
                  pegawai_id = '$pegawai_id', 
                  kartu_diskon_id = " . ($kartu_diskon_id ? "'$kartu_diskon_id'" : "NULL") . " 
                  WHERE id = '$id'";
        
        if (mysqli_query($koneksi, $query)) {
            $alert = '<div class="alert alert-success">Data anggota berhasil diperbarui!</div>';
            // Reset form
            $id = "";
            $status_aktif = "";
            $pegawai_id = "";
            $kartu_diskon_id = "";
        } else {
            $alert = '<div class="alert alert-danger">Error: ' . mysqli_error($koneksi) . '</div>';
        }
    }
}

// Proses hapus data
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    
    // Cek apakah anggota memiliki pesanan
    $cek_pesanan = "SELECT * FROM pesanan WHERE anggota_id = '$id'";
    $result_pesanan = mysqli_query($koneksi, $cek_pesanan);
    
    if (mysqli_num_rows($result_pesanan) > 0) {
        $alert = '<div class="alert alert-warning">Anggota tidak dapat dihapus karena memiliki pesanan!</div>';
    } else {
        // Hapus data
        $query = "DELETE FROM anggota WHERE id = '$id'";
        
        if (mysqli_query($koneksi, $query)) {
            $alert = '<div class="alert alert-success">Data anggota berhasil dihapus!</div>';
        } else {
            $alert = '<div class="alert alert-danger">Error: ' . mysqli_error($koneksi) . '</div>';
        }
    }
}

// Proses edit data
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $query = "SELECT * FROM anggota WHERE id = '$id'";
    $result = mysqli_query($koneksi, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $id = $row['id'];
        $status_aktif = $row['status_aktif'];
        $pegawai_id = $row['pegawai_id'];
        $kartu_diskon_id = $row['kartu_diskon_id'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Anggota Koperasi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="card-title">Data Anggota Koperasi</h4>
                    </div>
                    <div class="card-body">
                        <?php echo $alert; ?>
                        
                        <!-- Form Input/Edit Data -->
                        <form method="POST" action="">
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                            
                            <div class="mb-3">
                                <label for="pegawai_id" class="form-label">Pegawai</label>
                                <select class="form-select" name="pegawai_id" id="pegawai_id" required>
                                    <option value="">-- Pilih Pegawai --</option>
                                    <?php
                                    $query_pegawai = "SELECT * FROM pegawai ORDER BY nama ASC";
                                    $result_pegawai = mysqli_query($koneksi, $query_pegawai);
                                    
                                    while ($row_pegawai = mysqli_fetch_assoc($result_pegawai)) {
                                        $selected = ($pegawai_id == $row_pegawai['id']) ? 'selected' : '';
                                        echo "<option value='" . $row_pegawai['id'] . "' $selected>" . $row_pegawai['nama'] . " (" . $row_pegawai['nip'] . ")</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="kartu_diskon_id" class="form-label">Kartu Diskon</label>
                                <select class="form-select" name="kartu_diskon_id" id="kartu_diskon_id">
                                    <option value="">-- Pilih Kartu Diskon --</option>
                                    <?php
                                    $query_kartu = "SELECT * FROM kartu_diskon ORDER BY nama ASC";
                                    $result_kartu = mysqli_query($koneksi, $query_kartu);
                                    
                                    while ($row_kartu = mysqli_fetch_assoc($result_kartu)) {
                                        $selected = ($kartu_diskon_id == $row_kartu['id']) ? 'selected' : '';
                                        echo "<option value='" . $row_kartu['id'] . "' $selected>" . $row_kartu['nama'] . " (" . $row_kartu['persen_diskon'] . "%)</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="status_aktif" name="status_aktif" <?php echo ($status_aktif == 1) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="status_aktif">Status Aktif</label>
                            </div>
                            
                            <?php if ($id) : ?>
                                <button type="submit" name="update" class="btn btn-primary">Perbarui Data</button>
                                <a href="../views/data-anggota.php" class="btn btn-secondary">Batal</a>
                            <?php else : ?>
                                <button type="submit" name="submit" class="btn btn-success">Simpan Data</button>
                            <?php endif; ?>
                                <a href="../views/dashboard.php" class="btn btn-secondary mb">
                                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                                </a>
                                
                        </form>
                        
                        <hr>
                        
                        <!-- Tabel Data Anggota -->
                        <div class="table-responsive mt-4">
                            <table class="table table-striped table-hover table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Pegawai</th>
                                        <th>Status</th>
                                        <th>Kartu Diskon</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = "SELECT a.*, p.nama as nama_pegawai, p.nip, k.nama as nama_kartu, k.persen_diskon 
                                              FROM anggota a
                                              LEFT JOIN pegawai p ON a.pegawai_id = p.id
                                              LEFT JOIN kartu_diskon k ON a.kartu_diskon_id = k.id
                                              ORDER BY p.nama ASC";
                                    $result = mysqli_query($koneksi, $query);
                                    $no = 1;
                                    
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td>" . $no++ . "</td>";
                                            echo "<td>" . $row['nama_pegawai'] . " (" . $row['nip'] . ")</td>";
                                            echo "<td>" . ($row['status_aktif'] ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Tidak Aktif</span>') . "</td>";
                                            echo "<td>" . ($row['nama_kartu'] ? $row['nama_kartu'] . " (" . $row['persen_diskon'] . "%)" : '-') . "</td>";
                                            echo "<td>
                                                    <a href='data-anggota.php?edit=" . $row['id'] . "' class='btn btn-warning btn-sm'><i class='bi bi-pencil-square'></i> Edit</a>
                                                    <a href='data-anggota.php?hapus=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Yakin ingin menghapus data ini?');\"><i class='bi bi-trash'></i> Hapus</a>
                                                  </td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5' class='text-center'>Tidak ada data anggota</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
