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
$selected_matkul_id = isset($_GET['matkul_id']) ? (int)$_GET['matkul_id'] : 0;
$selected_dosen_id = isset($_GET['dosen_id']) ? (int)$_GET['dosen_id'] : 0;

$search_query_sql = "";
$matkul_filter_sql = "";
$dosen_filter_sql = "";

if (!empty($search_keyword)) {
    $search_query_sql = " AND (t.judul LIKE '%$search_keyword%' OR mk.nama_matkul LIKE '%$search_keyword%')";
}

if ($selected_matkul_id > 0) {
    $matkul_filter_sql = " AND t.matkul_id = '$selected_matkul_id'";
}

if ($selected_dosen_id > 0) {
    $dosen_filter_sql = " AND t.dosen_id = '$selected_dosen_id'";
}

$queryMataKuliahFilter = mysqli_query($koneksi, "SELECT id, nama_matkul FROM mata_kuliah ORDER BY nama_matkul ASC");

$queryDosenFilter = mysqli_query($koneksi, "SELECT id, nama_lengkap FROM users WHERE role = 'dosen' ORDER BY nama_lengkap ASC");

$queryTugas = mysqli_query($koneksi, "
    SELECT 
        t.id AS tugas_id, 
        t.judul, 
        t.deskripsi, 
        t.deadline, 
        t.file_tugas, 
        mk.nama_matkul, 
        u_dosen.nama_lengkap AS nama_dosen,
        p.id AS pengumpulan_id,
        p.file_jawaban,
        p.tanggal_kumpul,
        p.nilai,
        p.komentar
    FROM tugas t
    JOIN mata_kuliah mk ON t.matkul_id = mk.id
    JOIN users u_dosen ON t.dosen_id = u_dosen.id
    LEFT JOIN pengumpulan p ON t.id = p.tugas_id AND p.mahasiswa_id = '$id_mahasiswa'
    WHERE 1=1 " . $search_query_sql . $matkul_filter_sql . $dosen_filter_sql . "
    ORDER BY t.deadline ASC
");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Lihat Tugas</title>
    <link rel="stylesheet" href="<?= base_url('bootstrap/css/bootstrap.min.css') ?>">
    <style>
        :root {
            --primary-color: #6a5acd;
            --text-color: #ffffff;
            --secondary: #e0e0e0;
            --dark-purple-hover: #403385;
            --success-color: #28a745;
            --warning-color: #ffc107;
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

        .card-tugas {
            background-color: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .card-tugas h5 {
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        .card-tugas .badge {
            font-size: 0.8em;
            padding: 5px 10px;
            border-radius: 50px;
        }
        .card-tugas .deadline-warning {
            color: #dc3545;
            font-weight: bold;
        }
        .card-tugas .action-buttons a {
            margin-right: 10px;
            text-decoration: none;
            color: var(--primary-color);
            font-weight: 600;
        }
        .card-tugas .action-buttons a:hover {
            text-decoration: underline;
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
        .filter-form {
            margin-bottom: 20px;
            background-color: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body>
    <?php include "../navbar.php"; ?>

    <div class="container mt-4">
        <div class="header-section">
            <h2>Daftar Tugas Anda</h2>
            <p>Lihat semua tugas yang diberikan, unduh materi, dan periksa status pengumpulan Anda.</p>
        </div>

        <div class="filter-form">
            <form class="row g-3 align-items-center" method="GET" action="">
                <div class="col-md-4">
                    <label for="search" class="form-label visually-hidden">Cari Tugas</label>
                    <input class="form-control" type="search" placeholder="Cari Judul Tugas atau Mata Kuliah..." aria-label="Search" name="search" id="search" value="<?= htmlspecialchars($search_keyword); ?>">
                </div>
                <div class="col-md-4">
                    <label for="matkul_id" class="form-label visually-hidden">Filter Mata Kuliah</label>
                    <select class="form-select" id="matkul_id" name="matkul_id">
                        <option value="0">Semua Mata Kuliah</option>
                        <?php 
                        while($matkul = mysqli_fetch_assoc($queryMataKuliahFilter)): 
                        ?>
                            <option value="<?= $matkul['id']; ?>" <?= ($selected_matkul_id == $matkul['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($matkul['nama_matkul']); ?>
                            </option>
                        <?php 
                        endwhile; 
                        mysqli_data_seek($queryMataKuliahFilter, 0);
                        ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="dosen_id" class="form-label visually-hidden">Filter Dosen</label>
                    <select class="form-select" id="dosen_id" name="dosen_id">
                        <option value="0">Semua Dosen</option>
                        <?php 
                        while($dosen = mysqli_fetch_assoc($queryDosenFilter)): 
                        ?>
                            <option value="<?= $dosen['id']; ?>" <?= ($selected_dosen_id == $dosen['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($dosen['nama_lengkap']); ?>
                            </option>
                        <?php 
                        endwhile; 
                        mysqli_data_seek($queryDosenFilter, 0);
                        ?>
                    </select>
                </div>
                <div class="col-md-12 d-flex justify-content-end mt-3">
                    <button class="btn action-btn me-2" type="submit">Filter</button>
                    <?php if (!empty($search_keyword) || $selected_matkul_id > 0 || $selected_dosen_id > 0): ?>
                        <a href="<?= base_url('tugas_mahasiswa/lihat_tugas.php') ?>" class="btn btn-secondary">Reset Filter</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="row">
            <?php if (mysqli_num_rows($queryTugas) > 0): ?>
                <?php while($tugas = mysqli_fetch_assoc($queryTugas)): ?>
                    <div class="col-md-6 col-lg-4 d-flex align-items-stretch">
                        <div class="card-tugas flex-fill">
                            <h5><?= htmlspecialchars($tugas['judul']); ?></h5>
                            <p><strong>Mata Kuliah:</strong> <?= htmlspecialchars($tugas['nama_matkul']); ?></p>
                            <p><strong>Dosen:</strong> <?= htmlspecialchars($tugas['nama_dosen']); ?></p>
                            <p><strong>Deadline:</strong> 
                                <?php
                                $deadline = strtotime($tugas['deadline']);
                                $now = time();
                                echo date('d M Y H:i', $deadline);
                                if ($now > $deadline && is_null($tugas['pengumpulan_id'])) {
                                    echo ' <span class="badge bg-danger">Terlewat</span>';
                                } elseif ($now <= $deadline && ($deadline - $now) < (24 * 3600) && is_null($tugas['pengumpulan_id'])) {
                                    echo ' <span class="badge bg-warning text-dark">Mendekati Deadline!</span>';
                                }
                                ?>
                            </p>
                            <p><strong>Status Pengumpulan:</strong>
                                <?php if (!is_null($tugas['pengumpulan_id'])): ?>
                                    <span class="badge bg-success">Sudah Dikumpulkan</span>
                                    <?php if ($tugas['nilai'] !== null): ?>
                                        <span class="badge bg-info">Nilai: <?= htmlspecialchars($tugas['nilai']); ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Belum Dinilai</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge bg-danger">Belum Dikumpulkan</span>
                                <?php endif; ?>
                            </p>
                            
                            <div class="action-buttons mt-3">
                                <?php if (!empty($tugas['file_tugas'])): ?>
                                    <a href="<?= base_url('tugas/file_tugas/' . $tugas['file_tugas']) ?>" class="btn action-btn btn-sm" download>Unduh Soal</a>
                                <?php endif; ?>
                                <a href="<?= base_url('tugas_mahasiswa/upload_tugas.php?id_tugas=' . $tugas['tugas_id']) ?>" class="btn action-btn btn-sm">
                                    <?= !is_null($tugas['pengumpulan_id']) ? 'Ubah Pengumpulan' : 'Unggah Jawaban'; ?>
                                </a>
                                <?php if (!is_null($tugas['pengumpulan_id'])): ?>
                                    <a href="<?= base_url('tugas_mahasiswa/riwayat_nilai.php?id_tugas=' . $tugas['tugas_id']) ?>" class="btn btn-info btn-sm text-white">Lihat Detail</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center" role="alert">
                        Tidak ada tugas yang ditemukan untuk kriteria ini.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="<?= base_url('bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>