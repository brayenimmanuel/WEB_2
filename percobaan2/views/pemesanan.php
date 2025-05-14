<?php
// Koneksi database
$host = "localhost";
$username = "root";
$password = "";
$database = "db_koperasi";

$conn = mysqli_connect($host, $username, $password, $database);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Inisialisasi variabel
$error = "";
$success = "";

// Mengambil data anggota untuk dropdown
$query_anggota = "SELECT a.*, p.nama FROM anggota a JOIN pegawai p ON a.pegawai_id = p.id WHERE a.status_aktif = 1";
$result_anggota = mysqli_query($conn, $query_anggota);

// Mengambil data produk untuk dropdown
$query_produk = "SELECT p.*, jp.nama AS jenis_produk_nama 
                FROM produk p 
                JOIN jenis_produk jp ON p.jenis_produk_id = jp.id";
$result_produk = mysqli_query($conn, $query_produk);
$all_produk = [];
while ($produk = mysqli_fetch_assoc($result_produk)) {
    $all_produk[] = $produk;
}

// Reset pointer
mysqli_data_seek($result_produk, 0);

// Proses form pemesanan
if (isset($_POST['submit_pemesanan'])) {
    $anggota_id = $_POST['anggota_id'];
    $produk_id = $_POST['produk_id'];
    $jumlah = $_POST['jumlah'];
    $tanggal = date('Y-m-d');
    
    // Validasi input
    if (empty($anggota_id) || empty($produk_id) || empty($jumlah)) {
        $error = "Silakan lengkapi semua field!";
    } else {
        // Cek stok produk
        $query_stok = "SELECT stok FROM produk WHERE id = $produk_id";
        $result_stok = mysqli_query($conn, $query_stok);
        $data_stok = mysqli_fetch_assoc($result_stok);
        
        if ($jumlah > $data_stok['stok']) {
            $error = "Stok tidak mencukupi!";
        } else {
            // Proses pemesanan
            // 1. Buat entry di tabel pesanan
            $diskon = 0; // Default value, bisa disesuaikan dengan logika diskon
            $status_bayar = 0; // Belum dibayar
            
            // Cek apakah anggota memiliki kartu diskon
            $query_diskon = "SELECT kd.persen_diskon 
                            FROM kartu_diskon kd 
                            JOIN anggota a ON kd.id = a.kartu_diskon_id 
                            WHERE a.id = $anggota_id";
            $result_diskon = mysqli_query($conn, $query_diskon);
            
            if (mysqli_num_rows($result_diskon) > 0) {
                $data_diskon = mysqli_fetch_assoc($result_diskon);
                $diskon = $data_diskon['persen_diskon'];
            }
            
            $query_insert_pesanan = "INSERT INTO pesanan (anggota_id, tanggal, diskon, status_bayar) 
                                    VALUES ($anggota_id, '$tanggal', $diskon, $status_bayar)";
            
            if (mysqli_query($conn, $query_insert_pesanan)) {
                $pesanan_id = mysqli_insert_id($conn);
                
                // 2. Tambah detail pesanan
                $query_insert_detail = "INSERT INTO detail_pesanan (pesanan_id, produk_id, jumlah) 
                                       VALUES ($pesanan_id, $produk_id, $jumlah)";
                
                if (mysqli_query($conn, $query_insert_detail)) {
                    // 3. Update stok produk
                    $stok_baru = $data_stok['stok'] - $jumlah;
                    $query_update_stok = "UPDATE produk SET stok = $stok_baru WHERE id = $produk_id";
                    mysqli_query($conn, $query_update_stok);
                    
                    $success = "Pemesanan berhasil dibuat! ID Pesanan: " . $pesanan_id;
                } else {
                    $error = "Gagal membuat detail pesanan: " . mysqli_error($conn);
                }
            } else {
                $error = "Gagal membuat pesanan: " . mysqli_error($conn);
            }
        }
    }
}

// Mengambil semua data pemesanan untuk tabel
$query_pemesanan = "SELECT p.id, p.tanggal, p.status_bayar, p2.nama as anggota_nama, 
                   GROUP_CONCAT(pr.nama SEPARATOR ', ') as produk_nama, 
                   SUM(dp.jumlah) as total_item,
                   SUM(pr.harga * dp.jumlah) as total_harga,
                   p.diskon
                   FROM pesanan p
                   JOIN anggota a ON p.anggota_id = a.id
                   JOIN pegawai p2 ON a.pegawai_id = p2.id
                   JOIN detail_pesanan dp ON p.id = dp.pesanan_id
                   JOIN produk pr ON dp.produk_id = pr.id
                   GROUP BY p.id
                   ORDER BY p.tanggal DESC";
