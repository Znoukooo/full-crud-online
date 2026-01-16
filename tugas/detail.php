<?php
session_start();
include "../koneksi.php";
include "../template.php";

if (!isset($_SESSION['role'])) {
    header("Location: " . base_url('login.php'));
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: " . base_url('tugas/index.php'));
    exit;
}

$task_id = mysqli_real_escape_string($koneksi, $_GET['id']);
$role = $_SESSION['role'];
$user_id = $_SESSION['id'];

$query_detail_task = "";

if ($role === 'dosen') {
    $query_detail_task = "SELECT t.id AS id_tugas, t.judul AS judul_tugas, t.deskripsi AS deskripsi_tugas,
                              t.deadline AS due_date, t.file_tugas, mk.nama_matkul
                          FROM tugas t
                          JOIN mata_kuliah mk ON t.matkul_id = mk.id
                          WHERE t.id = '$task_id' AND t.dosen_id = '$user_id'";
} elseif ($role === 'mahasiswa') {
    $query_detail_task = "SELECT t.id AS id_tugas, t.judul AS judul_tugas, t.deskripsi AS deskripsi_tugas,
                              t.deadline AS due_date, t.file_tugas, mk.nama_matkul
                          FROM tugas t
                          JOIN mata_kuliah mk ON t.matkul_id = mk.id
                          JOIN krs k ON mk.id = k.id_matkul_krs
                          WHERE t.id = '$task_id' AND k.id_mahasiswa_krs = '$user_id'";
} elseif ($role === 'admin') {
    $query_detail_task = "SELECT t.id AS id_tugas, t.judul AS judul_tugas, t.deskripsi AS deskripsi_tugas,
                              t.deadline AS due_date, t.file_tugas,
                              mk.nama_matkul, u.nama_lengkap AS nama_dosen
                          FROM tugas t
                          JOIN mata_kuliah mk ON t.matkul_id = mk.id
                          JOIN users u ON t.dosen_id = u.id
                          WHERE t.id = '$task_id'";
}

$result_detail_task = mysqli_query($koneksi, $query_detail_task);
$detail_tugas = mysqli_fetch_assoc($result_detail_task);

if (!$detail_tugas) {
    echo "<script>alert('Tugas tidak ditemukan atau Anda tidak memiliki akses.'); window.location.href='" . base_url('tugas/index.php') . "';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Detail Tugas: <?= htmlspecialchars($detail_tugas['judul_tugas']); ?></title>
    <link rel="stylesheet" href="<?= base_url('bootstrap/css/bootstrap.min.css') ?>">
    <style>
        :root {
            --primary-color: #6a5acd;
            --text-color: #ffffff;
            --secondary: #e0e0e0;
            --dark-purple-hover: #403385;
            --light-blue: #e0f2f7;
            --dark-blue: #0288d1;
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

        .detail-card {
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .detail-item {
            margin-bottom: 15px;
        }
        .detail-item strong {
            color: var(--primary-color);
            display: block;
            margin-bottom: 5px;
        }
        .detail-item p {
            margin-bottom: 0;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .action-btn {
            background-color: var(--primary-color) !important;
            color: var(--text-color) !important;
            font-weight: 600 !important;
            border: none;
            padding: 8px 20px;
            border-radius: 50px;
            transition: background-color 0.2s ease-in-out;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
        .action-btn:hover {
            background-color: var(--dark-purple-hover) !important;
            color: white !important;
        }
        .btn-danger {
            background-color: #dc3545 !important;
            color: white !important;
            font-weight: 600 !important;
            border: none;
            padding: 8px 20px;
            border-radius: 50px;
            transition: background-color 0.2s ease-in-out;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
        .btn-danger:hover {
            background-color: #c82333 !important;
        }
        .btn-success {
            background-color: #28a745 !important;
            color: white !important;
            font-weight: 600 !important;
            border: none;
            padding: 8px 20px;
            border-radius: 50px;
            transition: background-color 0.2s ease-in-out;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
        .btn-success:hover {
            background-color: #218838 !important;
        }

        .file-download-link {
            display: inline-block;
            margin-top: 10px;
            background-color: var(--light-blue);
            color: var(--dark-blue);
            padding: 8px 15px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
        }
        .file-download-link:hover {
            background-color: var(--dark-blue);
            color: var(--text-color);
        }
    </style>
</head>
<body>
<?php include "../navbar.php"; ?>

<div class="container mt-4">
    <div class="header-section">
        <h2>Detail Tugas</h2>
        <p>Informasi lengkap mengenai tugas ini.</p>
    </div>

    <div class="detail-card">
        <div class="detail-item">
            <strong>Judul Tugas:</strong>
            <p><?= htmlspecialchars($detail_tugas['judul_tugas']); ?></p>
        </div>
        <div class="detail-item">
            <strong>Mata Kuliah:</strong>
            <p><?= htmlspecialchars($detail_tugas['nama_matkul']); ?></p>
        </div>
        <?php if ($role === 'admin' && isset($detail_tugas['nama_dosen'])): ?>
            <div class="detail-item">
                <strong>Dosen Pengampu:</strong>
                <p><?= htmlspecialchars($detail_tugas['nama_dosen']); ?></p>
            </div>
        <?php endif; ?>
        <div class="detail-item">
            <strong>Deskripsi:</strong>
            <p><?= nl2br(htmlspecialchars($detail_tugas['deskripsi_tugas'])); ?></p>
        </div>
        <div class="detail-item">
            <strong>Batas Waktu Pengumpulan (Deadline):</strong>
            <p><?= date('d M Y H:i', strtotime($detail_tugas['due_date'])); ?></p>
        </div>
        <div class="detail-item">
            <strong>File Tugas:</strong>
            <?php if (!empty($detail_tugas['file_tugas'])): ?>
                <a href="<?= base_url('uploads/tugas/' . $detail_tugas['file_tugas']) ?>" target="_blank" class="file-download-link">
                    Unduh File Tugas (<?= htmlspecialchars($detail_tugas['file_tugas']); ?>)
                </a>
            <?php else: ?>
                <p>Tidak ada file tugas terlampir.</p>
            <?php endif; ?>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="<?= base_url('tugas/list.php') ?>" class="btn btn-secondary action-btn">Kembali</a>
            <?php if ($role === 'dosen' || $role === 'admin'): ?>
                <a href="<?= base_url('tugas/edit.php?id=' . $detail_tugas['id_tugas']) ?>" class="btn action-btn">Edit</a>
                <a href="<?= base_url('tugas/hapus.php?id=' . $detail_tugas['id_tugas']) ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus tugas ini?');">Hapus</a>
            <?php endif; ?>
            <?php if ($role === 'mahasiswa'): ?>
                <a href="<?= base_url('pengumpulan/upload.php?id_tugas=' . $detail_tugas['id_tugas']) ?>" class="btn btn-success">Kumpulkan Tugas</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="<?= base_url('bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>