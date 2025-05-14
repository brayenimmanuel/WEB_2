<?php
// File: jenis-produk.php
// Manajemen Jenis Produk untuk Sistem Koperasi

// Mulai session untuk manajemen pengguna (opsional)
session_start();

// Koneksi database
$koneksi = mysqli_connect("localhost", "root", "", "db_koperasi");

// Periksa koneksi database
if (mysqli_connect_errno()) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Fungsi anti-injection untuk keamanan input
function anti_injection($data) {
    global $koneksi;
    return mysqli_real_escape_string($koneksi, htmlspecialchars(stripslashes(trim($data))));
}

// Variabel untuk pesan notifikasi
$pesan = "";

// Proses Tambah Jenis Produk
if (isset($_POST['tambah'])) {
    $nama = anti_injection($_POST['nama']);
    $deskripsi = anti_injection($_POST['deskripsi'] ?? '');

    // Validasi input
    if (empty($nama)) {
        $pesan = "<div class='alert alert-danger'>Nama jenis produk harus diisi!</div>";
    } else {
        // Cek duplikasi nama jenis produk
        $cek_duplikat = mysqli_query($koneksi, "SELECT * FROM jenis_produk WHERE nama = '$nama'");
        if (mysqli_num_rows($cek_duplikat) > 0) {
            $pesan = "<div class='alert alert-danger'>Jenis produk sudah ada!</div>";
        } else {
            // Query tambah jenis produk
            $query_tambah = "INSERT INTO jenis_produk (nama, deskripsi) VALUES ('$nama', '$deskripsi')";
            if (mysqli_query($koneksi, $query_tambah)) {
                $pesan = "<div class='alert alert-success'>Jenis produk berhasil ditambahkan!</div>";
            } else {
                $pesan = "<div class='alert alert-danger'>Gagal menambahkan jenis produk: " . mysqli_error($koneksi) . "</div>";
            }
        }
    }
}

// Proses Update Jenis Produk
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $nama = anti_injection($_POST['nama']);
    $deskripsi = anti_injection($_POST['deskripsi'] ?? '');

    // Validasi input
    if (empty($nama)) {
        $pesan = "<div class='alert alert-danger'>Nama jenis produk harus diisi!</div>";
    } else {
        // Cek duplikasi nama jenis produk di data lain
        $cek_duplikat = mysqli_query($koneksi, "SELECT * FROM jenis_produk WHERE nama = '$nama' AND id != $id");
        if (mysqli_num_rows($cek_duplikat) > 0) {
            $pesan = "<div class='alert alert-danger'>Jenis produk sudah ada!</div>";
        } else {
            // Query update jenis produk
            $query_update = "UPDATE jenis_produk SET nama = '$nama', deskripsi = '$deskripsi' WHERE id = $id";
            if (mysqli_query($koneksi, $query_update)) {
                $pesan = "<div class='alert alert-success'>Jenis produk berhasil diupdate!</div>";
            } else {
                $pesan = "<div class='alert alert-danger'>Gagal update jenis produk: " . mysqli_error($koneksi) . "</div>";
            }
        }
    }
}

// Proses Hapus Jenis Produk
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);

    // Periksa apakah jenis produk masih digunakan di produk
    $cek_produk = mysqli_query($koneksi, "SELECT * FROM produk WHERE jenis_produk_id = $id");
    if (mysqli_num_rows($cek_produk) > 0) {
        $pesan = "<div class='alert alert-danger'>Jenis produk tidak bisa dihapus karena masih digunakan!</div>";
    } else {
        // Query hapus jenis produk
        $query_hapus = "DELETE FROM jenis_produk WHERE id = $id";
        if (mysqli_query($koneksi, $query_hapus)) {
            $pesan = "<div class='alert alert-success'>Jenis produk berhasil dihapus!</div>";
        } else {
            $pesan = "<div class='alert alert-danger'>Gagal menghapus jenis produk: " . mysqli_error($koneksi) . "</div>";
        }
    }
}

// Proses Edit (Ambil data untuk form)
$data_edit = null;
if (isset($_GET['edit'])) {
    $id_edit = intval($_GET['edit']);
    $query_edit = mysqli_query($koneksi, "SELECT * FROM jenis_produk WHERE id = $id_edit");
    $data_edit = mysqli_fetch_assoc($query_edit);
}

// Pagination
$batas = 10; // Jumlah data per halaman
$halaman = isset($_GET['halaman']) ? intval($_GET['halaman']) : 1;
$mulai = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

