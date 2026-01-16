    <?php
    session_start();
    include "../koneksi.php";
    include "../template.php";

    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'dosen') {
        header("Location: " . base_url('login.php'));
        exit;
    }

    $role = $_SESSION['role'];
    $user = $_SESSION['nama_lengkap'];
    $id_dosen = $_SESSION['id'];

    $default_limit = 5;
    $current_limit = isset($_GET['limit']) ? (int)$_GET['limit'] : $default_limit;

    $search_keyword = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
    $search_query_sql = "";

    if (!empty($search_keyword)) {
        $search_query_sql = " AND (t.judul LIKE '%$search_keyword%' OR mk.nama_matkul LIKE '%$search_keyword%')";
    }

    $queryTotalTugas = mysqli_query($koneksi, "SELECT COUNT(t.id) AS total FROM tugas t JOIN mata_kuliah mk ON t.matkul_id = mk.id WHERE t.dosen_id = '$id_dosen'" . $search_query_sql);
    $totalTugasResult = mysqli_fetch_assoc($queryTotalTugas);
    $total_tugas_dosen = $totalTugasResult['total'];

    $queryTugasDosen = mysqli_query($koneksi, "SELECT t.id, t.judul, t.deadline, mk.nama_matkul FROM tugas t JOIN mata_kuliah mk ON t.matkul_id = mk.id WHERE t.dosen_id = '$id_dosen'" . $search_query_sql . " ORDER BY t.deadline DESC LIMIT " . $current_limit);

    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Nilai Tugas Mahasiswa</title>
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
            <h2>Daftar Tugas untuk Penilaian</h2>
            <p>Pilih tugas untuk melihat pengumpulan dan memberikan nilai.</p>
        </div>

        <div class="table-container">
            <form class="search-form d-flex mb-3" method="GET" action="">
                <input class="form-control me-2" type="search" placeholder="Cari Judul Tugas atau Mata Kuliah..." aria-label="Search" name="search" value="<?= htmlspecialchars($search_keyword); ?>">
                <button class="btn action-btn" type="submit">Cari</button>
                <?php if (!empty($search_keyword)): ?>
                    <a href="<?= base_url('nilai/dosen_nilai_tugas.php') ?>" class="btn btn-secondary ms-2">Reset</a>
                <?php endif; ?>
            </form>

            <?php if (mysqli_num_rows($queryTugasDosen) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul Tugas</th>
                                <th>Mata Kuliah</th>
                                <th>Deadline</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php while($tugas = mysqli_fetch_assoc($queryTugasDosen)): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($tugas['judul']); ?></td>
                                    <td><?= htmlspecialchars($tugas['nama_matkul']); ?></td>
                                    <td><?= date('d M Y H:i', strtotime($tugas['deadline'])); ?></td>
                                    <td>
                                        <a href="<?= base_url('nilai/berikan_nilai.php?id_tugas=' . $tugas['id']) ?>" class="btn action-btn">Lihat Pengumpulan</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($current_limit < $total_tugas_dosen): ?>
                    <div class="pagination-container">
                        <a href="?limit=<?= $current_limit + $default_limit ?><?= !empty($search_keyword) ? '&search=' . urlencode($search_keyword) : '' ?>" class="btn action-btn">Tampilkan Lebih Banyak</a>
                    </div>
                <?php elseif ($total_tugas_dosen > 0): ?>
                    <div class="alert alert-secondary text-center mt-3" role="alert">
                        Semua tugas telah ditampilkan.
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="alert alert-info text-center" role="alert">
                    Anda belum membuat tugas apa pun.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="<?= base_url('bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    </body>
    </html>