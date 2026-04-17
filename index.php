<?php
include_once('koneksi.php');

if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 'dashboard';
}

$idadmin = 1; // Simulasi ID Admin, nanti bisa diganti dengan session login
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMASI Pro - Dashboard</title>

    <link href="<?= $base_url ?>/vendor/bootstrap/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="<?= $base_url ?>/vendor/fontawesome/css/all.min.css">

    <link rel="stylesheet" href="<?= $base_url ?>/vendor/datatables/dataTables.bootstrap5.min.css">

    <!-- Select2 -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/vendor/select2/select2.min.css" />

    <link rel="stylesheet" href="<?= $base_url ?>/vendor/jquery-ui/jquery-ui.css">

    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.css">

    <script src="<?= $base_url ?>/vendor/jquery/jquery-3.7.0.js"></script>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top px-2">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-industry me-2 text-info"></i> SIMASI Pro
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?= $page == 'dashboard' ? 'active' : '' ?>" href="<?= $base_url ?>/dashboard">
                            <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?= $page == 'masterplan' ? 'active' : '' ?>" href="<?= $base_url ?>/masterplan">
                            <i class="fas fa-flask me-1"></i> Master Plan
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="prodDrop" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-industry me-1"></i> Produksi
                        </a>
                        <ul class="dropdown-menu border-top-primary shadow">
                            <li class="dropdown-submenu">
                                <a class="dropdown-item" href="#">
                                    <div class="d-flex align-items-center">
                                        <!-- <i class="fas fa-cogs me-2 text-muted"></i> -->
                                        <span>Master Proses</span>
                                    </div>
                                    <i class="fas fa-chevron-right ms-3" style="font-size: 10px;"></i>
                                </a>
                                <ul class="dropdown-menu shadow">
                                    <li><a class="dropdown-item" href="<?= $base_url ?>/produksi/masterproses/cutting">Cutting</a></li>
                                    <li><a class="dropdown-item" href="<?= $base_url ?>/produksi/masterproses/edging">Edging</a></li>
                                    <li><a class="dropdown-item" href="<?= $base_url ?>/produksi/masterproses/bor">Bor</a></li>
                                </ul>
                            </li>

                            <li class="dropdown-submenu">
                                <a class="dropdown-item" href="#">
                                    <div class="d-flex align-items-center">
                                        <!-- <i class="fas fa-check-double me-2 text-muted"></i> -->
                                        <span>Realisasi</span>
                                    </div>
                                    <i class="fas fa-chevron-right ms-3" style="font-size: 10px;"></i>
                                </a>
                                <ul class="dropdown-menu shadow">
                                    <li><a class="dropdown-item" href="<?= $base_url ?>/produksi/realisasi/cutting">Cutting</a></li>
                                    <li><a class="dropdown-item" href="<?= $base_url ?>/produksi/realisasi/edging">Edging</a></li>
                                    <li><a class="dropdown-item" href="<?= $base_url ?>/produksi/realisasi/bor">Bor</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="reportDrop" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-file-invoice me-1"></i> Laporan
                        </a>
                        <ul class="dropdown-menu border-top-primary shadow">
                            <li><a class="dropdown-item" href="#">Laporan Harian</a></li>
                            <li><a class="dropdown-item" href="#">Laporan Efisiensi</a></li>
                            <li><a class="dropdown-item" href="#">Laporan Reject</a></li>
                        </ul>
                    </li>
                </ul>

                <div class="dropdown">
                    <a class="nav-link dropdown-toggle text-white d-flex align-items-center p-0" href="#" role="button" data-bs-toggle="dropdown">
                        <span class="me-2 fw-bold">Admin</span>
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold" style="width: 35px; height: 35px; font-size: 14px;">
                            AD
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow mt-2 border-top-primary">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profil</a></li>
                        <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-sign-out-alt me-2"></i> Keluar</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <nav aria-label="breadcrumb" class="fixed-breadcrumb bg-light border-bottom">
        <div class="container-fluid py-2">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home me-1"></i>Home</a></li>
                <li class="breadcrumb-item"><a href="#">Produksi</a></li>
                <li class="breadcrumb-item active text-capitalize"><?= str_replace('-', ' ', $page); ?></li>
            </ol>
        </div>
    </nav>

    <?php
    switch ($page) {
        case 'dashboard':
            include 'pages/dashboard/index.php';
            break;

        case 'masterplan':
            include 'pages/masterplan/index.php';
            break;

        case 'produksi-masterproses-cutting':
            include 'pages/produksi/master_proses/cutting/index.php';
            break;
        case 'produksi-masterproses-cutting-crud':
            include 'pages/produksi/master_proses/cutting/crud.php';
            break;

         case 'produksi-masterproses-edging':
            include 'pages/produksi/master_proses/edging/index.php';
            break;
        case 'produksi-masterproses-edging-crud':
            include 'pages/produksi/master_proses/edging/crud.php';
            break;

        case 'produksi-realisasi-cutting':
            include 'pages/produksi/realisasi/cutting/index.php';
            break;
        case 'produksi-realisasi-cutting-crud':
            include 'pages/produksi/realisasi/cutting/crud.php';
            break;

        default:
            include 'pages/dashboard/index.php';
            break;
    }
    ?>

    <script src="<?= $base_url ?>/vendor/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="<?= $base_url ?>/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="<?= $base_url ?>/vendor/datatables/dataTables.bootstrap5.min.js"></script>

    <script src="<?php echo $base_url; ?>/vendor/select2/select2.min.js"></script>

    <script src="<?= $base_url ?>/vendor/jquery-ui/jquery-ui.min.js"></script>

    <script>
        function showToast(message, type = 'success') {
            // Buat container jika belum ada
            if ($('#toast-container').length === 0) {
                $('body').append('<div id="toast-container"></div>');
            }

            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            const toastClass = type === 'success' ? 'toast-success' : 'toast-error';

            const id = Date.now(); // ID unik untuk setiap toast
            const toastHTML = `
        <div id="toast-${id}" class="custom-toast ${toastClass}">
            <i class="fas ${icon}"></i>
            <span>${message}</span>
        </div>
    `;

            $('#toast-container').append(toastHTML);

            // Hapus element dari DOM setelah animasi selesai (4 detik)
            setTimeout(() => {
                $(`#toast-${id}`).remove();
            }, 1500);
        }
    </script>

</body>

</html>