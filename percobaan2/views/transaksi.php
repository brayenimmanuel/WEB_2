<?php
// Memulai session
session_start();

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_koperasi";

$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Fungsi untuk mendapatkan daftar pesanan yang belum dibayar
function getPesananBelumBayar($conn)
{
    $sql = "SELECT
                p.id, 
                p.tanggal, 
                p2.nama AS anggota_nama, 
                a.id AS anggota_id,
                p.diskon,
                (SELECT SUM(dp.jumlah * pr.harga)
                FROM detail_pesanan dp
                JOIN produk pr ON dp.produk_id = pr.id
                WHERE dp.pesanan_id = p.id) AS total_bayar
            FROM 
                pesanan p
            JOIN 
                anggota a ON p.anggota_id = a.id
            JOIN
                pegawai p2 ON a.pegawai_id = p2.id
            WHERE 
                p.status_bayar = 0
            ORDER BY 
                p.tanggal DESC";

    $result = $conn->query($sql);
    $pesanan = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pesanan[] = $row;
        }
    }

    return $pesanan;
}

// Fungsi untuk mendapatkan daftar semua pembayaran
function getPembayaran($conn)
{
    $sql = "SELECT pb.id, pb.tanggal, pb.jumlah_bayar, p.id AS pesanan_id, 
            p2.nama AS anggota_nama
            FROM pembayaran pb
            JOIN pesanan p ON pb.pesanan_id = p.id
            JOIN anggota a ON p.anggota_id = a.id
            JOIN pegawai p2 ON a.pegawai_id = p2.id
            ORDER BY pb.tanggal DESC, pb.id DESC";

    $result = $conn->query($sql);
    $pembayaran = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pembayaran[] = $row;
        }
    }

    return $pembayaran;
}

// Fungsi untuk mendapatkan detail pesanan
function getDetailPesanan($conn, $pesanan_id)
{
    $sql = "SELECT dp.jumlah, pr.nama AS produk_nama, pr.harga,
            (dp.jumlah * pr.harga) AS subtotal
            FROM detail_pesanan dp
            JOIN produk pr ON dp.produk_id = pr.id
            WHERE dp.pesanan_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $pesanan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $detail = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $detail[] = $row;
        }
    }

    return $detail;
}

// Fungsi untuk mendapatkan total pesanan
function getTotalPesanan($conn, $pesanan_id)
{
    $sql = "SELECT SUM(dp.jumlah * pr.harga) AS total
            FROM detail_pesanan dp
            JOIN produk pr ON dp.produk_id = pr.id
            WHERE dp.pesanan_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $pesanan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return $row['total'];
}

// Fungsi untuk mendapatkan detail pembayaran berdasarkan ID
function getPembayaranById($conn, $pembayaran_id)
{
    $sql = "SELECT pb.*, p.id AS pesanan_id, 
            p2.nama AS anggota_nama,
            (SELECT SUM(dp.jumlah * pr.harga)
            FROM detail_pesanan dp
            JOIN produk pr ON dp.produk_id = pr.id
            WHERE dp.pesanan_id = p.id) AS total_pesanan
            FROM pembayaran pb
            JOIN pesanan p ON pb.pesanan_id = p.id
            JOIN anggota a ON p.anggota_id = a.id
            JOIN pegawai p2 ON a.pegawai_id = p2.id
            WHERE pb.id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $pembayaran_id);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_assoc();
}

// Proses pembayaran
if (isset($_POST['bayar'])) {
    $pesanan_id = $_POST['pesanan_id'];
    $jumlah_bayar = $_POST['jumlah_bayar'];
    $tanggal = date('Y-m-d');

    // Mulai transaction
    $conn->begin_transaction();

    try {
        // Insert data pembayaran
        $sql_bayar = "INSERT INTO pembayaran (pesanan_id, tanggal, jumlah_bayar) VALUES (?, ?, ?)";
        $stmt_bayar = $conn->prepare($sql_bayar);
        $stmt_bayar->bind_param("isd", $pesanan_id, $tanggal, $jumlah_bayar);
        $stmt_bayar->execute();

        // Update status pembayaran di tabel pesanan
        $sql_update = "UPDATE pesanan SET status_bayar = 1 WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("i", $pesanan_id);
        $stmt_update->execute();

        // Commit transaction
        $conn->commit();

        $_SESSION['success_message'] = "Pembayaran berhasil disimpan";
        header("Location: transaksi.php");
        exit();
    } catch (Exception $e) {
        // Rollback transaction jika terjadi kesalahan
        $conn->rollback();
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
    }
}

