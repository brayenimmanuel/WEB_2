<?php
// Memulai session
session_start();

// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "db_koperasi");

// Cek koneksi
if (mysqli_connect_errno()) {
    echo "Koneksi database gagal: " . mysqli_connect_error();
    exit();
}

// Fungsi untuk mencegah injeksi SQL
function anti_injection($data)
{
    global $koneksi;
    $filter = mysqli_real_escape_string($koneksi, stripslashes(strip_tags(htmlspecialchars($data, ENT_QUOTES))));
    return $filter;
}

// Inisialisasi variabel pesan
$pesan = "";

// PROSES TAMBAH DATA
if (isset($_POST['tambah'])) {
    $kode = anti_injection($_POST['kode']);
    $nama = anti_injection($_POST['nama']);
    $deskripsi = anti_injection($_POST['deskripsi']);
    $harga = floatval($_POST['harga']);
    $stok = intval($_POST['stok']);
    $jenis_produk_id = intval($_POST['jenis_produk_id']);

    // Validasi input
    if (empty($kode) || empty($nama) || empty($jenis_produk_id)) {
        $pesan = "<div class='alert alert-danger'>Kode, nama produk, dan jenis produk harus diisi!</div>";
    } else {
        // Cek apakah kode produk sudah ada
        $cek = mysqli_query($koneksi, "SELECT kode FROM produk WHERE kode='$kode'");
        if (mysqli_num_rows($cek) > 0) {
            $pesan = "<div class='alert alert-danger'>Kode produk sudah ada, silahkan gunakan kode lain!</div>";
        } else {
            // Query tambah data
            $query = "INSERT INTO produk (kode, nama, deskripsi, harga, stok, jenis_produk_id) 
                      VALUES ('$kode', '$nama', '$deskripsi', $harga, $stok, $jenis_produk_id)";
            $result = mysqli_query($koneksi, $query);

            if ($result) {
                $pesan = "<div class='alert alert-success'>Data produk berhasil ditambahkan!</div>";
            } else {
                $pesan = "<div class='alert alert-danger'>Gagal menambahkan data produk: " . mysqli_error($koneksi) . "</div>";
            }
        }
    }
}

// PROSES EDIT DATA
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $kode = anti_injection($_POST['kode']);
    $nama = anti_injection($_POST['nama']);
    $deskripsi = anti_injection($_POST['deskripsi']);
    $harga = floatval($_POST['harga']);
    $stok = intval($_POST['stok']);
    $jenis_produk_id = intval($_POST['jenis_produk_id']);

    // Validasi input
    if (empty($kode) || empty($nama) || empty($jenis_produk_id)) {
        $pesan = "<div class='alert alert-danger'>Kode, nama produk, dan jenis produk harus diisi!</div>";
    } else {
        // Cek apakah kode produk sudah ada di data lain
        $cek = mysqli_query($koneksi, "SELECT kode FROM produk WHERE kode='$kode' AND id != $id");
        if (mysqli_num_rows($cek) > 0) {
            $pesan = "<div class='alert alert-danger'>Kode produk sudah ada, silahkan gunakan kode lain!</div>";
        } else {
            // Query update data
            $query = "UPDATE produk SET 
                      kode='$kode', 
                      nama='$nama', 
                      deskripsi='$deskripsi', 
                      harga=$harga, 
                      stok=$stok, 
                      jenis_produk_id=$jenis_produk_id 
                      WHERE id=$id";
            $result = mysqli_query($koneksi, $query);

            if ($result) {
                $pesan = "<div class='alert alert-success'>Data produk berhasil diupdate!</div>";
            } else {
                $pesan = "<div class='alert alert-danger'>Gagal mengupdate data produk: " . mysqli_error($koneksi) . "</div>";
            }
        }
    }
}

// PROSES HAPUS DATA
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);

    // Periksa apakah produk sedang digunakan di detail_pesanan
    $cek = mysqli_query($koneksi, "SELECT * FROM detail_pesanan WHERE produk_id=$id");
    if (mysqli_num_rows($cek) > 0) {
        $pesan = "<div class='alert alert-danger'>Produk tidak dapat dihapus karena masih terkait dengan data pesanan!</div>";
    } else {
        $query = "DELETE FROM produk WHERE id=$id";
        $result = mysqli_query($koneksi, $query);

        if ($result) {
            $pesan = "<div class='alert alert-success'>Data produk berhasil dihapus!</div>";
        } else {
            $pesan = "<div class='alert alert-danger'>Gagal menghapus data produk: " . mysqli_error($koneksi) . "</div>";
        }
    }
}

// Ambil data untuk form edit
$data_edit = [];
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $query = "SELECT * FROM produk WHERE id=$id";
    $result = mysqli_query($koneksi, $query);
    $data_edit = mysqli_fetch_assoc($result);
}

// Ambil data jenis produk untuk dropdown
$query_jenis = "SELECT * FROM jenis_produk ORDER BY nama";
$result_jenis = mysqli_query($koneksi, $query_jenis);
$jenis_produk = [];
while ($row = mysqli_fetch_assoc($result_jenis)) {
    $jenis_produk[] = $row;
}

// Pagination
$batas = 10; // Jumlah data per halaman
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

$previous = $halaman - 1;
$next = $halaman + 1;

$query_count = "SELECT COUNT(*) AS jumlah FROM produk";
$result_count = mysqli_query($koneksi, $query_count);
$row_count = mysqli_fetch_assoc($result_count);
$jumlah_data = $row_count['jumlah'];
$total_halaman = ceil($jumlah_data / $batas);

