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


$queryMatkulCount = mysqli_query($koneksi, "SELECT COUNT(*) AS total_matkul FROM mata_kuliah");
$dataMatkulCount = mysqli_fetch_assoc($queryMatkulCount);
$totalMatkul = $dataMatkulCount['total_matkul'];

$queryTugasCount = mysqli_query($koneksi, "SELECT COUNT(*) AS total_tugas FROM tugas");
$dataTugasCount = mysqli_fetch_assoc($queryTugasCount);
$totalTugas = $dataTugasCount['total_tugas'];

$queryUserCount = mysqli_query($koneksi, "SELECT COUNT(*) AS total_users FROM users"); 
$dataUserCount = mysqli_fetch_assoc($queryUserCount);
$totalUsers = $dataUserCount['total_users'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard</title>
  <link rel="stylesheet" href="<?= base_url('bootstrap/css/bootstrap.min.css') ?>">

  <style>
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
      background-color:rgb(64, 51, 133) !important;
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
                <a href="<?= base_url('mata_kuliah/list.php') ?>" class="btn lihat  px-4 mt-4">Lihat</a>
              </div>
            </div>
          </div>
          <div class="col-lg-4 col-md-12 my-3">
            <div class="card" >
              <div class="card-body p-4">
                <h5 class="card-title">Lihat Semua Tugas</h5>
                <h6 class="card-subtitle mb-2 ">Total: <?= $totalTugas; ?> tugas</h6>
                <a href="<?= base_url('tugas/list.php') ?>" class="btn lihat  px-4 mt-4">Lihat</a>
              </div>
            </div>
          </div>
          <div class="col-lg-4 col-md-12 my-3">
            <div class="card" >
              <div class="card-body p-4">
                <h5 class="card-title">Kelola Pengguna</h5>
                <h6 class="card-subtitle mb-2 ">Total: <?= $totalUsers; ?> pengguna</h6>
                <a href="<?= base_url('admin/kelola_user.php') ?>" class="btn lihat  px-4 mt-4">Lihat</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php elseif ($role === 'dosen'): ?>
      <a href="<?= base_url('tugas/tambah.php') ?>" class="list-group-item list-group-item-action">Buat Tugas Baru</a>
      <a href="<?= base_url('tugas/list.php') ?>" class="list-group-item list-group-item-action">Daftar Tugas Saya</a>
      <a href="<?= base_url('pengumpulan/nilai.php') ?>" class="list-group-item list-group-item-action">Nilai Tugas Mahasiswa</a>
    <?php elseif ($role === 'mahasiswa'): ?>
      <a href="<?= base_url('tugas/list.php') ?>" class="list-group-item list-group-item-action">Lihat Tugas</a>
      <a href="<?= base_url('pengumpulan/upload.php') ?>" class="list-group-item list-group-item-action">Upload Jawaban</a>
      <a href="<?= base_url('pengumpulan/lihat.php') ?>" class="list-group-item list-group-item-action">Riwayat dan Nilai</a>
    <?php endif; ?>
  </div>
</div>

<script src="<?= base_url('bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>