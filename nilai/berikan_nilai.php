<?php
session_start();
include "../koneksi.php";
include "../template.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'dosen') {
    header("Location: " . base_url('login.php'));
    exit;
}

$id_dosen = $_SESSION['id'];

if (!isset($_GET['id_tugas'])) {
    header("Location: " . base_url('nilai/dosen_nilai_tugas.php'));
    exit;
}

$id_tugas = mysqli_real_escape_string($koneksi, $_GET['id_tugas']);

$queryInfoTugas = mysqli_query($koneksi, "SELECT t.judul, t.deskripsi, t.deadline, t.file_tugas, mk.nama_matkul 
                                           FROM tugas t 
                                           JOIN mata_kuliah mk ON t.matkul_id = mk.id 
                                           WHERE t.id = '$id_tugas' AND t.dosen_id = '$id_dosen'");

$infoTugas = mysqli_fetch_assoc($queryInfoTugas);

if (!$infoTugas) {
    echo "<script>alert('Tugas tidak ditemukan atau Anda tidak memiliki akses ke tugas ini.'); window.location.href='" . base_url('nilai/dosen_nilai_tugas.php') . "';</script>";
    exit;
}

$queryPengumpulan = mysqli_query($koneksi, "SELECT p.id AS pengumpulan_id, u.nama_lengkap AS nama_mahasiswa, 
                                            p.file_jawaban, p.tanggal_kumpul, p.nilai, p.komentar,
                                            p.mahasiswa_id
                                            FROM pengumpulan p
                                            JOIN users u ON p.mahasiswa_id = u.id
                                            WHERE p.tugas_id = '$id_tugas'
                                            ORDER BY u.nama_lengkap ASC");

$queryMahasiswaBelumMengumpulkan = mysqli_query($koneksi, "
    SELECT u.id AS mahasiswa_id, u.nama_lengkap AS nama_mahasiswa
    FROM users u
    WHERE u.role = 'mahasiswa'
    AND u.id NOT IN (
        SELECT p.mahasiswa_id
        FROM pengumpulan p
        WHERE p.tugas_id = '$id_tugas'
    )
    ORDER BY u.nama_lengkap ASC
");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Lihat Pengumpulan: <?= htmlspecialchars($infoTugas['judul']); ?></title>
    <link rel="stylesheet" href="<?= base_url('bootstrap/css/bootstrap.min.css') ?>">
    <style>
        :root {
            --primary-color: #6a5acd;
            --text-color: #ffffff;
            --secondary: #e0e0e0;
            --dark-purple-hover: #403385;
            --danger-color: #dc3545;
            --success-color: #28a745;
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

        .card-tugas-info, .table-container {
            background-color: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .card-tugas-info h3 {
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        .card-tugas-info p {
            margin-bottom: 8px;
        }
        .card-tugas-info .file-link {
            display: inline-block;
            margin-top: 10px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        .card-tugas-info .file-link:hover {
            text-decoration: underline;
        }

        .table-custom {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table-custom thead th {
            background-color: var(--primary-color);
            color: var(--text-color);
            padding: 12px 15px;
            text-align: left;
            border-bottom: 2px solid var(--dark-purple-hover);
        }

        .table-custom tbody tr {
            border-bottom: 1px solid #ddd;
        }

        .table-custom tbody tr:nth-of-type(even) {
            background-color: #f9f9f9;
        }

        .table-custom tbody tr:hover {
            background-color: #f1f1f1;
        }

        .table-custom tbody td {
            padding: 12px 15px;
            vertical-align: middle;
        }

        .action-btn {
            background-color: var(--primary-color) !important;
            color: var(--text-color) !important;
            font-weight: 600 !important;
            border: none;
            padding: 6px 12px;
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

        .btn-danger-custom {
            background-color: var(--danger-color) !important;
            border-color: var(--danger-color) !important;
        }
        .btn-danger-custom:hover {
            background-color: #c82333 !important;
            border-color: #bd2130 !important;
        }

        .btn-success-custom {
            background-color: var(--success-color) !important;
            border-color: var(--success-color) !important;
        }
        .btn-success-custom:hover {
            background-color: #218838 !important;
            border-color: #1e7e34 !important;
        }

        .form-control-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            border-radius: 0.2rem;
            width: 80px;
        }
    </style>
</head>
<body>
    <?php include "../navbar.php"; ?>

    <div class="container mt-4">
        <div class="header-section">
            <h2>Pengumpulan Tugas: <?= htmlspecialchars($infoTugas['judul']); ?></h2>
            <p>Mata Kuliah: **<?= htmlspecialchars($infoTugas['nama_matkul']); ?>** | Deadline: **<?= date('d M Y H:i', strtotime($infoTugas['deadline'])); ?>**</p>
            <?php if (!empty($infoTugas['file_tugas'])): ?>
                <a href="<?= base_url('uploads/tugas/' . $infoTugas['file_tugas']) ?>" class="btn action-btn mt-2" download>Unduh Soal Tugas</a>
            <?php endif; ?>
        </div>

        <div class="card-tugas-info">
            <h3>Informasi Tugas</h3>
            <p><strong>Judul:</strong> <?= htmlspecialchars($infoTugas['judul']); ?></p>
            <p><strong>Deskripsi:</strong> <?= nl2br(htmlspecialchars($infoTugas['deskripsi'])); ?></p>
            <p><strong>Deadline:</strong> <?= date('d F Y, H:i', strtotime($infoTugas['deadline'])); ?> WIB</p>
        </div>

        <div class="table-container">
            <h3>Daftar Pengumpulan Mahasiswa</h3>
            <?php if (mysqli_num_rows($queryPengumpulan) > 0): ?>
                <div class="table-responsive">
                    <form action="proses_nilai.php" method="POST">
                        <input type="hidden" name="id_tugas" value="<?= $id_tugas; ?>">
                        <table class="table table-custom">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>Tanggal Kumpul</th>
                                    <th>Status</th>
                                    <th>File Jawaban</th>
                                    <th>Nilai</th>
                                    <th>Komentar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php while($pengumpulan = mysqli_fetch_assoc($queryPengumpulan)): ?>
                                    <?php
                                        $is_late = strtotime($pengumpulan['tanggal_kumpul']) > strtotime($infoTugas['deadline']);
                                        $status_text = $is_late ? '<span class="badge bg-danger">Terlambat</span>' : '<span class="badge bg-success">Tepat Waktu</span>';
                                    ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= htmlspecialchars($pengumpulan['nama_mahasiswa']); ?></td>
                                        <td><?= date('d M Y H:i', strtotime($pengumpulan['tanggal_kumpul'])); ?></td>
                                        <td><?= $status_text; ?></td>
                                        <td>
                                            <?php if (!empty($pengumpulan['file_jawaban'])): ?>
                                                <a href="<?= base_url('uploads/jawaban/' . $pengumpulan['file_jawaban']) ?>" class="btn action-btn btn-sm" download>Unduh File</a>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm" name="nilai[<?= $pengumpulan['pengumpulan_id']; ?>]" value="<?= htmlspecialchars($pengumpulan['nilai']); ?>" min="0" max="100" step="1">
                                        </td>
                                        <td>
                                            <textarea class="form-control form-control-sm" name="komentar[<?= $pengumpulan['pengumpulan_id']; ?>]" rows="2"><?= htmlspecialchars($pengumpulan['komentar']); ?></textarea>
                                        </td>
                                        <td>
                                            <button type="submit" name="submit_nilai" value="<?= $pengumpulan['pengumpulan_id']; ?>" class="btn action-btn btn-sm btn-success-custom">Simpan</button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </form>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center" role="alert">
                    Belum ada mahasiswa yang mengumpulkan tugas ini.
                </div>
            <?php endif; ?>

            <?php if (mysqli_num_rows($queryMahasiswaBelumMengumpulkan) > 0): ?>
                <h4 class="mt-5">Mahasiswa Belum Mengumpulkan:</h4>
                <ul class="list-group">
                    <?php while($mahasiswa = mysqli_fetch_assoc($queryMahasiswaBelumMengumpulkan)): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= htmlspecialchars($mahasiswa['nama_mahasiswa']); ?>
                            <span class="badge bg-secondary">Belum Mengumpulkan</span>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php endif; ?>

            <div class="mt-4 text-center">
                <a href="<?= base_url('nilai/dosen_nilai_tugas.php') ?>" class="btn action-btn btn-secondary">Kembali ke Daftar Tugas</a>
            </div>
        </div>
    </div>

    <script src="<?= base_url('bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>