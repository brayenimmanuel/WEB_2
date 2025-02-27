<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Total Belanja</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <?php
        if (isset($_POST['submit'])) {
            $nama_customer = $_POST['nama_customer'];
            $nama_produk = $_POST['nama_produk'];
            $jumlah_produk = $_POST['jumlah_produk'];

            $harga = 0;
            switch ($nama_produk) {
                case 'tv':
                    $harga = 4200000;
                    break;
                case 'kulkas':
                    $harga = 3100000;
                    break;
                case 'mesin_cuci':
                    $harga = 3800000;
                    break;
            }

            $total = $harga * $jumlah_produk;

            echo '<div class="alert alert-success">';
            echo '<h4>Hasil Belanja</h4>';
            echo '<hr>';
            echo '<p>Nama Customer : ' . htmlspecialchars($nama_customer) . '</p>';
            echo '<p>Produk Pilihan : ' . htmlspecialchars($nama_produk) . '</p>';
            echo '<p>Jumlah Beli : ' . htmlspecialchars($jumlah_produk) . '</p>';
            echo '<p>Total Belanja : Rp ' . number_format($total, 0, ',', '.') . ',-</p>';
            echo '</div>';
        } else {
            echo '<div class="alert alert-danger">Formulir belum dikirim.</div>';
        }
        ?>
        <a href="form_belanja.php" class="btn btn-primary mt-3">Kembali ke Form</a>
    </div>
</body>
</html>