$result_pemesanan = mysqli_query($conn, $query_pemesanan);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pemesanan - Koperasi Pegawai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 20px;
            padding-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4 text-center">Manajemen Pemesanan Koperasi</h1>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <!-- Form Pemesanan Baru -->
        <div class="card mb-5">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Form Pemesanan Baru</h4>
            </div>
            <div class="card-body">
                <form method="post" action="">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="anggota_id" class="form-label">Pilih Anggota:</label>
                            <select class="form-select" id="anggota_id" name="anggota_id" required>
                                <option value="">-- Pilih Anggota --</option>
                                <?php while ($anggota = mysqli_fetch_assoc($result_anggota)): ?>
                                    <option value="<?php echo $anggota['id']; ?>">
                                        <?php echo $anggota['id'] . " - " . $anggota['nama']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="produk_id" class="form-label">Pilih Produk:</label>
                            <select class="form-select" id="produk_id" name="produk_id" required>
                                <option value="">-- Pilih Produk --</option>
                                <?php foreach ($all_produk as $produk): ?>
                                    <?php if ($produk['stok'] > 0): ?>
                                        <option value="<?php echo $produk['id']; ?>" data-stok="<?php echo $produk['stok']; ?>">
                                            <?php echo $produk['nama'] . " - Rp " . number_format($produk['harga'], 0, ',', '.') . " (Stok: " . $produk['stok'] . ")"; ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah:</label>
                        <input type="number" class="form-control" id="jumlah" name="jumlah" min="1" required>
                        <small class="text-muted" id="stok-info"></small>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary" name="submit_pemesanan">Buat Pemesanan</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Daftar Pemesanan -->
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h4 class="mb-0">Daftar Pemesanan</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tanggal</th>
                                <th>Anggota</th>
                                <th>Produk</th>
                                <th>Jumlah Item</th>
                                <th>Total Harga</th>
                                <th>Diskon</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (mysqli_num_rows($result_pemesanan) > 0):
                                while ($pesanan = mysqli_fetch_assoc($result_pemesanan)): 
                                    // Hitung total setelah diskon
                                    $harga_sebelum_diskon = $pesanan['total_harga'];
                                    $nilai_diskon = ($harga_sebelum_diskon * $pesanan['diskon']) / 100;
                                    $harga_setelah_diskon = $harga_sebelum_diskon - $nilai_diskon;
                            ?>
                                <tr>
                                    <td><?php echo $pesanan['id']; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($pesanan['tanggal'])); ?></td>
                                    <td><?php echo $pesanan['anggota_nama']; ?></td>
                                    <td><?php echo $pesanan['produk_nama']; ?></td>
                                    <td><?php echo $pesanan['total_item']; ?></td>
                                    <td>
                                        <?php if ($pesanan['diskon'] > 0): ?>
                                            <del class="text-muted">Rp <?php echo number_format($harga_sebelum_diskon, 0, ',', '.'); ?></del><br>
                                            <strong>Rp <?php echo number_format($harga_setelah_diskon, 0, ',', '.'); ?></strong>
                                        <?php else: ?>
                                            Rp <?php echo number_format($harga_sebelum_diskon, 0, ',', '.'); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($pesanan['diskon'] > 0): ?>
                                            <?php echo $pesanan['diskon']; ?>%<br>
                                            <small>(Rp <?php echo number_format($nilai_diskon, 0, ',', '.'); ?>)</small>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($pesanan['status_bayar'] == 1): ?>
                                            <span class="badge bg-success">Dibayar</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Belum Dibayar</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="?hapus=<?php echo $pesanan['id']; ?>" class="btn btn-sm         btn-danger"onclick="return confirm('Apakah Anda yakin ingin menghapus pesanan ini?')"><i class="fas fa-trash"></i> Hapus
                                        </a>

                                        <?php if ($pesanan['status_bayar'] == 0): ?>
                                            <a href="../views/transaksi.php?php echo $pesanan['id']; ?>" class="btn btn-sm btn-primary">Bayar</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php 
                                endwhile; 
                            else:
                            ?>
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada data pemesanan</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="../views/dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validasi jumlah berdasarkan stok
        document.getElementById('produk_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const stok = selectedOption.dataset.stok;
            const jumlahInput = document.getElementById('jumlah');
            
            jumlahInput.max = stok;
            document.getElementById('stok-info').textContent = `Stok tersedia: ${stok}`;
        });
    </script>
</body>
</html>