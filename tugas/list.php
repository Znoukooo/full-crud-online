<?php
session_start();
include "../koneksi.php";
include "../template.php";

if (!isset($_SESSION['role'])) {
    header("Location: " . base_url('login.php'));
    exit;
}

function getTasks($koneksi, $role, $user_id, $current_limit, $search_keyword) {
    $search_query_sql = "";
    if (!empty($search_keyword)) {
        $search_query_sql = " AND (t.judul LIKE '%$search_keyword%' OR mk.nama_matkul LIKE '%$search_keyword%')";
    }

    $base_query = "";
    if ($role === 'dosen') {
        $base_query = "SELECT t.id AS id_tugas, t.judul AS judul_tugas, t.deskripsi AS deskripsi_tugas,
                              t.deadline AS due_date, t.file_tugas,
                              mk.nama_matkul
                         FROM tugas t
                         JOIN mata_kuliah mk ON t.matkul_id = mk.id
                         WHERE t.dosen_id = '$user_id'";
    } elseif ($role === 'mahasiswa') {
        $base_query = "SELECT t.id AS id_tugas, t.judul AS judul_tugas, t.deskripsi AS deskripsi_tugas,
                              t.deadline AS due_date, t.file_tugas,
                              mk.nama_matkul, u.nama_lengkap AS nama_dosen
                         FROM tugas t
                         JOIN mata_kuliah mk ON t.matkul_id = mk.id
                         JOIN users u ON t.dosen_id = u.id";
    } elseif ($role === 'admin') {
        $base_query = "SELECT t.id AS id_tugas, t.judul AS judul_tugas, t.deskripsi AS deskripsi_tugas,
                              t.deadline AS due_date, t.file_tugas,
                              mk.nama_matkul, u.nama_lengkap AS nama_dosen
                         FROM tugas t
                         JOIN mata_kuliah mk ON t.matkul_id = mk.id
                         JOIN users u ON t.dosen_id = u.id";
    }

    $final_query_with_search = $base_query . $search_query_sql;
    
    $totalTugasQuery = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM (" . $final_query_with_search . ") AS subquery");
    $totalTugasResult = mysqli_fetch_assoc($totalTugasQuery);
    $total_tugas = $totalTugasResult['total'];

    $final_display_query = $final_query_with_search . " ORDER BY t.deadline ASC LIMIT " . $current_limit;
    $queryTugas = mysqli_query($koneksi, $final_display_query);

    return ['query_result' => $queryTugas, 'total_tasks' => $total_tugas];
}

$role = $_SESSION['role'];
$user_id = $_SESSION['id'];
$user = $_SESSION['nama_lengkap'];

$default_limit = 5;
$current_limit = isset($_GET['limit']) ? (int)$_GET['limit'] : $default_limit;
$search_keyword = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';

$task_data = getTasks($koneksi, $role, $user_id, $current_limit, $search_keyword);
$queryTugas = $task_data['query_result'];
$total_tugas = $task_data['total_tasks'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Daftar Tugas</title>
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

        .table-container {
            background-color: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
        .action-btn-tugas {
            background-color: var(--text-color) !important;
            color: var(--primary-color) !important;
            font-weight: 600 !important;
            border: none;
            padding: 6px 12px;
            border-radius: 50px;
            transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
            text-decoration: none;
            display: inline-block;
            margin: 2px;
        }
        .action-btn-tugas:hover {
            background-color: var(--dark-purple-hover) !important;
            color: white !important;
        }
        .btn-danger {
            background-color: #dc3545 !important;
            color: white !important;
            font-weight: 600 !important;
            border: none;
            padding: 6px 12px;
            border-radius: 50px;
            transition: background-color 0.2s ease-in-out;
            text-decoration: none;
            display: inline-block;
            margin: 2px;
        }
        .btn-danger:hover {
            background-color: #c82333 !important;
        }

        .search-form {
            margin-bottom: 20px;
        }
        .pagination-container {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<?php include "../navbar.php"; ?>

<div class="container mt-4">
    <div class="header-section">
        <h2>Daftar Tugas Saya</h2>
        <p>Lihat semua tugas yang tersedia atau yang Anda berikan.</p>
        <?php if ($role === 'dosen'): ?>
            <a href="<?= base_url('tugas/tambah.php') ?>" class="btn action-btn-tugas mt-3">Buat Tugas Baru</a>
        <?php endif; ?>
    </div>

    <div class="table-container">
        <form class="search-form d-flex mb-3" method="GET" action="">
            <input class="form-control me-2" type="search" placeholder="Cari Judul Tugas atau Mata Kuliah..." aria-label="Search" name="search" value="<?= htmlspecialchars($search_keyword); ?>">
            <button class="btn action-btn" type="submit">Cari</button>
            <?php if (!empty($search_keyword)): ?>
                <a href="<?= base_url('tugas/list.php') ?>" class="btn btn-secondary ms-2">Reset</a>
            <?php endif; ?>
        </form>

        <?php if (mysqli_num_rows($queryTugas) > 0): ?>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul Tugas</th>
                            <th>Mata Kuliah</th>
                            <?php if ($role === 'admin' || $role === 'mahasiswa'): ?>
                                <th>Dosen</th>
                            <?php endif; ?>
                            <th>Deadline</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php while($tugas = mysqli_fetch_assoc($queryTugas)): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($tugas['judul_tugas']); ?></td>
                                <td><?= htmlspecialchars($tugas['nama_matkul']); ?></td>
                                <?php if ($role === 'admin' || $role === 'mahasiswa'): ?>
                                    <td><?= htmlspecialchars($tugas['nama_dosen']); ?></td>
                                <?php endif; ?>
                                <td><?= date('d M Y', strtotime($tugas['due_date'])); ?></td>
                                <td>
                                    <a href="<?= base_url('tugas/detail.php?id=' . $tugas['id_tugas']) ?>" class="btn action-btn">Lihat Detail</a>
                                    <?php if ($role === 'dosen' || $role === 'admin'): ?>
                                        <a href="<?= base_url('tugas/edit.php?id=' . $tugas['id_tugas']) ?>" class="btn action-btn">Edit</a>
                                        <a href="<?= base_url('tugas/hapus.php?id=' . $tugas['id_tugas']) ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus tugas ini?');">Hapus</a>
                                    <?php endif; ?>
                                    <?php if ($role === 'mahasiswa'): ?>
                                        <a href="<?= base_url('mahasiswa/upload_tugas.php?id_tugas=' . $tugas['id_tugas']) ?>" class="btn action-btn">Kumpulkan</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($current_limit < $total_tugas): ?>
                <div class="pagination-container">
                    <a href="?limit=<?= $current_limit + $default_limit ?><?= !empty($search_keyword) ? '&search=' . urlencode($search_keyword) : '' ?>" class="btn action-btn">Tampilkan Lebih Banyak</a>
                </div>
            <?php elseif ($total_tugas > 0): ?>
                <div class="alert alert-secondary text-center mt-3" role="alert">
                    Semua tugas telah ditampilkan.
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="alert alert-info text-center" role="alert">
                Tidak ada tugas yang ditemukan.
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="<?= base_url('bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>