// Proses edit pembayaran
if (isset($_POST['edit_pembayaran'])) {
    $pembayaran_id = $_POST['pembayaran_id'];
    $jumlah_bayar_baru = $_POST['jumlah_bayar_edit'];
    $tanggal_baru = $_POST['tanggal_edit'];

    // Mulai transaction
    $conn->begin_transaction();

    try {
        // Update data pembayaran
        $sql_edit = "UPDATE pembayaran SET tanggal = ?, jumlah_bayar = ? WHERE id = ?";
        $stmt_edit = $conn->prepare($sql_edit);
        $stmt_edit->bind_param("sdi", $tanggal_baru, $jumlah_bayar_baru, $pembayaran_id);
        $stmt_edit->execute();

        // Commit transaction
        $conn->commit();

        $_SESSION['success_message'] = "Pembayaran berhasil diubah";
        header("Location: transaksi.php");
        exit();
    } catch (Exception $e) {
        // Rollback transaction jika terjadi kesalahan
        $conn->rollback();
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
    }
}

// Proses hapus pembayaran
if (isset($_GET['delete'])) {
    $pembayaran_id = $_GET['delete'];

    // Mulai transaction
    $conn->begin_transaction();

    try {
        // Ambil detail pembayaran
        $detail_pembayaran = getPembayaranById($conn, $pembayaran_id);

        if ($detail_pembayaran) {
            // Hapus pembayaran
            $sql_delete = "DELETE FROM pembayaran WHERE id = ?";
            $stmt_delete = $conn->prepare($sql_delete);
            $stmt_delete->bind_param("i", $pembayaran_id);
            $stmt_delete->execute();

            // Update status pesanan kembali menjadi belum dibayar
            $sql_update_pesanan = "UPDATE pesanan SET status_bayar = 0 WHERE id = ?";
            $stmt_update_pesanan = $conn->prepare($sql_update_pesanan);
            $stmt_update_pesanan->bind_param("i", $detail_pembayaran['pesanan_id']);
            $stmt_update_pesanan->execute();

            // Commit transaction
            $conn->commit();

            $_SESSION['success_message'] = "Pembayaran berhasil dihapus";
            header("Location: transaksi.php");
            exit();
        } else {
            throw new Exception("Pembayaran tidak ditemukan");
        }
    } catch (Exception $e) {
        // Rollback transaction jika terjadi kesalahan
        $conn->rollback();
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
    }
}

// Proses view detail pesanan
$detail_pesanan = array();
$total_pesanan = 0;
$selected_pesanan = null;
$edit_pembayaran = null;

if (isset($_GET['view']) && !empty($_GET['view'])) {
    $pesanan_id = $_GET['view'];
    $detail_pesanan = getDetailPesanan($conn, $pesanan_id);
    $total_pesanan = getTotalPesanan($conn, $pesanan_id);

    // Ambil data pesanan yang dipilih
    $sql_pesanan = "SELECT p.id, p.tanggal, p2.nama AS anggota_nama, a.id AS anggota_id
                   FROM pesanan p
                   JOIN anggota a ON p.anggota_id = a.id
                   JOIN pegawai p2 ON a.pegawai_id = p2.id
                   WHERE p.id = ?";
    $stmt_pesanan = $conn->prepare($sql_pesanan);
    $stmt_pesanan->bind_param("i", $pesanan_id);
    $stmt_pesanan->execute();
    $result_pesanan = $stmt_pesanan->get_result();

    if ($result_pesanan->num_rows > 0) {
        $selected_pesanan = $result_pesanan->fetch_assoc();
    }
}

// Proses edit pembayaran
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $pembayaran_id = $_GET['edit'];
    $edit_pembayaran = getPembayaranById($conn, $pembayaran_id);
}

// Ambil daftar pesanan yang belum dibayar
$pesanan_belum_bayar = getPesananBelumBayar($conn);

