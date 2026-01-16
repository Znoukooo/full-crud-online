<?php
session_start();
include "../koneksi.php";
include "../template.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: " . base_url('login.php'));
    exit;
}

$id_mahasiswa = $_SESSION['id'];
$search_keyword = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$search_query_sql = "";

if (!empty($search_keyword)) {
    $search_query_sql = " AND (t.judul LIKE '%$search_keyword%' OR mk.nama_matkul LIKE '%$search_keyword%')";
}

$queryRiwayat = mysqli_query($koneksi, "
    SELECT 
        p.id AS pengumpulan_id,
        t.judul, 
        t.deskripsi, 
        t.deadline, 
        t.file_tugas, 
        mk.nama_matkul, 
        u_dosen.nama_lengkap AS nama_dosen,
        p.file_jawaban,
        p.tanggal_kumpul,
        p.nilai,
        p.komentar
    FROM pengumpulan p
    JOIN tugas t ON p.tugas_id = t.id
    JOIN mata_kuliah mk ON t.matkul_id = mk.id
    JOIN users u_dosen ON t.dosen_id = u_dosen.id
    WHERE p.mahasiswa_id = '$id_mahasiswa' " . $search_query_sql . "
    ORDER BY p.tanggal_kumpul DESC
");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Riwayat dan Nilai Tugas</title>
    <link rel="stylesheet" href="<?= base_url('bootstrap/css/bootstrap.min.css') ?>">
    <style>
        :root {
            --primary-color: #6a5acd;
            --text-color: #ffffff;
            --secondary: #e0e0e0;
            --dark-purple-hover: #403385;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
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

        .table-container {
            background-color: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
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
        .search-form {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include "../navbar.php"; ?>

    <div class="container mt-4">
        <div class="header-section">
            <h2>Riwayat Pengumpulan & Nilai Tugas</h2>
            <p>Lihat semua tugas yang telah Anda kumpulkan, beserta nilai dan komentar dari dosen.</p>
        </div>

        <form class="search-form d-flex mb-3" method="GET" action="">
            <input class="form-control me-2" type="search" placeholder="Cari Judul Tugas atau Mata Kuliah..." aria-label="Search" name="search" value="<?= htmlspecialchars($search_keyword); ?>">
            <button class="btn action-btn" type="submit">Cari</button>
            <?php if (!empty($search_keyword)): ?>
                <a href="<?= base_url('tugas_mahasiswa/riwayat_nilai.php') ?>" class="btn btn-secondary ms-2">Reset</a>
            <?php endif; ?>
        </form>

        <div class="table-container">
            <?php if (mysqli_num_rows($queryRiwayat) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul Tugas</th>
                                <th>Mata Kuliah</th>
                                <th>Dosen</th>
                                <th>Tanggal Kumpul</th>
                                <th>Nilai</th>
                                <th>Komentar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php while($riwayat = mysqli_fetch_assoc($queryRiwayat)): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($riwayat['judul']); ?></td>
                                    <td><?= htmlspecialchars($riwayat['nama_matkul']); ?></td>
                                    <td><?= htmlspecialchars($riwayat['nama_dosen']); ?></td>
                                    <td><?= date('d M Y H:i', strtotime($riwayat['tanggal_kumpul'])); ?></td>
                                    <td>
                                        <?php if ($riwayat['nilai'] !== null): ?>
                                            <span class="badge bg-primary fs-6"><?= htmlspecialchars($riwayat['nilai']); ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Belum Dinilai</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= !empty($riwayat['komentar']) ? nl2br(htmlspecialchars($riwayat['komentar'])) : '-'; ?></td>
                                    <td>
                                        <?php if (!empty($riwayat['file_jawaban'])): ?>
                                            <a href="<?= base_url('tugas/jawaban/' . $riwayat['file_jawaban']) ?>" class="btn action-btn btn-sm" download>Unduh Jawaban</a>
                                        <?php endif; ?>
                                        <?php if (!empty($riwayat['file_tugas'])): ?>
                                            <a href="<?= base_url('tugas/file_tugas/' . $riwayat['file_tugas']) ?>" class="btn action-btn btn-sm" download>Unduh Soal</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center" role="alert">
                    Anda belum mengumpulkan tugas apa pun.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="<?= base_url('bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>