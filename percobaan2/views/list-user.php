<?php
require_once __DIR__ . '/../models/user.php';

use models\User;

$users = User::get();
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
        <?php include_once './partials/navbar.php' ?>
        <div id="layoutSidenav">
            <?php include_once './partials/sidebar.php' ?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">User</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">User</li>
                        </ol>
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                List User
                            </div>
                            <div class="card-body">
                                <div class="mb-3 text-end">
                                    <a href="create-user.php" class="btn btn-success">
                                        <i class="fas fa-plus"></i>Add User
                                    </a>
                                </div>
                                <table id="datatablesSimple">
                                    <thead>
                                        <th>No.</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Gender</th>
                                        <th>Action</th>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $index =>$user):?>
                                            <tr>
                                                <td><?= $index + 1;?></td>
                                                <td><?= $user['firstname']?></td>
                                                <td><?= $user['lastname']?></td>
                                                <td><?= $user['gender']?></td>
                                                <td>
                                                    <a href="detail-user.php?id=<?= $user['id']?>" class="btn btn-primary">
                                                        <i class="fas fa-eye"></i>Detail
                                                    </a>

                                                    <a href="edit-user.php?id=<?= $user['id']?>" class="btn btn-warning">
                                                        <i class="fas fa-edit"></i>Edit
                                                    </a>

                                                    <a href="delete-user.php?id=<?= $user['id']?>" class="btn btn-danger">
                                                        <i class="fas fa-trash"></i>Delete
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach;?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </main>
                <?php include_once './partials/footer.php'; ?>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../public/js/scripts.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="../public/js/datatables-simple-demo.js"></script>
    </body>
</html>