// Ambil daftar pembayaran
$pembayaran = getPembayaran($conn);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Pesanan - Koperasi Pegawai</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 20px auto;
        }

        .card {
            margin-bottom: 20px;
        }

        .detail-table {
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="mb-4">Pembayaran Pesanan Koperasi</h1>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item"><a href="pemesanan.php">Pemesanan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pembayaran</li>
            </ol>
        </nav>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php
                echo $_SESSION['success_message'];
                unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?php
                echo $_SESSION['error_message'];
                unset($_SESSION['error_message']);
                ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Daftar Pesanan Belum Dibayar</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tanggal</th>
                                        <th>Anggota</th>
                                        <th>Total</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($pesanan_belum_bayar) > 0): ?>
                                        <?php foreach ($pesanan_belum_bayar as $p):
                                            $harga_sebelum_diskon = $p['total_bayar'];
                                            $nilai_diskon = ($harga_sebelum_diskon * $p['diskon']) / 100;
                                            $harga_setelah_diskon = $harga_sebelum_diskon - $nilai_diskon; ?>
                                            <tr>
                                                <td><?= $p['id'] ?></td>
                                                <td><?= date('d/m/Y', strtotime($p['tanggal'])) ?></td>
                                                <td><?= $p['anggota_nama'] ?></td>
                                                <td><?php if ($p['diskon'] > 0): ?>
                                                <del class="text-muted">Rp <?php echo number_format($harga_sebelum_diskon, 0, ',', '.'); ?></del><br>
                                                <strong>Rp <?php echo number_format($harga_setelah_diskon, 0, ',', '.'); ?></strong>
                                            <?php else: ?>
                                                <strong>Rp <?php echo number_format($harga_sebelum_diskon, 0, ',', '.'); ?></strong>
                                            <?php endif; ?></td>
                                                <td>
                                                    <a href="transaksi.php?view=<?= $p['id'] ?>" class="btn btn-sm btn-info">Detail</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">Tidak ada pesanan yang belum dibayar</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">Riwayat Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tanggal</th>
                                        <th>ID Pesanan</th>
                                        <th>Anggota</th>
                                        <th>Jumlah Bayar</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($pembayaran) > 0): ?>
                                        <?php foreach ($pembayaran as $bayar): ?>
                                            <tr>
                                                <td><?= $bayar['id'] ?></td>
                                                <td><?= date('d/m/Y', strtotime($bayar['tanggal'])) ?></td>
                                                <td><?= $bayar['pesanan_id'] ?></td>
                                                <td><?= $bayar['anggota_nama'] ?></td>
                                                <td>Rp <?= number_format($bayar['jumlah_bayar'], 0, ',', '.') ?></td>
                                                <td>
                                                    <a href="transaksi.php?edit=<?= $bayar['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                                    <a href="transaksi.php?delete=<?= $bayar['id'] ?>" class="btn btn-sm btn-danger btn-hapus">Hapus</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">Belum ada data pembayaran</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <?php if ($selected_pesanan): ?>
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="card-title mb-0">Detail Pesanan #<?= $selected_pesanan['id'] ?></h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Tanggal:</strong> <?= date('d/m/Y', strtotime($selected_pesanan['tanggal'])) ?><br>
                                <strong>Anggota:</strong> <?= $selected_pesanan['anggota_nama'] ?>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm table-bordered detail-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Produk</th>
                                            <th>Harga</th>
                                            <th>Jumlah</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($detail_pesanan as $detail): ?>
                                            <tr>
                                                <td><?= $detail['produk_nama'] ?></td>
                                                <td>Rp <?= number_format($detail['harga'], 0, ',', '.') ?></td>
                                                <td><?= $detail['jumlah'] ?></td>
                                                <td>Rp <?= number_format($detail['subtotal'], 0, ',', '.') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="3" class="text-end">Total</th>
                                            <th><?php if ($p['diskon'] > 0): ?>
                                                <del class="text-muted">Rp <?php echo number_format($harga_sebelum_diskon, 0, ',', '.'); ?></del><br>
                                                <strong>Rp <?php echo number_format($harga_setelah_diskon, 0, ',', '.'); ?></strong>
                                            <?php else: ?>
                                                <strong>Rp <?php echo number_format($harga_sebelum_diskon, 0, ',', '.'); ?></strong>
                                            <?php endif; ?></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <form action="transaksi.php" method="post" id="formPembayaran" class="mt-3">
                                <input type="hidden" name="pesanan_id" value="<?= $selected_pesanan['id'] ?>">
                                <div class="mb-3">
                                    <label for="jumlah_bayar" class="form-label">Jumlah Pembayaran</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="jumlah_bayar" id="jumlah_bayar" class="form-control" min="0" required>
                                    </div>
                                </div>
                                <button type="submit" name="bayar" class="btn btn-primary">Proses Pembayaran</button>
                                <a href="transaksi.php" class="btn btn-secondary">Batal</a>
                            </form>
                        </div>
                    </div>
                <?php elseif ($edit_pembayaran): ?>
                    <div class="card">
                        <div class="card-header bg-warning text-white">
                            <h5 class="card-title mb-0">Edit Pembayaran #<?= $edit_pembayaran['id'] ?></h5>
                        </div>
                        <div class="card-body">
                            <form action="transaksi.php" method="post" id="formEditPembayaran">
                                <input type="hidden" name="pembayaran_id" value="<?= $edit_pembayaran['id'] ?>">
                                <div class="mb-3">
                                    <label for="pesanan_id_edit" class="form-label">ID Pesanan</label>
                                    <input type="text" id="pesanan_id_edit" class="form-control" value="<?= $edit_pembayaran['pesanan_id'] ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="anggota_nama_edit" class="form-label">Anggota</label>
                                    <input type="text" id="anggota_nama_edit" class="form-control" value="<?= $edit_pembayaran['anggota_nama'] ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="tanggal_edit" class="form-label">Tanggal</label>
                                    <input type="date" name="tanggal_edit" id="tanggal_edit" class="form-control" value="<?= $edit_pembayaran['tanggal'] ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="jumlah_bayar_edit" class="form-label">Jumlah Pembayaran</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="jumlah_bayar_edit" id="jumlah_bayar_edit" class="form-control"
                                            min="1" value="<?= $edit_pembayaran['jumlah_bayar'] ?>"
                                            max="<?= $edit_pembayaran['total_pesanan'] ?>" step="1000" required>
                                    </div>
                                    <small class="form-text text-muted">Total Pesanan: Rp <?= number_format($edit_pembayaran['total_pesanan'], 0, ',', '.') ?></small>
                                </div>
                                <button type="submit" name="edit_pembayaran" class="btn btn-primary">Simpan Perubahan</button>
                                <a href="transaksi.php" class="btn btn-secondary">Batal</a>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card">
                        <div class="card-header bg-warning">
                            <h5 class="card-title mb-0">Informasi</h5>
                        </div>
                        <div class="card-body">
                            <p>Pilih pesanan dari daftar untuk melihat detail dan melakukan pembayaran.</p>

                            <div class="alert alert-info">
                                <h6>Langkah Pembayaran:</h6>
                                <ol>
                                    <li>Pilih pesanan yang akan dibayar dari daftar di sebelah kiri</li>
                                    <li>Periksa detail pesanan dan total yang harus dibayar</li>
                                    <li>Masukkan jumlah pembayaran</li>
                                    <li>Klik tombol "Proses Pembayaran"</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Validasi form pembayaran sebelum submit
            $('#formPembayaran').submit(function(e) {
                const totalPesanan = <?= $total_pesanan ?? 0 ?>;
                const jumlahBayar = parseFloat($('#jumlah_bayar').val());

                // if (jumlahBayar < totalPesanan) {
                //     e.preventDefault();
                //     alert('Jumlah pembayaran tidak boleh kurang dari total pesanan!');
                //     return false;
                // }

                return confirm('Apakah Anda yakin ingin memproses pembayaran ini?');
            });

            // Validasi form edit pembayaran
            $('#formEditPembayaran').submit(function(e) {
                const totalPesanan = <?= $edit_pembayaran ? $edit_pembayaran['total_pesanan'] : 0 ?>;
                const jumlahBayar = parseFloat($('#jumlah_bayar_edit').val());

                if (jumlahBayar > totalPesanan) {
                    e.preventDefault();
                    alert('Jumlah pembayaran tidak boleh melebihi total pesanan!');
                    return false;
                }

                return confirm('Apakah Anda yakin ingin mengubah pembayaran ini?');
            });

            // Konfirmasi hapus pembayaran
            $('.btn-hapus').click(function(e) {
                return confirm('Apakah Anda yakin ingin menghapus pembayaran ini? Status pesanan akan dikembalikan ke belum dibayar.');
            });
        });
    </script>
</body>

</html>