// Hitung total data dan halaman
$query_total = "SELECT COUNT(*) as total FROM jenis_produk";
$result_total = mysqli_query($koneksi, $query_total);
$data_total = mysqli_fetch_assoc($result_total);
$total_halaman = ceil($data_total['total'] / $batas);

// Query ambil data dengan pagination
$query = "SELECT 
            jp.id, 
            jp.nama, 
            jp.deskripsi, 
            COUNT(p.id) as jumlah_produk 
          FROM jenis_produk jp 
          LEFT JOIN produk p ON jp.id = p.jenis_produk_id 
          GROUP BY jp.id 
          ORDER BY jp.nama 
          LIMIT $mulai, $batas";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Jenis Produk</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .table-action-column {
            white-space: nowrap;
            width: 1%;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Manajemen Jenis Produk</h1>

    <a href="dashboard.php" class="btn btn-secondary mb-3">Kembali ke Dashboard</a>
    
    <!-- Pesan Notifikasi -->
    <?php echo $pesan; ?>

    <!-- Form Input/Edit Jenis Produk -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5>
                <i class="fas fa-<?php echo $data_edit ? 'edit' : 'plus-circle'; ?>"></i>
                <?php echo $data_edit ? 'Edit' : 'Tambah'; ?> Jenis Produk
            </h5>
        </div>
        <div class="card-body">
            <form method="post" action="">
                <?php if ($data_edit): ?>
                    <input type="hidden" name="id" value="<?php echo $data_edit['id']; ?>">
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Jenis Produk</label>
                        <input type="text" name="nama" class="form-control" 
                               value="<?php echo $data_edit ? htmlspecialchars($data_edit['nama']) : ''; ?>" 
                               required maxlength="100">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Deskripsi (Opsional)</label>
                        <textarea name="deskripsi" class="form-control" rows="3" maxlength="255"><?php 
                            echo $data_edit ? htmlspecialchars($data_edit['deskripsi']) : ''; 
                        ?></textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <?php if ($data_edit): ?>
                        <button type="submit" name="update" class="btn btn-success me-2">
                            <i class="fas fa-save"></i> Update
                        </button>
                        <a href="jenis-produk.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    <?php else: ?>
                        <button type="submit" name="tambah" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tambah
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Jenis Produk -->
    <div class="card">
        <div class="card-header bg-info text-white">
            <h5><i class="fas fa-list"></i> Daftar Jenis Produk</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Jenis Produk</th>
                            <th>Deskripsi</th>
                            <th>Jumlah Produk</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $nomor = $mulai + 1;
                        if (mysqli_num_rows($result) > 0): 
                            while ($row = mysqli_fetch_assoc($result)): 
                        ?>
                            <tr>
                                <td><?php echo $nomor++; ?></td>
                                <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                <td><?php echo htmlspecialchars($row['deskripsi'] ?: '-'); ?></td>
                                <td><?php echo $row['jumlah_produk']; ?></td>
                                <td class="table-action-column">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-info" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#detailModal<?php echo $row['id']; ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="jenis-produk.php?edit=<?php echo $row['id']; ?>" 
                                           class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="jenis-produk.php?hapus=<?php echo $row['id']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Yakin hapus jenis produk?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php 
                            endwhile; 
                        else: 
                        ?>
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data jenis produk</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav>
                <ul class="pagination justify-content-center">
                    <?php for ($x = 1; $x <= $total_halaman; $x++): ?>
                        <li class="page-item <?php echo ($halaman == $x) ? 'active' : ''; ?>">
                            <a class="page-link" href="?halaman=<?php echo $x; ?>"><?php echo $x; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Modal Detail untuk setiap jenis produk -->
    <?php 
    // Reset pointer hasil query
    mysqli_data_seek($result, 0);
    while ($row = mysqli_fetch_assoc($result)): 
    ?>
        <div class="modal fade" id="detailModal<?php echo $row['id']; ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">Detail Jenis Produk</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered">
                            <tr>
                                <th>ID</th>
                                <td><?php echo $row['id']; ?></td>
                            </tr>
                            <tr>
                                <th>Nama</th>
                                <td><?php echo htmlspecialchars($row['nama']); ?></td>
                            </tr>
                            <tr>
                                <th>Deskripsi</th>
                                <td><?php echo htmlspecialchars($row['deskripsi'] ?: 'Tidak ada deskripsi'); ?></td>
                            </tr>
                            <tr>
                                <th>Jumlah Produk</th>
                                <td><?php echo $row['jumlah_produk']; ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Tutup koneksi database
mysqli_close($koneksi);
?>
