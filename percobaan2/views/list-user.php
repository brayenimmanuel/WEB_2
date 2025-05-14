<?php
require_once __DIR__ . '/../models/pegawai.php';

use models\Pegawai;

$users = Pegawai::get();
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
                    <h1 class="mt-4">Daftar Pegawai</h1>

                    <a href="dashboard.php" class="btn btn-secondary mb-3">Kembali ke Dashboard</a>
                    <a href="create-user.php" class="btn btn-primary mb-3">Tambah Pegawai</a>


                    <table id="datatablesSimple">
                        <thead>
                            <th>No.</th>
                            <th>NIP</th>
                            <th>Nama</th>
                            <th>Jenis Kelamin</th>
                            <th>Jabatan</th>
                            <th>Aksi</th>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $index => $user): ?>
                                <tr>
                                    <td><?= $index + 1; ?></td>
                                    <td><?= $user['nip'] ?></td>
                                    <td><?= $user['nama'] ?></td>
                                    <td><?= $user['jenis_kelamin'] ?></td>
                                    <td><?= $user['jabatan'] ?></td>
                                    <td>
                                        <a href="detail-user.php?id=<?= $user['id'] ?>" class="btn btn-primary">
                                            <i class="fas fa-eye"></i>Detail
                                        </a>

                                        <a href="edit-user.php?id=<?= $user['id'] ?>" class="btn btn-warning">
                                            <i class="fas fa-edit"></i>Edit
                                        </a>

                                        <a href="delete-user.php?id=<?= $user['id'] ?>" class="btn btn-danger">
                                            <i class="fas fa-trash"></i>Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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