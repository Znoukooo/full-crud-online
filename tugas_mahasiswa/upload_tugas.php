<?php
session_start();
include "../koneksi.php";
include "../template.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: " . base_url('login.php'));
    exit;
}

$id_mahasiswa = $_SESSION['id'];

if (!isset($_GET['id_tugas'])) {
    header("Location: " . base_url('tugas_mahasiswa/lihat_tugas.php'));
    exit;
}

$id_tugas = mysqli_real_escape_string($koneksi, $_GET['id_tugas']);

$queryInfoTugas = mysqli_query($koneksi, "SELECT t.id, t.judul, t.deskripsi, t.deadline, t.file_tugas, mk.nama_matkul 
                                           FROM tugas t 
                                           JOIN mata_kuliah mk ON t.matkul_id = mk.id 
                                           WHERE t.id = '$id_tugas'");
$infoTugas = mysqli_fetch_assoc($queryInfoTugas);

if (!$infoTugas) {
    echo "<script>alert('Tugas tidak ditemukan.'); window.location.href='" . base_url('tugas_mahasiswa/lihat_tugas.php') . "';</script>";
    exit;
}

$queryPengumpulanSaatIni = mysqli_query($koneksi, "SELECT * FROM pengumpulan WHERE tugas_id = '$id_tugas' AND mahasiswa_id = '$id_mahasiswa'");
$pengumpulanSaatIni = mysqli_fetch_assoc($queryPengumpulanSaatIni);

$form_action_text = $pengumpulanSaatIni ? "Ubah Jawaban" : "Unggah Jawaban";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $form_action_text; ?>: <?= htmlspecialchars($infoTugas['judul']); ?></title>
    <link rel="stylesheet" href="<?= base_url('bootstrap/css/bootstrap.min.css') ?>">
    <style>
        :root {
            --primary-color: #6a5acd;
            --text-color: #ffffff;
            --secondary: #e0e0e0;
            --dark-purple-hover: #403385;
            --danger-color: #dc3545;
        }

        body {
            background-color: #f8f9fa;
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
            margin-bottom: 10px;
        }
        .header-section p {
            color: var(--secondary);
            font-size: 0.9em;
        }

        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        .form-container h3 {
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .action-btn {
            background-color: var(--primary-color) !important;
            color: var(--text-color) !important;
            font-weight: 600 !important;
            border: none;
            padding: 8px 16px;
            border-radius: 50px;
            transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
            text-decoration: none;
            display: inline-block;
            margin: 2px;
        }
        .action-btn:hover {
            background-color: var(--dark-purple-hover) !important;
            color: white !important;
        }
        .btn-cancel {
            background-color: #6c757d !important;
        }
        .btn-cancel:hover {
            background-color: #5a6268 !important;
        }
    </style>
</head>
<body>
    <?php include "../navbar.php"; ?>

    <div class="container mt-4">
        <div class="header-section">
            <h2><?= $form_action_text; ?>: <?= htmlspecialchars($infoTugas['judul']); ?></h2>
            <p>Mata Kuliah: **<?= htmlspecialchars($infoTugas['nama_matkul']); ?>** | Deadline: **<?= date('d M Y H:i', strtotime($infoTugas['deadline'])); ?>**</p>
            <?php if (!empty($infoTugas['file_tugas'])): ?>
                <a href="<?= base_url('/tugas/file_tugas/' . $infoTugas['file_tugas']) ?>" class="btn action-btn mt-2" download>Unduh Soal Tugas</a>
            <?php endif; ?>
        </div>

        <div class="form-container">
            <h3>Detail Tugas</h3>
            <p><strong>Judul:</strong> <?= htmlspecialchars($infoTugas['judul']); ?></p>
            <p><strong>Deskripsi:</strong> <?= nl2br(htmlspecialchars($infoTugas['deskripsi'])); ?></p>
            <p><strong>Deadline:</strong> <?= date('d F Y, H:i', strtotime($infoTugas['deadline'])); ?> WIB</p>
            
            <hr>

            <h3>Form Unggah Jawaban</h3>
            <form action="<?= base_url('tugas_mahasiswa/Controller_upload.php') ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_tugas" value="<?= $id_tugas; ?>">
                <?php if ($pengumpulanSaatIni): ?>
                    <input type="hidden" name="pengumpulan_id" value="<?= $pengumpulanSaatIni['id']; ?>">
                    <div class="mb-3">
                        <label for="file_lama" class="form-label">File Jawaban Saat Ini:</label><br>
                        <?php if (!empty($pengumpulanSaatIni['file_jawaban'])): ?>
                            <p><a href="<?= base_url('tugas/jawaban/' . $pengumpulanSaatIni['file_jawaban']) ?>" download><?= htmlspecialchars($pengumpulanSaatIni['file_jawaban']); ?></a></p>
                        <?php else: ?>
                            <p>Belum ada file yang diunggah.</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label for="file_jawaban" class="form-label">Pilih File Jawaban Baru (PDF, DOCX, ZIP, Max 10MB)</label>
                    <input class="form-control" type="file" id="file_jawaban" name="file_jawaban" accept=".pdf,.doc,.docx,.zip">
                    <div class="form-text">Unggah file jawaban Anda. Jika mengunggah ulang, file lama akan diganti.</div>
                </div>
                
                <button type="submit" class="btn action-btn"><?= $form_action_text; ?></button>
                <a href="<?= base_url('tugas_mahasiswa/lihat_tugas.php') ?>" class="btn action-btn btn-cancel">Batal</a>
            </form>
        </div>
    </div>

    <script src="<?= base_url('bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>