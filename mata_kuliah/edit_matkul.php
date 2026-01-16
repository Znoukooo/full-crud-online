<?php
session_start();
include "../koneksi.php";
include "../template.php";


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . base_url('login.php'));
    exit;
}

$role = $_SESSION['role'];
$user = $_SESSION['nama_lengkap'];
$id = $_GET['id'];
$message = '';
$data = [];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_matkul = $_POST['kode_matkul'];
    $nama_matkul = $_POST['nama_matkul'];
    $sks = $_POST['sks'];

    if (empty($kode_matkul) || empty($nama_matkul) || empty($sks)) {
        $message = '<div class="alert alert-danger" role="alert">Semua kolom harus diisi!</div>';
    } elseif (!is_numeric($sks) || $sks <= 0) {
        $message = '<div class="alert alert-danger" role="alert">SKS harus berupa angka positif!</div>';
    } else {
        $stmt = mysqli_prepare($koneksi, "UPDATE mata_kuliah SET kode_matkul = ?, nama_matkul = ?, sks = ? WHERE id = ?");

        if ($stmt === false) {
            $message = '<div class="alert alert-danger" role="alert">Error preparing statement: ' . mysqli_error($koneksi) . '</div>';
        } else {
            mysqli_stmt_bind_param($stmt, "ssii", $kode_matkul, $nama_matkul, $sks, $id);

            if (mysqli_stmt_execute($stmt)) {
                $message = '<div class="alert alert-success" role="alert">Mata kuliah berhasil diperbarui!</div>';
            } else {
                $message = '<div class="alert alert-danger" role="alert">Error memperbarui mata kuliah: ' . mysqli_stmt_error($stmt) . '</div>';
            }
            mysqli_stmt_close($stmt);
        }
    }
}

$stmt_select = mysqli_prepare($koneksi, "SELECT * FROM mata_kuliah WHERE id = ?");

if ($stmt_select === false) {
    die("Error preparing select statement: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_param($stmt_select, "i", $id);
mysqli_stmt_execute($stmt_select);
$result_select = mysqli_stmt_get_result($stmt_select);
$data = mysqli_fetch_assoc($result_select);
mysqli_stmt_close($stmt_select);

if (!$data) {
    echo '<div class="container mt-4"><div class="alert alert-warning" role="alert">Mata kuliah tidak ditemukan!</div></div>';
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Mata Kuliah</title>
    <link rel="stylesheet" href="<?= base_url('bootstrap/css/bootstrap.min.css') ?>">
    <style>

        :root {
            --primary-color: #6a5acd;
            --text-color: #ffffff;
            --secondary: #e0e0e0;
            --dark-purple-hover: #403385;
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
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-group label {
            font-weight: bold;
            color: #343a40;
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 10px 15px;
        }
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(106, 90, 205, 0.25);
        }

        .action-btn {
            background-color: var(--primary-color) !important;
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
            background-color: #6c757d !important;
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
        <h2>Edit Mata Kuliah</h2>
        <p>Ubah detail mata kuliah di bawah ini.</p>
    </div>

    <div class="form-container">
        <?= $message; ?>
        <form id="formMatkul" action="" method="POST">
            <div class="mb-3">
                <label for="kode_matkul" class="form-label">Kode Mata Kuliah:</label>
                <input type="text" class="form-control" id="kode_matkul" name="kode_matkul" required value="<?= htmlspecialchars($data['kode_matkul']); ?>">
            </div>
            <div class="mb-3">
                <label for="nama_matkul" class="form-label">Nama Mata Kuliah:</label>
                <input type="text" class="form-control" id="nama_matkul" name="nama_matkul" required value="<?= htmlspecialchars($data['nama_matkul']); ?>">
            </div>
            <div class="mb-3">
                <label for="sks" class="form-label">Jumlah SKS:</label>
                <input type="number" class="form-control" id="sks" name="sks" required min="1" value="<?= htmlspecialchars($data['sks']); ?>">
            </div>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <button type="button" class="btn action-btn" onclick="konfirmasiEditMatkul()">Simpan Perubahan</button>
                <a href="<?= base_url('mata_kuliah/list.php') ?>" class="btn back-btn">Kembali ke Daftar Mata Kuliah</a>
            </div>
        </form>
    </div>
</div>

<script src="<?= base_url('bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script>
    function konfirmasiEditMatkul() {
        const kodeMatkul = document.getElementById('kode_matkul').value;
        const namaMatkul = document.getElementById('nama_matkul').value;
        const sks = document.getElementById('sks').value;

        if (kodeMatkul === '' || namaMatkul === '' || sks === '') {
            alert('Semua kolom harus diisi!');
            return;
        }

        if (isNaN(sks) || parseInt(sks) <= 0) {
            alert('SKS harus berupa angka positif!');
            return;
        }
        const isConfirmed = confirm("Anda yakin ingin menyimpan perubahan mata kuliah ini?");

        if (isConfirmed) {
            document.getElementById('formMatkul').submit();
        } else {
            alert("Perubahan mata kuliah dibatalkan.");
        }
    }
</script>
</body>
</html>