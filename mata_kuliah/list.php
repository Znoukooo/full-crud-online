<?php
session_start();
include "../koneksi.php";
include "../template.php"; // Assuming this includes base_url() and other essentials

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'dosen')) {
    header("Location: " . base_url('login.php'));
    exit;
}

$role = $_SESSION['role'];
$user = $_SESSION['nama_lengkap'];

// Fetch all mata kuliah data
$queryMatkul = mysqli_query($koneksi, "SELECT * FROM mata_kuliah ORDER BY nama_matkul ASC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Daftar Mata Kuliah</title>
  <link rel="stylesheet" href="<?= base_url('bootstrap/css/bootstrap.min.css') ?>">
  <style>
    /* Global variables (ensure these are defined in your template.php or a global CSS file) */
    :root {
      --primary-color: #6a5acd; /* Example: MediumSlateBlue */
      --text-color: #ffffff; /* Example: White */
      --secondary: #e0e0e0; /* Example: Light Gray */
      --dark-purple-hover: #403385; /* Example: Darker purple for hover */
    }

    .card {
      border: none !important;
      background-color: var(--primary-color) !important;
      border-radius: 25px 100px 25px 25px !important; /* Adjusted for a unique look */
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Add a subtle shadow */
      transition: transform 0.2s ease-in-out; /* Smooth hover effect */
    }
    .card:hover {
        transform: translateY(-5px); /* Lift card on hover */
    }
    .card-title {
      color: var(--text-color) !important;
      font-weight: bold;
    }
    .card-subtitle {
      color: var(--secondary) !important;
    }
    .card-text {
      color: var(--secondary) !important; /* For additional text in the card */
    }
    .lihat, .action-btn {
      background-color: var(--text-color) !important;
      color: var(--primary-color) !important;
      font-weight: 900 !important;
      border: none;
      padding: 8px 20px;
      border-radius: 50px; /* More rounded buttons */
      transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
    }
    .lihat:hover, .action-btn:hover {
      background-color: var(--dark-purple-hover) !important; /* Use a defined darker purple */
      color: white !important;
    }
    .header-section {
        background-color: var(--primary-color);
        color: var(--text-color);
        padding: 20px;
        margin-bottom: 30px;
        border-radius: 15px;
        text-align: center;
    }
    .header-section h2 {
        color: var(--text-color);
    }
  </style>
</head>
<body>
<?php include "../navbar.php"; ?>

<div class="container mt-4">
  <div class="header-section">
      <h2>Daftar Mata Kuliah</h2>
      <p>Kelola semua mata kuliah yang tersedia.</p>
      <?php if ($role === 'admin'): ?>
        <a href="<?= base_url('mata_kuliah/tambah.php') ?>" class="btn action-btn mt-3">Tambah Mata Kuliah Baru</a>
      <?php endif; ?>
  </div>

  <div class="row">
    <?php if (mysqli_num_rows($queryMatkul) > 0): ?>
      <?php while($matkul = mysqli_fetch_assoc($queryMatkul)): ?>
        <div class="col-lg-4 col-md-6 col-sm-12 my-3">
          <div class="card">
            <div class="card-body p-4">
              <h5 class="card-title"><?= htmlspecialchars($matkul['nama_matkul']); ?></h5>
              <h6 class="card-subtitle mb-2">Kode: <?= htmlspecialchars($matkul['kode_matkul']); ?></h6>
              <p class="card-text">SKS: <?= htmlspecialchars($matkul['sks']); ?></p>
              <a href="<?= base_url('mata_kuliah/detail.php?id=' . $matkul['id']) ?>" class="btn lihat mt-3">Lihat Detail</a>
              <?php if ($role === 'admin'): ?>
                <a href="<?= base_url('mata_kuliah/edit.php?id=' . $matkul['id']) ?>" class="btn action-btn mt-3 ms-2">Edit</a>
                <a href="<?= base_url('mata_kuliah/hapus.php?id=' . $matkul['id']) ?>" class="btn btn-danger mt-3" onclick="return confirm('Yakin ingin menghapus mata kuliah ini?');">Hapus</a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="col-12">
        <div class="alert alert-info text-center" role="alert">
          Belum ada mata kuliah yang terdaftar.
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<script src="<?= base_url('bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>