// Query untuk mengambil data produk dengan join ke jenis_produk
$query = "SELECT p.*, jp.nama as jenis_nama 
          FROM produk p 
          JOIN jenis_produk jp ON p.jenis_produk_id = jp.id 
          ORDER BY p.nama 
          LIMIT $halaman_awal, $batas";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Produk - Koperasi</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .table-responsive {
            margin-top: 20px;
        }

        .pagination {
            margin-top: 20px;
        }

        .btn-toolbar {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-4">Data Produk</h1>

        <a href="dashboard.php" class="btn btn-secondary mb-3">Kembali ke Dashboard</a>

        <?php echo $pesan; ?>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <?php if (isset($data_edit['id'])) : ?>
                    <h5><i class="fas fa-edit"></i> Edit Data Produk</h5>
                <?php else : ?>
                    <h5><i class="fas fa-plus"></i> Tambah Data Produk</h5>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <form method="post" action="">
                    <?php if (isset($data_edit['id'])) : ?>
                        <input type="hidden" name="id" value="<?php echo $data_edit['id']; ?>">
                    <?php endif; ?>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="kode" class="form-label">Kode Produk</label>
                            <input type="text" class="form-control" id="kode" name="kode" maxlength="45" required
                                value="<?php echo isset($data_edit['kode']) ? $data_edit['kode'] : ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="nama" class="form-label">Nama Produk</label>
                            <input type="text" class="form-control" id="nama" name="nama" maxlength="45" required
                                value="<?php echo isset($data_edit['nama']) ? $data_edit['nama'] : ''; ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="jenis_produk_id" class="form-label">Jenis Produk</label>
                            <select class="form-select" id="jenis_produk_id" name="jenis_produk_id" required>
                                <option value="">-- Pilih Jenis Produk --</option>
                                <?php foreach ($jenis_produk as $jenis) : ?>
                                    <option value="<?php echo $jenis['id']; ?>"
                                        <?php echo (isset($data_edit['jenis_produk_id']) && $data_edit['jenis_produk_id'] == $jenis['id']) ? 'selected' : ''; ?>>
                                        <?php echo $jenis['nama']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="harga" class="form-label">Harga</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="harga" name="harga" min="0" step="100000" required
                                    value="<?php echo isset($data_edit['harga']) ? $data_edit['harga'] : '0'; ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="stok" class="form-label">Stok</label>
                            <input type="number" class="form-control" id="stok" name="stok" min="0" required
                                value="<?php echo isset($data_edit['stok']) ? $data_edit['stok'] : '0'; ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?php echo isset($data_edit['deskripsi']) ? $data_edit['deskripsi'] : ''; ?></textarea>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <?php if (isset($data_edit['id'])) : ?>
                            <button type="submit" name="update" class="btn btn-success">
                                <i class="fas fa-save"></i> Update Data
                            </button>
                            <a href="data-produk.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        <?php else : ?>
                            <button type="submit" name="tambah" class="btn btn-primary">
                                <i class="fas fa-plus-circle"></i> Tambah Data
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel Data Produk -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5><i class="fas fa-table"></i> Daftar Produk</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama Produk</th>
                                <th>Jenis</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = $halaman_awal + 1;
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                            ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo htmlspecialchars($row['kode']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                        <td><?php echo htmlspecialchars($row['jenis_nama']); ?></td>
                                        <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                        <td><?php echo $row['stok']; ?></td>
                                        <td>
                                            <a href="data-produk.php?edit=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="data-produk.php?hapus=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>
                                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                                data-bs-target="#detailModal<?php echo $row['id']; ?>">
                                                <i class="fas fa-eye"></i> Detail
                                            </button>
                                        </td>
                                    </tr>
                                <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data produk</td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php echo ($halaman <= 1) ? "disabled" : ""; ?>">
                            <a class="page-link" href="<?php echo ($halaman > 1) ? "?halaman=$previous" : "#"; ?>">Previous</a>
                        </li>

                        <?php for ($x = 1; $x <= $total_halaman; $x++) { ?>
                            <li class="page-item <?php echo ($halaman == $x) ? "active" : ""; ?>">
                                <a class="page-link" href="?halaman=<?php echo $x ?>"><?php echo $x; ?></a>
                            </li>
                        <?php } ?>

                        <li class="page-item <?php echo ($halaman >= $total_halaman) ? "disabled" : ""; ?>">
                            <a class="page-link" href="<?php echo ($halaman < $total_halaman) ? "?halaman=$next" : "#"; ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <?php

    // Reset result set pointer ke awal
    mysqli_data_seek($result, 0);
    while ($row = mysqli_fetch_assoc($result)) {
    ?>
        <!-- Modal Detail -->
        <div class="modal fade" id="detailModal<?php echo $row['id']; ?>" tabindex="-1"
            aria-labelledby="detailModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="detailModalLabel<?php echo $row['id']; ?>">
                            Detail Produk: <?php echo htmlspecialchars($row['nama']); ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered">
                            <tr>
                                <th>ID</th>
                                <td><?php echo $row['id']; ?></td>
                            </tr>
                            <tr>
                                <th>Kode</th>
                                <td><?php echo htmlspecialchars($row['kode']); ?></td>
                            </tr>
                            <tr>
                                <th>Nama</th>
                                <td><?php echo htmlspecialchars($row['nama']); ?></td>
                            </tr>
                            <tr>
                                <th>Jenis</th>
                                <td><?php echo htmlspecialchars($row['jenis_nama']); ?></td>
                            </tr>
                            <tr>
                                <th>Harga</th>
                                <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                            </tr>
                            <tr>
                                <th>Stok</th>
                                <td><?php echo $row['stok']; ?></td>
                            </tr>
                            <tr>
                                <th>Deskripsi</th>
                                <td><?php echo nl2br(htmlspecialchars($row['deskripsi'])); ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
    ?>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>