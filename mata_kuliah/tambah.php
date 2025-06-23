<?php
session_start();
include "../koneksi.php";
include "../template.php"; // Assuming this includes base_url() and other essentials

// Restrict access to admin role only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . base_url('login.php'));
    exit;
}

$role = $_SESSION['role'];
$user = $_SESSION['nama_lengkap'];

$message = ''; // To store success or error messages

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_matkul = $_POST['kode_matkul'];
    $nama_matkul = $_POST['nama_matkul'];
    $sks = $_POST['sks'];

    // Basic validation
    if (empty($kode_matkul) || empty($nama_matkul) || empty($sks)) {
        $message = '<div class="alert alert-danger" role="alert">Semua kolom harus diisi!</div>';
    } elseif (!is_numeric($sks) || $sks <= 0) {
        $message = '<div class="alert alert-danger" role="alert">SKS harus berupa angka positif!</div>';
    } else {
        // Prepare and execute the insert query
        $stmt = mysqli_prepare($koneksi, "INSERT INTO mata_kuliah (kode_matkul, nama_matkul, sks) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssi", $kode_matkul, $nama_matkul, $sks);

        if (mysqli_stmt_execute($stmt)) {
            $message = '<div class="alert alert-success" role="alert">Mata kuliah berhasil ditambahkan!</div>';
            // Optional: Redirect to mata_kuliah/list.php after successful addition
            // header("Location: " . base_url('mata_kuliah/list.php?status=success'));
            // exit;
        } else {
            $message = '<div class="alert alert-danger" role="alert">Error: ' . mysqli_error($koneksi) . '</div>';
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tambah Mata Kuliah</title>
  <link rel="stylesheet" href="<?= base_url('bootstrap/css/bootstrap.min.css') ?>">
  <style>
    /* Global variables (ensure these are defined in your template.php or a global CSS file) */
    :root {
      --primary-color: #6a5acd; /* Example: MediumSlateBlue */
      --text-color: #ffffff; /* Example: White */
      --secondary: #e0e0e0; /* Example: Light Gray */
      --dark-purple-hover: #403385; /* Example: Darker purple for hover */
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

    .form-container {
        background-color: #f8f9fa; /* Light background for the form area */
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .form-group label {
        font-weight: bold;
        color: #343a40; /* Darker text for labels */
        margin-bottom: 8px;
    }

    .form-control {
        border-radius: 8px;
        border: 1px solid #ced4da;
        padding: 10px 15px;
    }
    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(106, 90, 205, 0.25); /* Bootstrap-like focus effect */
    }

    .action-btn {
      background-color: var(--primary-color) !important; /* Primary color for submit button */
      color: var(--text-color) !important;
      font-weight: 900 !important;
      border: none;
      padding: 10px 25px;
      border-radius: 50px;
      transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
    }
    .action-btn:hover {
      background-color: var(--dark-purple-hover) !important;
      color: white !important;
    }
    .back-btn {
        background-color: #6c757d !important; /* Bootstrap secondary color for back button */
        color: white !important;
        font-weight: 900 !important;
        border: none;
        padding: 10px 25px;
        border-radius: 50px;
        transition: background-color 0.2s ease-in-out;
    }
    .back-btn:hover {
        background-color: #5a6268 !important;
    }
  </style>
</head>
<body>
<?php include "../navbar.php"; ?>

<div class="container mt-4">
  <div class="header-section">
      <h2>Tambah Mata Kuliah Baru</h2>
      <p>Isi formulir di bawah ini untuk menambahkan mata kuliah.</p>
  </div>

  <div class="form-container">
    <?= $message; // Display success or error message ?>
    <form action="" method="POST">
      <div class="mb-3">
        <label for="kode_matkul" class="form-label">Kode Mata Kuliah:</label>
        <input type="text" class="form-control" id="kode_matkul" name="kode_matkul" required>
      </div>
      <div class="mb-3">
        <label for="nama_matkul" class="form-label">Nama Mata Kuliah:</label>
        <input type="text" class="form-control" id="nama_matkul" name="nama_matkul" required>
      </div>
      <div class="mb-3">
        <label for="sks" class="form-label">Jumlah SKS:</label>
        <input type="number" class="form-control" id="sks" name="sks" required min="1">
      </div>
      <div class="d-flex justify-content-between align-items-center mt-4">
        <button type="submit" class="btn action-btn">Tambah Mata Kuliah</button>
        <a href="<?= base_url('mata_kuliah/list.php') ?>" class="btn back-btn">Kembali ke Daftar Mata Kuliah</a>
      </div>
    </form>
  </div>
</div>

<script src="<?= base_url('bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>