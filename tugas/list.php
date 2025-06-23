<?php
session_start();
include "../koneksi.php";
include "../template.php";

if (!isset($_SESSION['role'])) {
    header("Location: " . base_url('login.php'));
    exit;
}

$role = $_SESSION['role'];
$user_id = $_SESSION['id']; 
$user = $_SESSION['nama_lengkap'];

$queryTugas = "";

if ($role === 'dosen') {
    // Dosen only sees tasks they created
    $queryTugas = mysqli_query($koneksi, "SELECT t.*, mk.nama_matkul 
                                           FROM tugas t 
                                           JOIN mata_kuliah mk ON t.id_matkul = mk.id_matkul
                                           WHERE t.id_dosen = '$user_id' ORDER BY t.due_date ASC");
} elseif ($role === 'mahasiswa') {
    // Mahasiswa sees tasks for courses they are enrolled in
    // This requires a table linking students to courses (e.g., krs, enrollment)
    // For simplicity, let's assume all tasks are visible for now, or adapt based on your enrollment logic
    // A more robust query would join with a student_course table:
    /*
    $queryTugas = mysqli_query($koneksi, "SELECT t.*, mk.nama_matkul
                                           FROM tugas t
                                           JOIN mata_kuliah mk ON t.id_matkul = mk.id_matkul
                                           JOIN student_courses sc ON mk.id_matkul = sc.id_matkul
                                           WHERE sc.id_mahasiswa = '$user_id' ORDER BY t.due_date ASC");
    */
    // For a simpler example, let's just show all tasks for now:
    $queryTugas = mysqli_query($koneksi, "SELECT t.*, mk.nama_matkul 
                                           FROM tugas t 
                                           JOIN mata_kuliah mk ON t.id_matkul = mk.id_matkul 
                                           ORDER BY t.due_date ASC");

} elseif ($role === 'admin') {
    
    $queryTugas = mysqli_query($koneksi, "SELECT t.*, mk.nama_matkul, u.nama_lengkap AS nama_dosen
                                           FROM tugas t 
                                           JOIN mata_kuliah mk ON t.matkul_id = mk.id
                                           JOIN users u ON t.dosen_id = u.id 
                                           ORDER BY t.deadline ASC");
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Daftar Tugas</title>
  <link rel="stylesheet" href="<?= base_url('bootstrap/css/bootstrap.min.css') ?>">
  <style>
  
    

    .card {
      border: none !important;
      background-color: var(--primary-color) !important;
      border-radius: 25px 25px 150px 25px !important; /* Adjusted for a unique look */
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      transition: transform 0.2s ease-in-out;
      position: relative; /* Needed for absolute positioning of badge */
    }
    .card:hover {
        transform: translateY(-5px);
    }
    .card-title {
      color: var(--text-color) !important;
      font-weight: bold;
    }
    .card-subtitle {
      color: var(--secondary) !important;
    }
    .card-text {
      color: var(--secondary) !important;
    }
    .lihat, .action-btn {
      background-color: var(--text-color) !important;
      color: var(--primary-color) !important;
      font-weight: 900 !important;
      border: none;
      padding: 8px 20px;
      border-radius: 50px;
      transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
    }
    .lihat:hover, .action-btn:hover {
      background-color: var(--dark-purple-hover) !important;
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
    .due-date-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background-color: var(--light-blue);
        color: var(--dark-blue);
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 0.85em;
        font-weight: bold;
    }
  </style>
</head>
<body>
<?php include "../navbar.php"; ?>

<div class="container mt-4">
  <div class="header-section">
      <h2>Daftar Tugas</h2>
      <p>Lihat semua tugas yang tersedia atau yang Anda berikan.</p>
      <?php if ($role === 'dosen'): ?>
        <a href="<?= base_url('tugas/tambah.php') ?>" class="btn action-btn mt-3">Buat Tugas Baru</a>
      <?php endif; ?>
  </div>

  <div class="row">
    <?php if (mysqli_num_rows($queryTugas) > 0): ?>
      <?php while($tugas = mysqli_fetch_assoc($queryTugas)): ?>
        <div class="col-lg-4 col-md-6 col-sm-12 my-3">
          <div class="card">
            <div class="card-body p-4">
              <span class="due-date-badge">Deadline: <?= date('d M Y', strtotime($tugas['due_date'])); ?></span>
              <h5 class="card-title"><?= htmlspecialchars($tugas['judul_tugas']); ?></h5>
              <h6 class="card-subtitle mb-2">Mata Kuliah: <?= htmlspecialchars($tugas['nama_matkul']); ?></h6>
              <?php if ($role === 'admin'): ?>
                <p class="card-text">Dosen: <?= htmlspecialchars($tugas['nama_dosen']); ?></p>
              <?php endif; ?>
              <p class="card-text">Deskripsi: <?= substr(htmlspecialchars($tugas['deskripsi']), 0, 100); ?>...</p>
              <a href="<?= base_url('tugas/detail.php?id=' . $tugas['id_tugas']) ?>" class="btn lihat mt-3">Lihat Detail</a>
              <?php if ($role === 'dosen' || $role === 'admin'): ?>
                <a href="<?= base_url('tugas/edit.php?id=' . $tugas['id_tugas']) ?>" class="btn action-btn mt-3 ms-2">Edit</a>
                <a href="<?= base_url('tugas/hapus.php?id=' . $tugas['id_tugas']) ?>" class="btn btn-danger mt-3" onclick="return confirm('Yakin ingin menghapus tugas ini?');">Hapus</a>
              <?php endif; ?>
              <?php if ($role === 'mahasiswa'): ?>
                <a href="<?= base_url('pengumpulan/upload.php?id_tugas=' . $tugas['id_tugas']) ?>" class="btn action-btn mt-3 ms-2">Kumpulkan</a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="col-12">
        <div class="alert alert-info text-center" role="alert">
          Belum ada tugas yang tersedia.
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<script src="<?= base_url('bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>