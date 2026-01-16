<?php
session_start();
include "../koneksi.php";
include "../template.php";

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'dosen')) {
    header("Location: " . base_url('login.php'));
    exit;
}

$role = $_SESSION['role'];
$user = $_SESSION['nama_lengkap'];


$default_limit = 5; 
$current_limit = isset($_GET['limit']) ? (int)$_GET['limit'] : $default_limit;

$search_keyword = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$search_query_sql = "";

if (!empty($search_keyword)) {
    $search_query_sql = " WHERE nama_matkul LIKE '%$search_keyword%' OR kode_matkul LIKE '%$search_keyword%'";
}

$totalMatkulQuery = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM mata_kuliah" . $search_query_sql);
$totalMatkulResult = mysqli_fetch_assoc($totalMatkulQuery);
$total_mata_kuliah = $totalMatkulResult['total'];

$queryMatkul = mysqli_query($koneksi, "SELECT * FROM mata_kuliah" . $search_query_sql . " ORDER BY nama_matkul ASC LIMIT " . $current_limit);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Daftar Mata Kuliah</title>
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
        .action-btn-tambah {
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
        .action-btn-tambah:hover {
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
        <h2>Daftar Mata Kuliah</h2>
        <p>Kelola semua mata kuliah yang tersedia.</p>
        <?php if ($role === 'admin'): ?>
            <a href="<?= base_url('mata_kuliah/tambah.php') ?>" class="btn action-btn-tambah mt-3">Tambah Mata Kuliah Baru</a>
        <?php endif; ?>
    </div>

    <div class="table-container">
        <form class="search-form d-flex mb-3" method="GET" action="">
            <input class="form-control me-2" type="search" placeholder="Cari Mata Kuliah..." aria-label="Search" name="search" value="<?= htmlspecialchars($search_keyword); ?>">
            <button class="btn action-btn" type="submit">Cari</button>
            <?php if (!empty($search_keyword)): ?>
                <a href="<?= base_url('mata_kuliah/list.php') ?>" class="btn btn-secondary ms-2">Reset</a>
            <?php endif; ?>
        </form>

        <?php if (mysqli_num_rows($queryMatkul) > 0): ?>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Mata Kuliah</th>
                            <th>Kode Mata Kuliah</th>
                            <th>SKS</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php while($matkul = mysqli_fetch_assoc($queryMatkul)): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($matkul['nama_matkul']); ?></td>
                                <td><?= htmlspecialchars($matkul['kode_matkul']); ?></td>
                                <td><?= htmlspecialchars($matkul['sks']); ?></td>
                                <td>
                                   
                                    <?php if ($role === 'admin'): ?>
                                        <a href="<?= base_url('mata_kuliah/edit_matkul.php?id=' . $matkul['id']) ?>" class="btn action-btn">Edit</a>
                                        <a href="<?= base_url('mata_kuliah/hapus.php?id=' . $matkul['id']) ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus mata kuliah ini?');">Hapus</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($current_limit < $total_mata_kuliah): ?>
                <div class="pagination-container">
                    <a href="?limit=<?= $current_limit + $default_limit ?><?= !empty($search_keyword) ? '&search=' . urlencode($search_keyword) : '' ?>" class="btn action-btn">Tampilkan Lebih Banyak</a>
                </div>
            <?php elseif ($total_mata_kuliah > 0): ?>
                <div class="alert alert-secondary text-center mt-3" role="alert">
                    Semua mata kuliah telah ditampilkan.
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="alert alert-info text-center" role="alert">
                Tidak ada mata kuliah yang ditemukan.
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="<?= base_url('bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>