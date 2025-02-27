<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Belanja</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8">
                <form method="POST" action="total_belanja.php" id="belanjaForm">
                    <fieldset class="border border-dark p-3 rounded" style="background-color: lightyellow;">
                        <legend class="float-none w-auto px-3 fw-bold h3">Form Belanja</legend>
                        <div class="form-group row">
                            <label for="nama_customer" class="col-sm-4 col-form-label">Nama Customer</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fa fa-address-card"></i>
                                        </div>
                                    </div>
                                    <input id="nama_customer" name="nama_customer" placeholder="Nama Anda" type="text" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4">Nama Produk</label>
                            <div class="col-sm-8">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input name="nama_produk" id="nama_produk_0" type="radio" class="custom-control-input" value="tv" required>
                                    <label for="nama_produk_0" class="custom-control-label">TV</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input name="nama_produk" id="nama_produk_1" type="radio" class="custom-control-input" value="kulkas" required>
                                    <label for="nama_produk_1" class="custom-control-label">Kulkas</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input name="nama_produk" id="nama_produk_2" type="radio" class="custom-control-input" value="mesin_cuci" required>
                                    <label for="nama_produk_2" class="custom-control-label">Mesin Cuci</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="jumlah_produk" class="col-sm-4 col-form-label">Jumlah Produk</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fa fa-calculator"></i>
                                        </div>
                                    </div>
                                    <input id="jumlah_produk" name="jumlah_produk" placeholder="Jumlah" type="number" class="form-control" required min="1">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-8 offset-sm-4">
                                <button name="submit" type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        Daftar Harga
                    </div>
                    <div class="card-body">
                        <p>TV : Rp 4.200.000</p>
                        <p>Kulkas : Rp 3.100.000</p>
                        <p>Mesin Cuci : Rp 3.800.000</p>
                    </div>
                    <div class="card-footer bg-primary text-white">
                        Harga Dapat Berubah Setiap Saat
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>