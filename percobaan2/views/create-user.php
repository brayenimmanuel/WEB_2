<?php 
require_once __DIR__ . '/../models/pegawai.php';

use models\Pegawai;

if(isset($_POST['submit'])){
    $data = [
        "nip" => $_POST['nip'],
        "nama" => $_POST['nama'],
        "jenis_kelamin" => $_POST['gender'],
        "jabatan" => $_POST['jabatan'],
    ];

    Pegawai::create($data);
    header("Location: list-user.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Praktikum 06</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="../public/css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">
        <!-- Navbar-->
        <?php include_once './partials/navbar.php' ?>

        <!-- Sidebar -->
        <div id="layoutSidenav">
        <?php include_once './partials/sidebar.php' ?>

            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                    <h1 class="mt-4">Add User</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="list-user.php">Tambah Pegawai</a></li>
                            <li class="breadcrumb-item"><a href="create-user.php"></a>Add User</li>
                        </ol>

                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                Add User
                            </div>
                            <div class="card-body">
                                <form action="create-user.php" method="POST">
                                    <div class="mb-3">
                                        <label for="firstname" class="form-label">NIP</label>
                                        <input type="text" class="form-control" id="nip" name="nip" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="lastname" class="form-label">Nama</label>
                                        <input type="text" class="form-control" id="nama" name="nama" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label d-block">Jenis Kelamin</label>
                                        <div class="form-check form-check-inline">
                                            <input type="radio" class="form-check-input" name="gender" id="laki-laki" value="Laki-laki">
                                            <label for="laki-laki" class="form-check-label">Laki-laki</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input type="radio" class="form-check-input" name="gender" id="perempuan" value="Perempuan">
                                            <label for="perempuan" class="form-check-label">Perempuan</label>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="age" class="form-label">Jabatan</label>
                                        <input type="text" class="form-control" id="jabatan" name="jabatan" min="0" max="100" required>
                                    </div>

                                    <!-- <div class="mb-3">
                                        <label for="weight" class="form-label">Weight</label>
                                        <input type="number" class="form-control" id="weight" name="weight" min="0" max="100" required>
                                    </div> -->

                                    <a href="list-user.php" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Back
                                    </a>
                                    <button type="submit" name="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </main>

            <!-- Footer -->
            <?php include_once './partials/footer.php' ?>

            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../public/js/scripts.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="../public/js/datatables-simple-demo.js"></script>
    </body>
</html>
