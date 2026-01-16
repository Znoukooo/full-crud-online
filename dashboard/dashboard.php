<?php
session_start();
include "../koneksi.php";
include "../template.php";

if (!isset($_SESSION['role'])) {
    header("Location: " . base_url('login.php'));
    exit;
}

$role = $_SESSION['role'];
$user = $_SESSION['nama_lengkap'];
$id = $_SESSION['id'];

$totalMatkul = 0;
$totalTugas = 0;
$totalUsers = 0;
$totalTugasSaya = 0; 
$totalTugasTersedia = 0; 
$totalPengumpulanSaya = 0; 
$totalPengumpulan = 0; 

if ($role === 'admin') {
    $queryMatkulCount = mysqli_query($koneksi, "SELECT COUNT(*) AS total_matkul FROM mata_kuliah");
    $dataMatkulCount = mysqli_fetch_assoc($queryMatkulCount);
    $totalMatkul = $dataMatkulCount['total_matkul'];

    $queryTugasCount = mysqli_query($koneksi, "SELECT COUNT(*) AS total_tugas FROM tugas");
    $dataTugasCount = mysqli_fetch_assoc($queryTugasCount);
    $totalTugas = $dataTugasCount['total_tugas'];

    $queryUserCount = mysqli_query($koneksi, "SELECT COUNT(*) AS total_users FROM users"); 
    $dataUserCount = mysqli_fetch_assoc($queryUserCount);
    $totalUsers = $dataUserCount['total_users'];

} elseif ($role === 'dosen') {
    $queryTugasSayaCount = mysqli_query($koneksi, "SELECT COUNT(*) AS total_tugas_saya FROM tugas WHERE dosen_id = '".$id."'");
    $dataTugasSayaCount = mysqli_fetch_assoc($queryTugasSayaCount);
    $totalTugasSaya = $dataTugasSayaCount['total_tugas_saya'];

    // Total pengumpulan untuk tugas-tugas yang dibuat oleh dosen ini
    $queryPengumpulanDosenCount = mysqli_query($koneksi, "
        SELECT COUNT(p.id) AS total_pengumpulan 
        FROM pengumpulan p
        JOIN tugas t ON p.tugas_id = t.id
        WHERE t.dosen_id = '$id'
    ");
    $dataPengumpulanDosenCount = mysqli_fetch_assoc($queryPengumpulanDosenCount);
    $totalPengumpulan = $dataPengumpulanDosenCount['total_pengumpulan'];

} elseif ($role === 'mahasiswa') {
    // Total tugas yang tersedia untuk mahasiswa (semua tugas di sistem, karena tidak ada KRS)
    $queryTugasTersediaCount = mysqli_query($koneksi, "SELECT COUNT(*) AS total_tugas_tersedia FROM tugas");
    $dataTugasTersediaCount = mysqli_fetch_assoc($queryTugasTersediaCount);
    $totalTugasTersedia = $dataTugasTersediaCount['total_tugas_tersedia'];

    // Total pengumpulan yang telah dilakukan oleh mahasiswa ini
    $queryPengumpulanSayaCount = mysqli_query($koneksi, "SELECT COUNT(*) AS total_pengumpulan_saya FROM pengumpulan WHERE mahasiswa_id = '".$id."'");
    $dataPengumpulanSayaCount = mysqli_fetch_assoc($queryPengumpulanSayaCount);
    $totalPengumpulanSaya = $dataPengumpulanSayaCount['total_pengumpulan_saya'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard</title>
    <link rel="stylesheet" href="<?= base_url('bootstrap/css/bootstrap.min.css') ?>">

    <style>
        :root {
            --primary-color: #6a5acd;
            --text-color: #ffffff;
            --secondary: #e0e0e0;
            --dark-purple-hover: #403385;
        }
        .card{
            border: none !important;
            background-color: var(--primary-color) !important;
            border-radius: 25px 150px 150px 25px !important;
        }
        .card-title{
            color: var(--text-color) !important;
        }
        .card-subtitle{
            color: var(--secondary) !important;
        }
        .lihat{
            background-color: var(--text-color) !important;
            color: var(--primary-color) !important;
            font-weight: 900 !important;
        }
        .lihat:hover{
            background-color:var(--dark-purple-hover) !important;
            color: white !important;
        }
    </style>
</head>
<body>
<?php include "../navbar.php"; ?>

<div class="container mt-4">
    <h2 class="title">Selamat datang, <?= ucfirst($user); ?>!</h2>
    <div class="list-group mt-3">
        <?php if ($role === 'admin'): ?>
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-4 col-md-12 my-3">
                        <div class="card" >
                            <div class="card-body p-4">
                                <h5 class="card-title">Kelola Mata Kuliah</h5>
                                <h6 class="card-subtitle mb-2 ">Total: <?= $totalMatkul; ?> mata kuliah</h6>
                                <a href="<?= base_url('mata_kuliah/list.php') ?>" class="btn lihat px-4 mt-4">Lihat</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12 my-3">
                        <div class="card" >
                            <div class="card-body p-4">
                                <h5 class="card-title">Lihat Semua Tugas</h5>
                                <h6 class="card-subtitle mb-2 ">Total: <?= $totalTugas; ?> tugas</h6>
                                <a href="<?= base_url('tugas/list.php') ?>" class="btn lihat px-4 mt-4">Lihat</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12 my-3">
                        <div class="card" >
                            <div class="card-body p-4">
                                <h5 class="card-title">Kelola Pengguna</h5>
                                <h6 class="card-subtitle mb-2 ">Total: <?= $totalUsers; ?> pengguna</h6>
                                <a href="<?= base_url('admin/kelola_user.php') ?>" class="btn lihat px-4 mt-4">Lihat</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif ($role === 'dosen'): ?>
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-4 col-md-12 my-3">
                        <div class="card" >
                            <div class="card-body p-4">
                                <h5 class="card-title">Buat Tugas Baru</h5>
                                <h6 class="card-subtitle mb-2 ">Total: <?= $totalTugasSaya; ?> tugas</h6>
                                <a href="<?= base_url('tugas/tambah.php?from=dashboard') ?>" class="btn lihat px-4 mt-4">Lihat</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12 my-3">
                        <div class="card" >
                            <div class="card-body p-4">
                                <h5 class="card-title">Daftar Tugas Saya</h5>
                                <h6 class="card-subtitle mb-2 ">Total: <?= $totalTugasSaya; ?> tugas</h6>
                                <a href="<?= base_url('tugas/list.php') ?>" class="btn lihat px-4 mt-4">Lihat</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12 my-3">
                        <div class="card" >
                            <div class="card-body p-4">
                                <h5 class="card-title">Nilai Tugas Mahasiswa</h5>
                                <h6 class="card-subtitle mb-2 ">Total: <?= $totalPengumpulan; ?> Pengumpulan</h6>
                                <a href="<?= base_url('nilai/dosen_nilai_tugas.php') ?>" class="btn lihat px-4 mt-4">Lihat</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif ($role === 'mahasiswa'): ?>
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-4 col-md-12 my-3">
                        <div class="card" >
                            <div class="card-body p-4">
                                <h5 class="card-title">Lihat Tugas Tersedia</h5>
                                <h6 class="card-subtitle mb-2 ">Total: <?= $totalTugasTersedia; ?> tugas</h6>
                                <a href="<?= base_url('tugas_mahasiswa/lihat_tugas.php') ?>" class="btn lihat px-4 mt-4">Lihat</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12 my-3">
                        <div class="card" >
                            <div class="card-body p-4">
                                <h5 class="card-title">Riwayat Pengumpulan</h5>
                                <h6 class="card-subtitle mb-2 ">Total: <?= $totalPengumpulanSaya; ?> pengumpulan</h6>
                                <a href="<?= base_url('tugas_mahasiswa/riwayat_nilai.php') ?>" class="btn lihat px-4 mt-4">Lihat</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12 my-3">
                        <div class="card" >
                            <div class="card-body p-4">
                                <h5 class="card-title">Tugas Belum Dikumpulkan</h5>
                                <?php
                               
                                $queryBelumKumpulCount = mysqli_query($koneksi, "
                                    SELECT COUNT(t.id) AS total_belum_kumpul
                                    FROM tugas t
                                    LEFT JOIN pengumpulan p ON t.id = p.tugas_id AND p.mahasiswa_id = '$id'
                                    WHERE p.id IS NULL
                                ");
                                $dataBelumKumpulCount = mysqli_fetch_assoc($queryBelumKumpulCount);
                                $totalBelumKumpul = $dataBelumKumpulCount['total_belum_kumpul'];
                                ?>
                                <h6 class="card-subtitle mb-2 ">Total: <?= $totalBelumKumpul; ?> tugas</h6>
                                <a href="<?= base_url('tugas_mahasiswa/lihat_tugas.php?status=belum_kumpul') ?>" class="btn lihat px-4 mt-4">Lihat</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="<?= base_url('bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>