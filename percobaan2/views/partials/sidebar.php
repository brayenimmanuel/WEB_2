<?php
/**
 * Sidebar Navigation Component
 * File: components/sidebar.php
 */
?>
<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <div class="sb-sidenav-menu-heading">MENU</div>
                <a class="nav-link" href="dashboard.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Dashboard
                </a>
                
                <!-- Manajemen Anggota -->
                <a class="nav-link collapsed" href="anggota.php" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                    <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>Manajemen Anggota
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                </a>
                <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link" href="../views/data-anggota.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                            Data Anggota
                        </a>
                        <a class="nav-link" href="../views/data-pegawai.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-user-tie"></i></div>
                            Data Pegawai
                        </a>
                        <a class="nav-link" href="../views/diskon/kartu-diskon.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-id-card"></i></div>
                            Kartu Diskon
                        </a>
                    </nav>
                </div>
                
                <!-- Manajemen Produk -->
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#manajemenProduk" aria-expanded="false" aria-controls="collapseLayouts">
                    <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                            Manajemen Produk
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="manajemenProduk" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link" href="../views/data-produk.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-box"></i></div>
                            Data Produk
                        </a>
                        <a class="nav-link" href="../views/jenis-produk.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tags"></i></div>
                            Jenis Produk
                        </a>
                    </nav>
                </div>
                
                <!-- Pemesanan -->
                <a class="nav-link" href="../views/pemesanan.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-shopping-cart"></i></div>
                    Pemesanan
                </a>
                
                <!-- Transaksi -->
                <a class="nav-link" href="../views/transaksi.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-money-bill"></i></div>
                    Transaksi
                </a>
            </div>
        </div>
        <div class="sb-sidenav-footer">
            <div class="small">Logged in as:</div>
            Hafidh Alim
        </div>
    </nav>
</div>